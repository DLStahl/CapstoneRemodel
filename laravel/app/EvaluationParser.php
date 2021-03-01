<?php
/**
 * Created by PhpStorm.
 * User: shaw
 * Date: 11/18/18
 * Time: 1:38 PM
 */

namespace App;

use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use App\Constant;
use App\EvaluateData;
use App\Resident;
use App\Option;
use App\Assignment;
use App\Http\Controllers\MedhubController;

class user{
    //array 0:LastName, 1:MiddleName, 2:FirstName
    public $namefl;

    //resident:0, attending:1
    public $occupation;
    public $startTime;
    public $endTime;
    public $diff;
    public function __construct($namefl,$occupation,$startTime,$endTime,$diff){

        $this->namefl=$namefl;
        $this->occupation=$occupation;
        $this->startTime=$startTime;
        $this->endTime=$endTime;
        $this->diff=$diff;
    }

}

class EvaluationParser extends Model
{
    protected $filepath;
    protected $date;
    protected $fileExists = true;

    private static function getDate($line)
    {

        $month = intval(substr($line, 0, 2));
        $day = intval(substr($line, 3, 2));
        $year = intval(substr($line, 6));

        return ($year."-".$month."-".$day);
    }

    private function getParticipants($line){

        $participants=array();
        $line=$line."\n";

        while ($line!=false) {

            $ep = stripos(substr($line, 0), ":");
            $name=self::getName(substr($line, 0, $ep));
            $name = array_reverse($name);
            $namefml = implode(" ", $name);
            $namefml = preg_replace( "/\s(?=\s)/","\\1", $namefml);
            echo 'name fml:'.$namefml."\n";

            $namefl = explode(" ", $namefml);
            $name_first = $namefl[0];
            if (sizeof($namefl) <= 3) {
                $name_last = end($namefl);
            } else {
                $name_last = $namefl[2];
            }

            $namefl = $name_first." ".$name_last;

            $ep = stripos(substr($line, 0), "\n");
            $line=substr($line,$ep+1);


            while(substr($line,0,1)==" "){
                $ep = stripos($line, "\n");

                $tmp=trim(substr($line, 0, $ep)," +");
                $occupation=self::getOccupation(substr($tmp,0,stripos($tmp," ")));

                preg_match("/from (.*) to/s",$tmp,$match);
                $startTime=self::getTime($match[1], $this->date);
                preg_match("/to (.*)/s",$tmp,$match);
                $endTime=self::getTime($match[1],$this->date);
                $diff = strtotime($endTime) - strtotime($startTime);
                $diff = $diff/60;

                $obj=new user($namefl,$occupation,$startTime,$endTime,$diff);
                array_push($participants,$obj);
                $line=substr($line,$ep+1);
            }
        }
        return $participants;
    }

    private static function getName($line){
        $name=substr($line,0,stripos(substr($line, 0), ","));
        $ep=strripos($name, " ");
        $last=substr($name,$ep+1);
        $name=substr($name,0,$ep+1);
        $ep=stripos($name," ");
        $first=substr($name,0,$ep);
        $middle=substr($name,$ep+1);
        $middle=trim($middle," +");
        return array($last,$middle,$first);
    }
    private static function getOccupation($line){
        if($line=="Anesthesiologist") return 1;
        else return 0;
    }

    private static function getTime($line,$date){
        if($line=="now") return $date." "."05:00";
        $date=null;
        $hourInt = null;
        $minuteInt = null;
        if(strlen($line)>4){
            $date=self::getDate(substr($line,0,stripos($line," ")));
            $line=substr($line,stripos($line," ")+1);
        }else{
            date_default_timezone_set('America/New_York');
            $date=date('y-m-d',strtotime("-1 day"));
        }
        $hourInt = substr($line, 0, 2);
        $minuteInt = substr($line, 2);

        return $date." ".$hourInt.":".$minuteInt;
    }



    public function __construct($datefile)
    {
        $this->filepath = Constant::EVAL_REPORT_PATH.$datefile.Constant::EXTENSION;

        /**
         * Assign value to date
         */
        $year = intval(substr($datefile, 0, 4));
        $month = intval(substr($datefile, 4, 2));
        $day = intval(substr($datefile, 6));
        $this->date = $year."-".$month."-".$day;

        if (!$this->insertEvaluateData($datefile))
        {
            $this->fileExists=false;
        }


    }

    public function fileExists()
    {
        return $this->fileExists;
    }

    private function insertEvaluateData($datefile){
        if (!file_exists($this->filepath)) {
            Log::info("no evaluate file");
            return false;
        }
        Log::info("parse evaluation data");
        $fp = fopen($this->filepath, 'r');

        $failedAddingUsers = array();
				
				$time_difference = DB::table('variables')->where('name', 'time_before_attending_evaluates_resident')->value('value');
				$time_difference = (int)$time_difference;

        fgetcsv($fp);
        while(($line = fgetcsv($fp)) !== false){
            $participants=self::getParticipants($line[7]);
            var_dump($participants);

            foreach ($participants as $resident) {
            	if($resident->occupation == 0 and $resident->diff >= $time_difference){
            		foreach ($participants as $attending) {
            			if($attending->occupation == 1 and $attending->diff >= $time_difference){
            				$resident->diff = (min(strtotime($resident->endTime), strtotime($attending->endTime)) - max(strtotime($resident->startTime), strtotime($attending->startTime)))/60;
                            if($resident->diff >= $time_difference){
                                $date=self::getDate($line[0]);
                                $diagnosis=$line[1];
                                $procedure=$line[2];
                                $location=$line[3];
                                $asa=$line[4];

                                // Add new resident to database if doesn't exist
                                $rId = null;
                                if(Resident::where('name', $resident->namefl)->doesntExist()){
                                    if (!in_array($resident->namefl, $failedAddingUsers)){
                                        echo 'Resident '.$resident->namefl.' doesnt exist'."\n";
                                        $MHC = new MedhubController();
                                        $residentAdded = $MHC->addUserFromMedhub('Resident', $resident->namefl);
                                        echo 'Resident added: '.$residentAdded."\n";
                                        if ($residentAdded){
                                            echo 'Resident '.$resident->namefl.' added'."\n";
                                            $rId = Resident::where('name', $resident->namefl)->value('id');
                                        } else {
                                            array_push($failedAddingUsers, $resident->namefl);
                                        }
                                    }
                                } else {
                                    echo 'Resident '.$resident->namefl.' exists'."\n";
                                    $rId = Resident::where('name', $resident->namefl)->value('id');
                                }

                                // Add new attending to database if doesn't exist
                                $aId = null;
                                if(Attending::where('name', $attending->namefl)->doesntExist()){
                                    if (!in_array($attending->namefl, $failedAddingUsers)){
                                        echo 'Attending '.$attending->namefl.' doesnt exist'."\n";
                                        $MHC = new MedhubController();
                                        $attendingAdded = $MHC->addUserFromMedhub('Attending', $attending->namefl);
                                        echo 'resident added: '.$attendingAdded."\n";
                                        if ($attendingAdded){
                                            echo 'Attending '.$attending->namefl.' added'."\n";
                                            $aId = Attending::where('name', $attending->namefl)->value('id');
                                        } else {
                                            array_push($failedAddingUsers, $attending->namefl);
                                        }
                                    }
                                    
                                } else {
                                    echo 'Attending '.$attending->namefl.' exists'."\n";
                                    $aId = Attending::where('name', $attending->namefl)->value('id');
                                }

                                $schedule = Assignment::where(['resident'=>$rId, 'date'=>$date])->value('schedule');
                                $milestone = Option::where(['resident'=>$rId, 'schedule'=>$schedule])->value('milestones');
                                $objective = Option::where(['resident'=>$rId, 'schedule'=>$schedule])->value('objectives');
                                //$prefAnest = Option::where(['resident'=>$rId, 'schedule'=>$schedule])->value('anesthesiologist_id');
                                echo $rId."\n"."Diff mins is: ".$resident->diff."\n";
                                echo "Resident ".$resident->namefl."\n";
                                echo " work with Attending ".$attending->namefl."<br>";
                                echo "see aId "."\n"."milestones ".$schedule."\n".$milestone;
                                if(EvaluateData::where(['date'=>$date, 'location'=>$location, 'resident'=>$resident->namefl, 'attending'=>$attending->namefl])->doesntExist()){
                                    EvaluateData::insert(
                                        ['date' => $date, 'location' => $location, 'diagnosis' => $diagnosis, 'procedure' => $procedure,'asa' => $asa, 'rId' => $rId, 'resident' => $resident->namefl, 'aId' => $aId, 'attending' => $attending->namefl, 'diff' =>$resident->diff ]
                                    );
                                }
                            }
            			}
            		}
            	}

            }
        }

        fclose($fp);
        return true;
    }



}
