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
	// public function notifyAddResident($toName, $toEmail, $residentName, $body='The resident could not be found and must be added manually.', $subject = 'REMODEL: Resident Needs to be Added')
    // {
		// //REMODEL Alert: Resident Needs to be Added
		// $heading = "Resident $residentName needs to be added.";
        // $data = array('name'=>$toName, 'heading'=>$heading, 'body'=>$body);

        // Mail::send('emails.mail', $data, function($message) use ($toName, $toEmail, $subject) {
            // $message->to($toEmail, $toName)->subject($subject);
            // $message->from('OhioStateAnesthesiology@gmail.com');
        // });
    // }

	// public function findPeopleOSU($firstName, $lastName)
	// {
		// $client = new Client([
            // 'base_uri' => 'http://directory.osu.edu/'
		// ]);
		// $callPath = 'fpjson.php?';
        // $query = "firstname=$firstName&lastname=$lastName";

		// return $client->request('GET', $callPath, ['query' => $query]);
	// }

	// public function addResidentFromMedhub($name){
		// $residentMatches = json_decode(self::medhubPOST("users/residentSearch",json_encode(array('name' => $name)))->getBody(), true);
		// if(sizeof($residentMatches)==1){
			// $name_first = $residentMatches[0]['name_first'];
			// $name_last = $residentMatches[0]['name_last'];
			// $osuMatches = json_decode(self::findPeopleOSU($name_first, $name_last)->getBody(), true);
			// // check if they are an Anesthesiology resident
			// if(sizeof($osuMatches)==1 && sizeof($osuMatches[0]['appointments'])>0 && $osuMatches[0]['appointments'][0]['organization'] == 'Anesthesiology'){
				// $osuEmail = $osuMatches[0]['email'];
				// echo "	$osuEmail";
				// //add resident
				// //Resident::insert(['name'=>$name, 'email'=>$osuEmail, 'exists'=>1, 'UserID'=>$residentMatches[0]['userID'], 'ResidentType'=>$residentMatches[0]['typeID'], 'Level'=>$residentMatches[0]['level']]);
			// }else{
				// $emailMessage = 'Resident was found on MedHub, but a definite match was not found. There may be multiple students with the same name or the resident may be using a preffered name at OSU.';
				// self::notifyAddResident('Gaberr', 'mralawi7@gmail.com', "$name_first $name_last", $emailMessage);
			// }
		// }else{
			// // send email to add resident
			// if(sizeof($residentMatches)>1){
				// $emailMessage = 'Multiple matches for this resident were found on MedHub.';
			// }else{
				// $emailMessage = 'No matches for this resident were found on MedHub.';
			// }
			// self::notifyAddResident('Gaberr', 'mralawi7@gmail.com', $name, $emailMessage);
			// // maybe say who they are working with
		// }
		// echo var_dump($residentMatches);
		// echo var_dump($osuMatches);
	// }	

	
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
