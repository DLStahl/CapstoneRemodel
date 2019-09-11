<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Constant;
use App\ScheduleData;
use App\Option;
use Illuminate\Support\Facades\Log;

class ScheduleParser extends Model
{

    /**
     * Protected members
     */
    protected $filepath;
    protected $date;
    protected $fileExists = true;

    /**
     * Private functions
     */

    /**
     * Parse date
     * 
     * @var string
     */
    private static function getLineDate($line)
    {

        $month = intval(substr($line[Constant::DATE], 0, 2));
        $day = intval(substr($line[Constant::DATE], 3, 5));
        $year = intval(substr($line[Constant::DATE], 6));
        
        return ($year."-".$month."-".$day);
    }

    /**
     * Parse time
     * 
     * @var string
     * 
     * @var int
     * 
     */
    private static function getLineTime($line, $index)
    {

        if (strcmp($line[$index], "") == 0) return null;
        $hourInt = intval(substr($line[$index], 0, 2));
        $minuteInt = intval(substr($line[$index], 2));
        
        return ($hourInt.":".$minuteInt.":00");
    }

    /**
     * Insert data into database.
     */
    private function insertScheduleData()
    {

        if (!file_exists($this->filepath)) {
            Log::info($this->filepath);
            return false;
        }

        date_default_timezone_set('America/New_York');
        ScheduleData::where('date',date("Y-m-d",strtotime('+2 day')))->delete();
        Log::info("detele succ");
        /**
         * Open file
         */
        $fp = fopen($this->filepath, 'r');

        /**
         * Read the first row
         */
        fgetcsv($fp);
        
        while (($line = fgetcsv($fp)) !== false)
        {
            $date = self::getLineDate($line);
            $location = $line[Constant::LOCATION];
            $room = $line[Constant::ROOM];
            $case_procedure = $line[Constant::CASE_PROCEDURE];
            $lead_surgeon = $line[Constant::LEAD_SURGEON];
            $patient_class = $line[Constant::PATIENT_CLASS];
            $start_time = self::getLineTime($line, Constant::START_TIME);
            $end_time = self::getLineTime($line, Constant::END_TIME);

            ScheduleData::insert(
                ['date' => $date, 'location' => $location, 'room' => $room, 'case_procedure' => $case_procedure, 
                'lead_surgeon' => $lead_surgeon, 'patient_class' => $patient_class, 'start_time' => $start_time, 
                'end_time' => $end_time]
            );
        }

        /**
         * Close file
         */
        fclose($fp);

        return true;

    }

    
    /**
     * Public functions
     */
    
    /**
     * Constructor of ScheduleBuffer.
     * 
     * @var string
     *      {@code date} Formate: "year" + "month" + "day"
     */
    public function __construct($datefile, $isConsole=false)
    {   
        Log::info('here');
        $this->filepath = $isConsole ? Constant::CONSOLE_PATH.$datefile.Constant::EXTENSION 
                            :Constant::WEB_PATH.$datefile.Constant::EXTENSION;

        Log::info($datefile);


        /**
         * Assign value to date
         */
        $year = intval(substr($datefile, 0, 4));
        $month = intval(substr($datefile, 4, 6));
        $day = intval(substr($datefile, 6));
        $this->date = $year."-".$month."-".$day;

        if (!$this->insertScheduleData())
        {
            $this->fileExists=false;
        }

        date_default_timezone_set('America/New_York');
        $this->processScheduleData(date('Y-m-d',strtotime('+2 day')));
        $this->processScheduleData(date('Y-m-d',strtotime('+4 day')));
    }

    public function fileExists()
    {
        return $this->fileExists;
    }

    /**
     * Combine data row with same date and room
     */
    public function processScheduleData($date)
    {

        $items=ScheduleData::select('room')->where('date', $date)->distinct()->get();
        foreach($items as $item){
            $records=ScheduleData::where('date', $date)->where('room',$item['room'])->orderBy('start_time')->get();
            $location=null;
            $room=null;
            $case_procedure=null;
            $lead_surgeon=null;
            $patient_class=null;
            $start_time=null;
            $end_time=null;
            Log::info($item['room']." numbers\n ".count($records));
            for ($i=0;$i<count($records);$i++){
                if($i==0){
                    $location=$records[$i]['location'];
                    $room=$records[$i]['room'];
                    $start_time=$records[$i]['start_time'];
                }
                if($i==count($records)-1){
                    $end_time=$records[$i]['end_time'];
                }


                
                //$line=$records[$i]['start_time']."-".$records[$i]['end_time'].":".$records[$i]['case_procedure']."\n";
                $line=$records[$i]['case_procedure']."\n";
                $case_procedure=$case_procedure.$line;
                $lead_surgeon=$lead_surgeon.$records[$i]['lead_surgeon']."\n";
                $patient_class=$patient_class.$records[$i]['patient_class']."\n";

            }
            ScheduleData::where('date',$date)->where('room',$room)->delete();
            ScheduleData::insert(
                ['date' => $date, 'location' => $location, 'room' => $room, 'case_procedure' => $case_procedure,
                    'lead_surgeon' => $lead_surgeon, 'patient_class' => $patient_class, 'start_time' => $start_time,
                    'end_time' => $end_time]
            );
        }
    }
    
}
