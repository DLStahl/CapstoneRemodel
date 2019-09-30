<?php


/*
    Get userIDs and add to REMODEL database
    Active Residents (page 133) get all the resident userID, add all of the output data to the database
    Active Faculty (page 135) same as residents but for faculty
    Resident Search (page 149) can be used to lookup a specific resident, Faculty Search (page 150) can do the same for a specific attending

    Get the Resident Schedules and add to REMODEL database
    Academic Years (page 117) —> I don’t fully understand how this call works but I think it just sets a variable rotationsetID for the current academic year (e.g. “18” for July 1 2018 to June 30 2019) —> you need it for the rotations call
    Schedules (page 121) —> once you have the academic year from the “Academic Years” call (above) then you use this to get the scheduleID for each resident (who I think will be output as schedule_name)
    Rotations (page 119) once we have the scheduleID (from the Academic Years call above), and the rotationsetID (from the Rotations call above) we can use this call to get the specific schedule for a specific resident -> the output is the rotationID and rotation_name (we want to keep both) and the start_date and end_date which we will need to cross reference to send the appropriate eval

    Figure out which evaluation goes with which rotation
    ...
 */

 // remodel is green 1611

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use GuzzleHttp\Client;
use App\Resident;
use App\EvaluateData;
use App\Rotations;
use App\Attending;
use Mail;
use \Datetime;

class MedhubController extends Controller
{

	public function medhubConnect()
    {
        $users = self::activeResidentsPOST()->getBody(); //get string JSON
        $usersArr = json_decode($users, true); //turn json into an array

		$fac = self::activeFacultyPOST()->getBody(); //get string JSON
        $facArr = json_decode($fac, true); //turn json into an array
		echo var_dump($facArr);

        $message = 'MedHub Active Residents';

        return view('schedules.admin.medhubTestPage', compact('message','usersArr'));
	}


    // request format: json_encode(array('programID' => 73));
    // or just a JSON encoded string: '{"programID":73}'
    public function medhubPOST($callPath, $request = '{"programID":73}')
    {
        $client = new Client([
            'base_uri' => 'https://osu.medhub.com/functions/api/'
        ]);
        $clientID='5006';
        $privateKey='331xyg1hl65o';

        return $client->request('POST', $callPath, [
            'form_params' => [
                $time = time(),
                'clientID' => $clientID,
                'ts' => $time,
                'type' => 'json',
                'request' => $request,
                'verify' => hash('sha256', "$clientID|$time|$privateKey|$request")
            ]
        ]);
    }

	// // only meant to be used when resident table UserID and Medhub UserID are inconsistent
	// public function UpdateResidentTable($usersArr)
	// {
		// // loop through all returned activeResidents and if they are in our resident table, update the UserID, ResidentType, and Level
		// foreach ($usersArr as $user){
			// $userID = $user['userID'];
			// $first = $user['name_first'];
			// $last = $user['name_last'];
			// $name = $first.' '.$last;
			// $email = $user['email'];
			// $username = $user['username'];
			// $typeID = $user['typeID'];
			// $level = $user['level'];

			// Resident::where('name', $name)->update(
			// [	'medhubId' => $userID
			// ]);

			 // if(Resident::where('name', $name)->doesntExist()){
				// Resident::insert(['name'=>$name, 'email'=>$email, 'exists'=>1, 'medhubId'=>$userID]);
			 // }
		// }
	// }

	// // only meant to be run once because different tables used different format for name, this function makes it so all names in resident table use "First (space) Last" format
	// public function UpdateResidentName()
	// {
		// // loop through all returned activeResidents and if they are in our resident table, update the UserID, ResidentType, and Level
		// $residentNames = DB::table('resident')->pluck('name');

		// for($i = 0; $i<count($residentNames); $i++)
		// {
			// $name = $residentNames[$i];
			// $oldName = $residentNames[$i];
			// $pieces = explode(" ", $name);

			// if(count($pieces) == 3 && $pieces[1] != "Del")
			// {
				// $first = $pieces[0];
				// $last = $pieces[2];
				// $name = $first.' '.$last;
			// }
			// elseif(count($pieces) == 4)
			// {
				// $first = $pieces[0];
				// $last = $pieces[2].' '.$pieces[3];
				// $name = $first.' '.$last;
			// }
			// elseif(count($pieces) == 5)
			// {
				// $first = $pieces[0];
				// $last = $pieces[2].' '.$pieces[3].' '.$pieces[4];
				// $name = $first.' '.$last;
			// }

			// $id = Resident::where('name', $oldName)->value('id');

			// DB::table('resident')
            // ->where('id', $id)
            // ->update(['name' => $name]);
		// }
	// }

	// // initiate evaluation for attending to eval resident
    // // returns responseID - unique evaluation identifier (value is 0 if initiation failed)
    // public function initAttendingEvalResidentPOST($evaluatorID, $evaluateeID, $evalID)
    // {
        // $callPath = 'evals/initiate';
        // $evalType = 5;
        // $programID = 73;
        // $notify = true;
        // $request = json_encode(array('evaluationID' => $evalID,'evaluation_type' => $evalType, 'evaluator_userID' => $evaluatorID, 'programID' => $programID, 'evaluatee_userID' => $evaluateeID));

        // return self::medhubPOST($callPath, $request);
    // }

	// // initiate evaluation for resident to eval attending
	// // returns responseID - unique evaluation identifier (value is 0 if initiation failed)
    // public function initResidentEvalAttendingPOST($evaluatorID, $evaluateeID)
    // {
        // $callPath = 'evals/initiate';
        // $evalID = 1353; // 'Resident Evaluation of Faculty' (v.2)
        // $evalType = 2;
        // $programID = 73;
        // $notify = true;
        // $request = json_encode(array('evaluationID' => $evalID,'evaluation_type' => array($evalType), 'evaluator_userID' => $evaluatorID, 'programID' => $programID, 'evaluatee_userID' => $evaluateeID));
		// return self::medhubPOST($callPath, $request);
    // }

	// public function initiateEvaluations()
	// {
		// $date = date('Y-m-d');
		// $newdate = strtotime ( '-1 day' , strtotime ( $date ) ) ;
		// $newdate = date ( 'Y-m-d' , $newdate );

		// $residentID = DB::table('evaluation_data')->where('date', $newdate)->pluck('resident');
		// $attendingName = DB::table('evaluation_data')->where('date', $newdate)->pluck('attending');

		// for($i = 0; $i<count($residentID); $i++)
		// {
			// $evaluateeName = $residentID[$i];

			// $rName = $evaluateeName;
			// $pieces = explode(" ", $rName);
			// if(count($pieces) == 3 && $pieces[1] != "Arce" && $pieces[1] != "Puertas" && $pieces[1] != "Zuleta")
			// {
				// $first = $pieces[0];
				// $last = $pieces[2];
				// $rName = $first.' '.$last;
			// }
			// elseif(count($pieces) == 4)
			// {
				// $first = $pieces[0];
				// $last = $pieces[2].' '.$pieces[3];
				// $rName = $first.' '.$last;
			// }
			// elseif(count($pieces) == 5)
			// {
				// $first = $pieces[0];
				// $last = $pieces[2].' '.$pieces[3].' '.$pieces[4];
				// $rName = $first.' '.$last;
			// }

			// $evaluateeID = Resident::where('name', $rName)->value('medhubId');

			// // grab all of the start and end dates for the resident
			// $evaluateeRotationsStart = Rotations::where('Name', $evaluateeName)->pluck('Start');
			// $evaluateeRotationsEnd = Rotations::where('Name', $evaluateeName)->pluck('End');

			// // save todays date
			// $date = date('Y-m-d');
			// $date=date('Y-m-d', strtotime($date));
			// // temporary service variable
			// $evaluateeService = 0;
			// // loop through all the residents start/end dates
			// for ($j = 0; $j < count($evaluateeRotationsStart); $j++) {
				// $startDate = date('Y-m-d', strtotime($evaluateeRotationsStart[$j])); //get the  start date
				// $endDate = date('Y-m-d', strtotime($evaluateeRotationsEnd[$j])); //get the  end date

				// if (($date> $startDate) && ($date<$endDate)){ // find the date range that fits today
					// $evaluateeService = Rotations::where('Name', $evaluateeName)->where('Start', $evaluateeRotationsStart[$j])->value('Service');
					// break; // break so we can save the evaluateeService
				// }
			// }

			// //check if we even need to send an eval ( 0 for the service means we dont send it)
			// if($evaluateeService == 0)
			// {
				// // do nothing
			// }
			// else
			// {
				// // grab the attending id
				// $aName = $attendingName[$i];
				// $pieces = explode(" ", $aName);

				// if(count($pieces) == 3 && $pieces[1] != "Del")
				// {
					// $first = $pieces[0];
					// $last = $pieces[2];
					// $aName = $first.' '.$last;
				// }
				// elseif(count($pieces) == 4)
				// {
					// $first = $pieces[0];
					// $last = $pieces[2].' '.$pieces[3];
					// $aName = $first.' '.$last;
				// }
				// elseif(count($pieces) == 5)
				// {
					// $first = $pieces[0];
					// $last = $pieces[2].' '.$pieces[3].' '.$pieces[4];
					// $aName = $first.' '.$last;
				// }

				// $evaluatorID = Attending::where('name', $aName)->value('id');
				// if($evaluateeID != null )
				// {

					// echo $evaluatorID.' '.$evaluateeID.' '.$evaluateeService;
					// break;

					// //self::initAttendingEvalResidentPOST($evaluatorID, $evaluateeID, $evaluateeService);
					// //self::initResidentEvalAttendingPOST($evaluateeID, $evaluatorID);
				// }

			// }

		// }
	// }
	public function notifyAddUser($userType, $toName, $toEmail, $userName, $body='The resident could not be found and must be added manually.', $subject = 'REMODEL: Resident/Attending Needs to be Added')
    {
		//REMODEL Alert: Resident Needs to be Added
		$heading = $userType." ".$userName.' needs to be added.';
        $data = array('name'=>$toName, 'heading'=>$heading, 'body'=>$body);

        Mail::send('emails.mail', $data, function($message) use ($toName, $toEmail, $subject) {
            $message->to($toEmail, $toName)->subject($subject);
            $message->from('OhioStateAnesthesiology@gmail.com');
        });
    }

	public function findPeopleOSU($firstName, $lastName)
	{
		$client = new Client([
            'base_uri' => 'http://directory.osu.edu/'
		]);
		$callPath = 'fpjson.php?';
        $query = "firstname=$firstName&lastname=$lastName";
	    echo 'find people OSU: http://directory.osu.edu/fpjson.php?'.$query."\n";
		return $client->request('GET', $callPath, ['query' => $query]);
	}

	// Find user information using OSU Find People API.
	public function addUserFromFindPeopleOSU($userType, $emailMessage, $name, $medhubId=null){
    	$namefl = explode(" ", $name);
    	$name_first = $namefl[0];
    	$name_last = $namefl[1];

    	$osuMatches = json_decode(self::findPeopleOSU($name_first, $name_last)->getBody(), true);

		$added = false;
		$osuEmail = '';
		if(sizeof($osuMatches)==1){
    		$osuEmail = $osuMatches[0]['email'];
			echo 'osu email: '.$osuEmail."\n";
			// check if he/she is an Anesthesiology resident/attending
        	if(sizeof($osuMatches[0]['appointments'])>0 && $osuMatches[0]['appointments'][0]['organization'] == 'Anesthesiology'){
        		if(strcmp($userType, 'Resident') == 0){
        			Resident::insert(['name'=>$name, 'email'=>$osuEmail, 'exists'=>1, 'medhubId'=>$medhubId]);
				} else {
				    Attending::insert(['name'=>$name, 'email'=>$osuEmail, 'exists'=>1, 'id'=>$medhubId]);
				}
        		
        		echo $userType." ".$name.' added to database'."\n";
				$added = true;
			} else {
				//not an an Anesthesiology resident
				$emailMessage = $emailMessage.$userType.' '.$name.' was found by OSU Find People but is not an Anesthesiology '.$userType.'. Please check the information and add user to database manually.';
	            // self::notifyAddUser($userType, 'Gaberr', 'mralawi7@gmail.com', "$name_first $name_last", $emailMessage);
	            self::notifyAddUser($userType, 'David', 'david.stahl@osumc.edu', $name, $emailMessage);
	            self::notifyAddUser($userType, 'Gail', 'chentianzhigail@gmail.com', $name, $emailMessage);
			}
		} elseif (sizeof($osuMatches)==0) {
			echo '0 residents found in FindPeopleOSU'."\n";
        	$emailMessage = $emailMessage.'No matches for '.$userType.' '.$name.' were found by OSU Find People. The '.$userType.' may be using a preffered name at OSU. Please check the information and add user to database manually.';
    		// self::notifyAddUser($userType, 'Gaberr', 'mralawi7@gmail.com', "$name_first $name_last", $emailMessage);
            self::notifyAddUser($userType, 'David', 'david.stahl@osumc.edu', $name, $emailMessage);
            self::notifyAddUser($userType, 'Gail', 'chentianzhigail@gmail.com', $name, $emailMessage);
    	} else {
    		echo 'More than 1 residents found in FindPeopleOSU'."\n";
    		$emailMessage = $emailMessage.'Multiple matches for '.$userType.' '.$name.' were found by OSU Find People. Please check the information and add user to database manually. ';
    		// self::notifyAddUser($userType, 'Gaberr', 'mralawi7@gmail.com', "$name_first $name_last", $emailMessage);
            self::notifyAddUser($userType, 'David', 'david.stahl@osumc.edu', $name, $emailMessage);
            self::notifyAddUser($userType, 'Gail', 'chentianzhigail@gmail.com', $name, $emailMessage);
    	}
		echo var_dump($osuMatches);
    	return $added;
	}

	// Get user's medhubId from Medhub. Get user's name.# email address from OSU Find People.
	// $userType is eiterh 'Resident' or 'Attending'
	public function addUserFromMedhub($userType, $name){
		$userAdded = false;
		$medhubMatches = null;
		if(strcmp($userType, 'Attending') == 0){
			$medhubMatches = json_decode(self::medhubPOST("users/facultySearch",json_encode(array('name' => $name)))->getBody(), true);
    	} else {
        	$medhubMatches = json_decode(self::medhubPOST("users/residentSearch",json_encode(array('name' => $name)))->getBody(), true);
    	}

    	echo'looking for '.$userType." ".$name.' info'."\n";

    	if(sizeof($medhubMatches)==1){
        	echo "medhub found people success"."\n";
        	$medhubId = $medhubMatches[0]['userID'];
        	echo 'medhub email: '.$medhubMatches[0]['email']."\n";
        	$emailMessage = $userType." ".$name.' with MedHubID '.$medhubId.' was found on MedHub. ';
        	$userAdded = self::addUserFromFindPeopleOSU($userType, $emailMessage, $name, $medhubId);
        } elseif (sizeof($medhubMatches)==0) {
      		echo '0 '.$userType.'s found in MedHub'."\n";
        	$emailMessage = 'No matches for '.$userType." ".$name.' were found on MedHub. ';
        	if(strcmp($userType, 'Attending') == 0){
        		// self::notifyAddUser($userType, 'Gaberr', 'mralawi7@gmail.com', "$name_first $name_last", $emailMessage);
	            self::notifyAddUser($userType, 'David', 'david.stahl@osumc.edu', $name, $emailMessage);
	            self::notifyAddUser($userType, 'Gail', 'chentianzhigail@gmail.com', $name, $emailMessage);
        	} else {
	        	$userAdded = self::addUserFromFindPeopleOSU($userType, $emailMessage, $name);
        	}
		} else {
			echo 'More than one residents found in Medhub'."\n";
			$emailMessage = 'Multiple matches for '.$userType." ".$name.' were found on MedHub. ';
			if(strcmp($userType, 'Attending') == 0){
        		// self::notifyAddUser($userType, 'Gaberr', 'mralawi7@gmail.com', "$name_first $name_last", $emailMessage);
	            self::notifyAddUser($userType, 'David', 'david.stahl@osumc.edu', $name, $emailMessage);
	            self::notifyAddUser($userType, 'Gail', 'chentianzhigail@gmail.com', $name, $emailMessage);
        	} else {
	        	$userAdded = self::addUserFromFindPeopleOSU($userType, $emailMessage, $name);
        	}
		}

		echo var_dump($medhubMatches);
		return $userAdded;
	}



	// needs to be refactored still
	//post call for schedule (requires programID & rotationSetID from academicYearPOST)
	public function schedulePOST()
	{
		$callPath = 'schedules/view';
		$programID = 73;

		$rotationsetID = 0; //temporarily set the rotationSetID to 0
		$years = self::academicYearPOST()->getBody(); //call academicYearPOST to get the rotationSetID argument
		$yearsArr = json_decode($years, true); //turn json into an array
		$date = date('Y-m-d');
		$date=date('Y-m-d', strtotime($date)); // get todays date
		for ($i = 0; $i < count($yearsArr); $i++) {
			$rotationsetID = $yearsArr[$i]['rotationsetID'];
			$startDate = date('Y-m-d', strtotime($yearsArr[$i]['start_date'])); //get the rotation start date from academicYearPOST
			$endDate = date('Y-m-d', strtotime($yearsArr[$i]['end_date'])); //get the rotation end date from academicYearPOST

			if (($date> $startDate) && ($date<$endDate)){ // find the date range that fits today
				break; // break so we can save the rotationSetID
			}
		}
		$request = json_encode(array('programID' => $programID,"rotationsetID" =>$rotationsetID)); // setup the arguments properly
		return self::medhubPOST($callPath, $request);
	}

	// needs to be refactored still
	//post call for rotations (requires scheduleID & rotationSetID from schedulePOST, academicYearPOST)
	public function rotationsPOST()
	{
			$callPath = 'schedules/rotations';
		$programID=73;

		$rotationsetID = 0; //temporarily set the rotationSetID to 0
		$years = self::academicYearPOST()->getBody(); //call academicYearPOST to get the rotationSetID argument
		$yearsArr = json_decode($years, true); //turn json into an array
		$date = date('Y-m-d');
		$date=date('Y-m-d', strtotime($date)); // get todays date
		for ($i = 0; $i < count($yearsArr); $i++) {
			$rotationsetID = $yearsArr[$i]['rotationsetID'];
			$startDate = date('Y-m-d', strtotime($yearsArr[$i]['start_date'])); //get the rotation start date from academicYearPOST
			$endDate = date('Y-m-d', strtotime($yearsArr[$i]['end_date'])); //get the rotation end date from academicYearPOST

			if (($date> $startDate) && ($date<$endDate)){ // find the date range that fits today
				break; // break so we can save the rotationSetID
			}
		}
		$schedule = self::schedulePOST($programID, $rotationsetID)->getBody(); // call schedule post
		$scheduleArr = json_decode($schedule, true);
		$scheduleID = $scheduleArr[0]['scheduleID']; // get the scheduleID from the schedule post
		$request = json_encode(array('scheduleID' => $scheduleID,"rotationsetID" =>$rotationsetID)); // setup the arguments properly
		return self::medhubPOST($callPath, $request);
	}

	//post call for active residents
	public function activeResidentsPOST()
	{
		return self::medhubPOST('users/residents');
	}

	//post call for active faculty (not sure if needed since most numbers seem to match up already)
	public function activeFacultyPOST()
	{
		return self::medhubPOST('users/faculty');
	}

	// post call for academic year
	public function academicYearPOST()
	{
		return self::medhubPOST('schedules/years');
	}

	//post call for evaluationForms
	public function evaluationFormsPOST()
	{
		return self::medhubPOST('evals/forms');
	}

	//post call for evaluationTypes
	public function evaluationTypesPOST()
	{
		return self::medhubPOST('evals/types');
	}

	// API Test
	public function testPOST()
	{
		return self::medhubPOST('info/test');
	}



}
