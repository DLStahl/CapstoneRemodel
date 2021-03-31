<?php

namespace App;

use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use App\Constant;
use App\Models\EvaluateData;
use App\Models\Resident;
use App\Models\Attending;
use App\Http\Controllers\MedhubController;
use Illuminate\Support\Facades\Mail;
use GuzzleHttp\Client;
use Carbon\Carbon;

class EvaluationParser extends Model {
    protected $filepath;
    protected $date;
    public $residentsFailedAdding = array(); // residents failed to add to DB
    public $attendingsFailedAdding = array(); // attendings failed to add to DB
    public $residentsInFile = array(); // all residents that appear in csv file
    public $attendingsInFile = array(); // all attendings that appear in csv file

    public function __construct($datefile) {
        $this->filepath = Constant::EVAL_REPORT_PATH . $datefile . Constant::EXTENSION;
        // get date in yyyy-mm-dd format
        $year = intval(substr($datefile, 0, 4));
        $month = intval(substr($datefile, 4, 2));
        $day = intval(substr($datefile, 6));
        $this->date = $year . "-" . $month . "-" . $day;
        self::insertEvaluateData();
    }

    public function insertEvaluateData() {
        if (!file_exists($this->filepath)) {
            Log::info("no evaluation file: " . $this->date);
            return false;
        }
        Log::info("Parse evaluation data for " . $this->date);
        $insertionDate = date('Y-m-d');
		$yesterday = strtotime ( '-1 day' , strtotime ( $insertionDate ) ) ;
		$dateInDB = date ( 'Y-m-d' , $yesterday );
        $fp = fopen($this->filepath, 'r');
        // amount of minutes that resident and attending need to overlap to have an evaluation
        $time_difference = DB::table('variables')->where('name', 'time_before_attending_evaluates_resident')->value('value');
        $time_difference = (int)$time_difference;

        fgetcsv($fp);
        while (($line = fgetcsv($fp)) !== false) {
            // extract participants from Attending/Resident Times col
            $participants = self::getParticipants($line[7]);
            foreach ($participants['residentsInLine'] as $resident) {
                if ($resident['diff'] >= $time_difference) {
                    foreach ($participants['attendingsInLine'] as $attending) {
                        if ($attending['diff'] >= $time_difference) {
                            // get the minimum amount of time resident and attending spent together 
                            $minutesOverlapped = (min(strtotime($resident['endTime']), strtotime($attending['endTime'])) - max(strtotime($resident['startTime']), strtotime($attending['startTime'])))/60;
                            if ($minutesOverlapped >= $time_difference) {
                                $diagnosis = $line[1];
                                $procedure = $line[2];
                                $location = $line[3];
                                $asa = $line[4];

                                $rId = $resident['id'];
                                $rName = $resident['dbName'];
                                $aId = $attending['id'];
                                $aName = $attending['dbName'];
                                
                                //echo "\nEvaluation Data: " . $dateInDB . ": Diagnosis(" . $diagnosis . ") Resident(" . $rName . ") Attending(" . $aName . ") Diff(". $minutesOverlapped . ")";
                                if (EvaluateData::where(['date' => $dateInDB, 'location' => $location, 'resident' => $rName, 'attending' => $aName])->doesntExist()) {
                                    Log::info("Adding Evaluation Data: " . $dateInDB . ": Diagnosis(" . $diagnosis . ") Resident(" . $rName . ") Attending(" . $aName . ") Diff(". $minutesOverlapped . ")");
                                    EvaluateData::insert([
                                        'date' => $dateInDB, 
                                        'location' => $location, 
                                        'diagnosis' => $diagnosis, 
                                        'procedure' => $procedure, 
                                        'asa' => $asa, 
                                        'rId' => $rId, 
                                        'resident' => $rName, 
                                        'aId' => $aId, 
                                        'attending' => $aName, 
                                        'diff' => $minutesOverlapped,
                                        'created_at'=>Carbon::now()
                                    ]);
                                }
                            }
                        }
                    }
                }
            }
        }
        //send emails for all users not added 
        self::notifyForAllFailedUsers($this->residentsFailedAdding, $this->attendingsFailedAdding, "Melanie", "ferguson.881@osu.edu");
        fclose($fp);
    }

    private function getParticipants($line) {
        $residentsInLine = array();
        $attendingsInLine = array();
        $line = $line . "\n";
        // while there are still participants in line, parse and add to residents or attendings
        while ($line != false) {
            // get participant's full name
            $name = substr($line, 0, stripos(substr($line, 0), ","));
            $ep = stripos(substr($line, 0), "\n");
            $line = substr($line, $ep + 1);
            // parse participant information
            while (substr($line, 0, 1) == " ") {
                $ep = stripos($line, "\n");
                // determine if participant is resident or attending
                $tmp = trim(substr($line, 0, $ep), " +");
                $occupation = self::getOccupation(substr($tmp, 0, stripos($tmp, " ")));
                // get startTime
                preg_match("/from (.*) to/s", $tmp, $match);
                $startTime = self::getTime($match[1], $this->date);
                // get endTime
                preg_match("/to (.*)/s", $tmp, $match);
                $endTime = self::getTime($match[1], $this->date);
                // get # of minutes between start and end time
                $diff = self::getMinutesDiff($startTime, $endTime);
                
                if($occupation == 0){
                    // if resident hasn't been encountered in file, get info
                    if(!array_key_exists($name, $this->residentsInFile)){
                        $residentInfo = self::getParticipantInfo('Resident', $name);
                        // if not found in DB or added and not already encountered- store to send email later
                        if(!$residentInfo['found'] && !array_key_exists($residentInfo['name'],$this->residentsFailedAdding)){
                            $this->residentsFailedAdding[$residentInfo['name']] = $residentInfo['emailMessage'];
                        }
                        $rId = $residentInfo['id'];
                        $rName = $residentInfo['name'];
                        $resident = array(
                            'fullName' => $name,
                            'occupation' => $occupation,
                            'startTime' => $startTime,
                            'endTime' => $endTime,
                            'diff' => $diff,
                            'id' => $rId,
                            'dbName' => $rName
                        );
                        $this->residentsInFile[$name] = $resident;  
                    }else{
                        $resident = $this->residentsInFile[$name];
                        $resident = array(
                            'fullName' => $name,
                            'occupation' => $occupation,
                            'startTime' => $startTime,
                            'endTime' => $endTime,
                            'diff' => $diff,
                            'id' => $resident['id'],
                            'dbName' => $resident['dbName']
                        );
                        $this->residentsInFile[$name] = $resident;
                    }
                    array_push($residentsInLine, $resident);
                }else{
                    // if attending hasn't been encountered in file, get info
                    if(!array_key_exists($name, $this->attendingsInFile)){
                        $attendingInfo = self::getParticipantInfo('Attending', $name);
                        // if not found in DB or added - store to send email later
                        if(!$attendingInfo['found'] && !array_key_exists($attendingInfo['name'], $this->attendingsFailedAdding)){
                            $this->attendingFailedAdding[$attendingInfo['name']] = $attendingInfo['emailMessage'];
                        }
                        $aId = $attendingInfo['id'];
                        $aName = $attendingInfo['name'];
                        $attending = array(
                            'fullName' => $name,
                            'occupation' => $occupation,
                            'startTime' => $startTime,
                            'endTime' => $endTime,
                            'diff' => $diff,
                            'id' => $aId,
                            'dbName' => $aName
                        );
                        $this->attendingsInFile[$name] = $attending;
                    }else{
                        $attending = $this->attendingsInFile[$name];
                        $attending = array(
                            'fullName' => $name,
                            'occupation' => $occupation,
                            'startTime' => $startTime,
                            'endTime' => $endTime,
                            'diff' => $diff,
                            'id' => $attending['id'],
                            'dbName' => $attending['dbName']
                        );
                        $this->attendingsInFile[$name] = $attending;
                    }
                    array_push($attendingsInLine, $attending);
                }
                $line = substr($line, $ep + 1);
            }
        }
        return array(
            'residentsInLine' => $residentsInLine,
            'attendingsInLine' =>$attendingsInLine
        );
    }

    private static function getOccupation($line) {
        if ($line == "Anesthesiologist") return 1;
        else return 0;
    }

    public static function getTime($line, $date) {
        if ($line == "Now" || $line == "now"  ) return $date . " " . "05:00";
        $date = null;
        $hourInt = null;
        $minuteInt = null;
        if (strlen($line) > 4) {
            $date = self::getDate(substr($line, 0, stripos($line, " ")));
            $line = substr($line, stripos($line, " ") + 1);
        } else {
            date_default_timezone_set('America/New_York');
            $date = date('y-m-d', strtotime("-1 day"));
        }
        $hourInt = substr($line, 0, 2);
        $minuteInt = substr($line, 2);
        return $date . " " . $hourInt . ":" . $minuteInt;
    }

    public static function getDate($line) {
        $month = intval(substr($line, 0, 2));
        $day = intval(substr($line, 3, 2));
        $year = intval(substr($line, 6));
        return ($year . "-" . $month . "-" . $day);
    }

    public static function getMinutesDiff($startTime, $endTime) {
        return (strtotime($endTime) - strtotime($startTime)) / 60;
    }
    
    // get id and name for participant 
    //check DB first, if it doesn't exist use Find People OSU and Medhub calls and insert in DB
    public function getParticipantInfo($userType, $name){
        $possibleNames = self::getNamePossibilities($name);
        $foundParticipant = false;
        $participantInfo = array();
        foreach($possibleNames as $possibleName){
            $namefl = $possibleName[0] . " " . $possibleName[1];
            if(strcmp($userType, 'Resident') == 0) {
                $resident = Resident::where('name', $namefl)->first();
                if(!is_null($resident)){
                    $foundParticipant = true;
                    $participantInfo = array(
                        'found' => $foundParticipant,
                        'id' => $resident->id,
                        'name' => $resident->name,
                        'emailMessage' => ''
                    );
                    break;
                }
            }else{
                $attending = Attending::where('name', $namefl)->first();
                if(!is_null($attending)){   
                    $foundParticipant = true;
                    $participantInfo = array(
                        'found' => $foundParticipant,
                        'id' => $attending->id,
                        'name' => $attending->name,
                        'emailMessage' => '' 
                    );
                    break;
                }
            }
        }
        // if not in DB - get info and add to DB
        if(!$foundParticipant){
            $participantInfo = self::findAndInsertParticipantInDB($userType, $possibleNames);
        }
        return $participantInfo;
    }

    // use Find People OSU to find participant name and email
    // use Medhub to get participant medhubId
    // insert into DB if successful and return id and name 
    public function findAndInsertParticipantInDB($userType, $possibleNames){
        $MHC = new MedhubController();
        $participantID = NULL;
        $foundParticipant = true;
        $emailMessage ='';
        $oSUFindPeopleResults =self::getParticipantNameAndEmail($userType, $possibleNames);
        $medhubResults = $MHC->getMedhubId($userType, $oSUFindPeopleResults['name']);
        // if osu find people or medhub search failed - send message
        if(!$oSUFindPeopleResults['foundNameAndEmail'] || is_null($medhubResults['medhubId'])){
            $emailMessage = $medhubResults['emailMessage'] . $oSUFindPeopleResults['emailMessage'];
            $foundParticipant = false;
        }else{
            if(strcmp($userType, 'Resident') == 0){
                $participantID = Resident::insertGetId([
                    'name' => $oSUFindPeopleResults['name'],
                    'email' => $oSUFindPeopleResults['email'],
                    'exists' => 1,
                    'medhubId' => $medhubResults['medhubId'],
                    'created_at'=>Carbon::now()
                ]);
            }else {
                Attending::insert([
                    'name' => $oSUFindPeopleResults['name'],
                    'email' => $oSUFindPeopleResults['email'],
                    'exists' => 1,
                    'id' => $medhubResults['medhubId'],
                    'created_at'=>Carbon::now()
                ]);
                $participantID = $medhubResults['medhubId'];

            }
        }
        return array(
            'found' =>$foundParticipant,
            'id' => $participantID,
            'name' => $oSUFindPeopleResults['name'],
            'emailMessage' => $emailMessage
        );
    }

    // Find user information using OSU Find People API.
    // possibleNames = array of name arrays with 
    // [0] = first name
    // [1] = last name
	public function getParticipantNameAndEmail($userType, $possibleNames) {
        // if only one name possible
        if(count($possibleNames) == 1) {
            $results = self::findParticipantWithFindPeopleOSU($possibleNames[0][0], $possibleNames[0][1], $userType);
        }else {
            foreach($possibleNames as $possibleName){
                $results = self::findParticipantWithFindPeopleOSU($possibleName[0], $possibleName[1],$userType);
                if ($results['found']){
                    break;
                } 
            }
        }
        return array(
            'foundNameAndEmail' => $results['found'],
            'name' => $results['name'],
            'email' => $results['osuEmail'],
            'emailMessage' => $results['emailMessage']
        );
	}

    public function findParticipantWithFindPeopleOSU($firstName, $lastName, $userType){
        $participantFound = false;
        $osuEmail = NULL;
        $name = $firstName . ' ' . $lastName;
        $peopleFound = array();
        try{
            $peopleFound = json_decode(self::findPeopleOSU($firstName, $lastName)->getBody(), true);
        }catch(\Exception $e){
            Log::debug('Exception: Error in Find People OSU request for name (' . $name . '). Exception code: ' . $e->getCode() . ' Exception Message: ' . $e->getMessage());
        }
        if (sizeof($peopleFound) == 0) {
            $emailMessage = 'No matches for ' . $userType . ' ' . $name. ' were found by OSU Find People. The ' . $userType . ' may be using a preffered name at OSU. Please check the information and add user to database manually.';
        } else if (sizeof($peopleFound) == 1){
            $osuEmail = $peopleFound[0]['email'];
            // check if person has Anesthesiology as part of their organization field
            if (sizeof($peopleFound[0]['appointments']) > 0 && strpos($peopleFound[0]['appointments'][0]['organization'],'Anesthesiology') !== false) {
                $participantFound = true;
                $emailMessage = $userType . ' ' . $name . ' was found by OSU Find People with email address (' . $osuEmail . ').';
            }else{
                $emailMessage = $userType . ' ' . $name . ' with email ('. $osuEmail .') was found by OSU Find People but is not an Anesthesiology ' . $userType . '. Please check the information and add user to database manually.';
            }
        } else{
            $emailMessage = 'Multiple matches for ' . $userType . ' ' . $name . ' were found by OSU Find People. Please check the information and add user to database manually. ';
        }
        return array(
            'found' => $participantFound,
            'osuEmail' => $osuEmail,
            'name' => $name,
            'emailMessage' => $emailMessage
        );
    }

    public static function findPeopleOSU($firstName, $lastName) {
		$client = new Client([
			'base_uri' => 'http://directory.osu.edu/'
		]);
		$callPath = 'fpjson.php?';
        $query = "firstname=$firstName&lastname=$lastName";
		//echo 'find people OSU: http://directory.osu.edu/fpjson.php?' . $query . "\n";
		return $client->request('GET', $callPath, ['query' => $query]);
	}

    // Given a full name, return all possible first and last name combos
	// - returns an array of possible name arrays
	// - where a name array: [0] = first name string and [1] = last name string
    public static function getNamePossibilities($name){
		$nameParts = explode(" ", $name);
        $listOfSuffixes = ["II", "III", "IV", "V", "Jr", "Sr", "MD", "PhD", "DO", "RN"];
        $nameParts = array_diff($nameParts, $listOfSuffixes);
		// array of name arrays where name array[0] = first name and [1] = last name
		$nameOptions = []; 
		if (count($nameParts) == 2){
			$flname[0] = $nameParts[0];
			$flname[1] = $nameParts[1];
			array_push($nameOptions, $flname);
		} else {
			$middleName = NULL;
			for($i = 0; $i<count($nameParts) ; $i++){
				if(strlen($nameParts[$i]) == 1){
					$middleName = $i;
					break;
				}
			}
			// if middle initial exists split first and last name by its position
			if(!is_null($middleName)){
				$first = array_slice($nameParts, 0, $middleName);
				$flname[0] = implode(" ", $first);
				$last = array_slice($nameParts, $middleName + 1);
				$flname[1] = implode(" ", $last);
				array_push($nameOptions, $flname);
			}else{
				// find all first last name combinations
				for($i = 1; $i<count($nameParts); $i++){
					$first = array_slice($nameParts, 0 , $i);
					$flname[0] = implode(" ", $first);
					$last = array_slice($nameParts, $i);
					$flname[1] = implode(" ", $last);
					array_push($nameOptions, $flname);
				}
			}
		}
		return $nameOptions;
	}

    public function notifyForAllFailedUsers($residentsFailedAdding, $attendingsFailedAdding, $toName, $toEmail){
        $residentNames = array_keys($residentsFailedAdding);
        foreach($residentNames as $residentName){
            self::notifyForFailedUser('Resident', $toName, $toEmail, $residentName, $residentsFailedAdding[$residentName]);
        }
        $attendingNames = array_keys( $attendingsFailedAdding);
        foreach($attendingNames as $attendingName){
            self::notifyForFailedUser('Attending', $toName, $toEmail, $attendingName,  $attendingsFailedAdding[$attendingName]);
        }
    }
    
    public function notifyForFailedUser($userType, $toName, $toEmail, $userName, $body) {
		//REMODEL Alert: Resident Needs to be Added
        $subject = 'REMODEL: Resident/Attending Needs to be Added';
		$heading = $userType . " " . $userName . ' needs to be added.';
		$data = array('name' => $toName, 'heading' => $heading, 'body' => $body);
        Log::info("Resident/Attending Needs to be Added Email: Sending to: " . $toEmail . " Email Message: " . $body);
		Mail::send('emails.mail', $data, function ($message) use ($toName, $toEmail, $subject) {
			$message->to($toEmail, $toName)->subject($subject);
			$message->from('OhioStateAnesthesiology@gmail.com');
		});
	}
}
