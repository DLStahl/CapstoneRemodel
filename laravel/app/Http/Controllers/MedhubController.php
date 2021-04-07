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

use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;

class MedhubController extends Controller
{
    public function medhubConnect()
    {
        $users = self::activeResidentsPOST()->getBody(); //get string JSON
        $usersArr = json_decode($users, true); //turn json into an array

        $fac = self::activeFacultyPOST()->getBody(); //get string JSON
        $facArr = json_decode($fac, true); //turn json into an array
        // echo var_dump($facArr);

        $message = "MedHub Active Residents";

        $form = self::evaluationFormsPOST()->getBody(); //get string JSON
        $formArr = json_decode($form, true); //turn json into an array
        $types = self::evaluationTypesPOST()->getBody(); //get string JSON
        $typesArr = json_decode($types, true); //turn json into an array

        return view("schedules.admin.medhubTestPage", compact("message", "usersArr", "facArr", "formArr", "typesArr"));
    }

    // request format: json_encode(array('programID' => 73));
    // or just a JSON encoded string: '{"programID":73}'
    public function medhubPOST($callPath, $request = '{"programID":73}')
    {
        $client = new Client([
            "base_uri" => "https://osu.medhub.com/functions/api/",
        ]);
        $clientID = "5006";
        $privateKey = "331xyg1hl65o";

        return $client->request("POST", $callPath, [
            "form_params" => [
                ($time = time()),
                "clientID" => $clientID,
                "ts" => $time,
                "type" => "json",
                "request" => $request,
                "verify" => hash("sha256", "$clientID|$time|$privateKey|$request"),
            ],
        ]);
    }

    // Get Medhub Id using name and Medhub API
    public function getMedhubId($userType, $name)
    {
        $medhubId = null;
        $medhubMatches = array();
        if (strcmp($userType, "Attending") == 0) {
            try {
                $medhubMatches = json_decode(self::medhubPOST("users/facultySearch", json_encode(array("name" => $name)))->getBody(), true);
            } catch (\Exception $e) {
                Log::debug("Exception: Error in Medhub request users/facultySearch for name (" . $name . "). Exception code: " . $e->getCode() . " Exception Message: " . $e->getMessage());
            }
        } else {
            try {
                $medhubMatches = json_decode(self::medhubPOST("users/residentSearch", json_encode(array("name" => $name)))->getBody(), true);
            } catch (\Exception $e) {
                Log::debug("Exception: Error in Medhub request users/residentSearch for name (" . $name . "). Exception code: " . $e->getCode() . " Exception Message: " . $e->getMessage());
            }
        }
        $emailMessage = "";
        if (sizeof($medhubMatches) == 1) {
            $medhubId = $medhubMatches[0]["userID"];
            $emailMessage = $emailMessage . $userType . " " . $name . " with MedHubID " . $medhubId . " was found on MedHub. ";
        } elseif (sizeof($medhubMatches) == 0) {
            $emailMessage = $emailMessage . "No matches for " . $userType . " " . $name . " were found on MedHub. ";
        } else {
            $emailMessage = $emailMessage . "Multiple matches for " . $userType . " " . $name . " were found on MedHub. ";
        }
        return array(
            "medhubId" => $medhubId,
            "emailMessage" => $emailMessage
        );
    }

    // needs to be refactored still
    //post call for schedule (requires programID & rotationSetID from academicYearPOST)
    public function schedulePOST()
    {
        $callPath = "schedules/view";
        $programID = 73;

        $rotationsetID = 0; //temporarily set the rotationSetID to 0
        $years = self::academicYearPOST()->getBody(); //call academicYearPOST to get the rotationSetID argument
        $yearsArr = json_decode($years, true); //turn json into an array
        $date = date("Y-m-d");
        $date = date("Y-m-d", strtotime($date)); // get todays date
        for ($i = 0; $i < count($yearsArr); $i++) {
            $rotationsetID = $yearsArr[$i]["rotationsetID"];
            $startDate = date("Y-m-d", strtotime($yearsArr[$i]["start_date"])); //get the rotation start date from academicYearPOST
            $endDate = date("Y-m-d", strtotime($yearsArr[$i]["end_date"])); //get the rotation end date from academicYearPOST

            if (($date > $startDate) && ($date < $endDate)) { 
                // find the date range that fits today
                break; // break so we can save the rotationSetID
            }
        }
        $request = json_encode(["programID" => $programID, "rotationsetID" => $rotationsetID]); // setup the arguments properly
        return self::medhubPOST($callPath, $request);
    }

    // needs to be refactored still
    //post call for rotations (requires scheduleID & rotationSetID from schedulePOST, academicYearPOST)
    public function rotationsPOST()
    {
        $callPath = "schedules/rotations";
        $programID = 73;

        $rotationsetID = 0; //temporarily set the rotationSetID to 0
        $years = self::academicYearPOST()->getBody(); //call academicYearPOST to get the rotationSetID argument
        $yearsArr = json_decode($years, true); //turn json into an array
        $date = date("Y-m-d");
        $date = date("Y-m-d", strtotime($date)); // get todays date
        for ($i = 0; $i < count($yearsArr); $i++) {
            $rotationsetID = $yearsArr[$i]["rotationsetID"];
            $startDate = date("Y-m-d", strtotime($yearsArr[$i]["start_date"])); //get the rotation start date from academicYearPOST
            $endDate = date("Y-m-d", strtotime($yearsArr[$i]["end_date"])); //get the rotation end date from academicYearPOST

            if (($date > $startDate) && ($date < $endDate)) { 
                // find the date range that fits today
                break; // break so we can save the rotationSetID
            }
        }
        $schedule = self::schedulePOST($programID, $rotationsetID)->getBody(); // call schedule post
        $scheduleArr = json_decode($schedule, true);
        $scheduleID = $scheduleArr[0]["scheduleID"]; // get the scheduleID from the schedule post
        $request = json_encode(["scheduleID" => $scheduleID, "rotationsetID" => $rotationsetID]); // setup the arguments properly
        return self::medhubPOST($callPath, $request);
    }

    //post call for active residents
    public function activeResidentsPOST()
    {
        return self::medhubPOST("users/residents");
    }

    //post call for active faculty (not sure if needed since most numbers seem to match up already)
    public function activeFacultyPOST()
    {
        return self::medhubPOST("users/faculty");
    }

    // post call for academic year
    public function academicYearPOST()
    {
        return self::medhubPOST("schedules/years");
    }

    //post call for evaluationForms
    public function evaluationFormsPOST()
    {
        return self::medhubPOST("evals/forms");
    }

    //post call for evaluationTypes
    public function evaluationTypesPOST()
    {
        return self::medhubPOST("evals/types");
    }

    // API Test
    public function testPOST()
    {
        return self::medhubPOST("info/test");
    }
}
