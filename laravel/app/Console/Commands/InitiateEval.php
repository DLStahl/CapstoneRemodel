<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

use Illuminate\Support\Facades\DB;
use GuzzleHttp\Client;
use App\Models\Rotations;
use App\Models\EvaluationForms;
use App\Models\EvaluateData;
use App\Models\Option;

class InitiateEval extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = "initiateEvals";

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = "initiate evals";

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */

    // request format: json_encode(array('programID' => 73));
    // or just a JSON encoded string: '{"programID":73}'
    public function medhubPOST($callPath, $request = '{"programID":73}')
    {
        $client = new Client([
            "base_uri" => "https://osu.medhub.com/functions/api/"
        ]);
        $clientID = "5006";
        $privateKey = "331xyg1hl65o";
        $time = time();

        Log::info($request);
        return $client->request("POST", $callPath, [
            "form_params" => [
                "clientID" => $clientID,
                "ts" => $time,
                "type" => "json",
                "request" => $request,
                "verify" => hash("sha256", "$clientID|$time|$privateKey|$request"),
            ],
        ]); 
    }

    // initiate evaluation for attending to eval resident
    // returns responseID - unique evaluation identifier (value is 0 if initiation failed)
    public function initAttendingEvalResidentPOST($evaluatorID, $evaluateeID, $evalID)
    {
        $callPath = "evals/initiate";
        $evalType = 5;
        $programID = 73;
        $notify = true;
        $request = json_encode([
            "evaluationID" => $evalID, 
            "eval_type" => $evalType, 
            "evaluator_userID" => $evaluatorID, 
            "programID" => $programID, 
            "evaluatee_userID" => intval($evaluateeID), 
            "notify" => $notify
        ]);
        return self::medhubPOST($callPath, $request);
    }

    // initiate evaluation for resident to eval attending
    // returns responseID - unique evaluation identifier (value is 0 if initiation failed)
    public function initResidentEvalAttendingPOST($evaluatorID, $evaluateeID)
    {
        $callPath = "evals/initiate";
        $evalID = EvaluationForms::where("form_type", "resident evaluation of faculty")->value("medhub_form_id");
        $evalType = 2;
        $programID = 73;
        $notify = true;
        $request = json_encode([
            "evaluationID" => $evalID, 
            "eval_type" => $evalType, 
            "evaluator_userID" => intval($evaluatorID), 
            "programID" => $programID, 
            "evaluatee_userID" => $evaluateeID, 
            "notify" => $notify
        ]);
        return self::medhubPOST($callPath, $request);
    }

    public function sendEvaluations($residentID, $residentName, $medhubFormId, $attendingID, $attendingName, $date)
    {
        $evalsSent = 0;
        try {
            $responseID = self::initAttendingEvalResidentPOST($attendingID, $residentID, $medhubFormId);
            if ($responseID != 0) {
                $evalsSent++;
            }
        } catch (\Exception $e) {
            Log::debug("Failed to send evaluation: Attending Evaluating Resident. Resident: " . $residentName . "Resident ID: " . $residentID . " Attending: " . $attendingName . " Attending ID " . $attendingID . " Medhub Form ID: " . $medhubFormId);
            Log::debug("Associated Exception code: " . $e->getCode() . " Exception Message: " . $e->getMessage());
        }
        try {
            $responseID = self::initResidentEvalAttendingPOST($residentID, $attendingID);
            if ($responseID != 0) {
                $evalsSent++;
            }
        } catch (\Exception $e) {
            Log::debug("Failed to send evaluation: Resident Evaluating Resident. Attending name: " . $attendingName . " Attending ID: " . $attendingID . "  Resident Name " . $residentName . " Resident ID " . $residentID);
            Log::debug("Associated Exception code: " . $e->getCode() . " Exception Message: " . $e->getMessage());
        }

        // if used REMODEL, fire off additional evaluation
        $hasOptions = Option::where("resident", $residentID)->where("date", $date)->get();
        if (!is_null($hasOptions) && count($hasOptions) > 0) {
            try {
                $additionalFormType = "REMODEL feedback";
                $additionalService = EvaluationForms::where("form_type", $additionalFormType)->value("medhub_form_id");
                $responseID = self::initAttendingEvalResidentPOST($attendingID, $residentID, $additionalService);
                if ($responseID != 0) {
                    $evalsSent++;
                }
            } catch (\Exception $e) {
                Log::debug("Failed to send evaluation: REMODEL Evaluation.  Resident Name: " . $residentName . " Resident ID: " . $residentID . " Attending Name: " . $attendingName . " Attending ID: " . $attendingID . " Medhub form ID: " . $medhubFormId);
                Log::debug("Associated Exception code: " . $e->getCode() . " Exception Message: " . $e->getMessage());
            }
        }
        return $evalsSent;
    }

    public function initiateEvaluations()
    {
        $date = date("Y-m-d");
        $date = strtotime("-1 day", strtotime($date));
        $date = date("Y-m-d", $date);
        Log::info("initiate evaluations for " . $date . " evaluation data");

        $alwaysEvalServices = ["1", "2", "21", "23"]; // hardcoded for now - needs to be changed when db updated
        $totalEvalsSent = 0;
        $time_difference = DB::table("variables")->where("name", "time_before_attending_evaluates_resident")->value("value");
        $time_difference = (int)$time_difference;
        $pairs = self::getResidentAttendingPairs($date);
        // for each Resident/Attending pair - send eval if necessary
        foreach ($pairs as $pair) {
            if (!is_null($pair["residentActiveService"]) && !is_null($pair["residentActiveService"])) {
                // get medhub form id for resident's active service
                $medhubFormId = EvaluationForms::where("id", $pair["residentActiveService"])->value("medhub_form_id");
                if (in_array($pair["residentActiveService"], $alwaysEvalServices)) {
                    Log::info("Resident: " .  $pair["residentName"] . " Active Service: " . $pair["residentActiveService"] . " is special. Attending " . $pair["attendingName"]);
                    $totalEvalsSent += self::sendEvaluations($pair["residentID"], $pair["residentName"], $medhubFormId, $pair["attendingID"], $pair["attendingName"], $date);
                } else if ($medhubFormId == 0) {
                    Log::debug("Medhub Form ID is 0 for Resident " . $pair["residentName"] . "'s active service " . $pair["residentActiveService"]);
                } else {
                    if ($pair["overallTime"] >= $time_difference) {
                        Log::info("Resident/Attending Pair send evaluation due to overall time. Resident: " . $pair["residentName"] . " Attending: " . $pair["attendingName"] . " Time: " . $pair["overallTime"] . " >= " . $time_difference);
                        $totalEvalsSent +=  self::sendEvaluations($pair["residentID"], $pair["residentName"], $medhubFormId, $pair["attendingID"], $pair["attendingName"], $date);
                    }
                }
            } else {
                Log::debug("Cannot initiate evaluation because no resident ID for " . $pair["residentName"] . " or no attending ID for " . $pair["attendingName"] . " can be found.");
            }
        }
        Log::debug("Total Number of Evaluations Succesfully sent: " . $totalEvalsSent);
    }

    public function getResidentAttendingPairs($date)
    {
        $pairs = array();
        //get residents that have evaluation data in DB on day
        $residents = EvaluateData::where("date", $date)->pluck("resident_id", "resident");
        Log::info("Residents in Evaluation Data with date " . $date . ":  " . $residents);
        // for each resident get their active rotation service and create pairs of residents and attendings that worked together
        foreach ($residents as $residentName => $residentID) {
            //get Resident's active rotation service
            $activeService = 0;
            $activeRotation = Rotations::where("name", $residentName)->where("Start", "<=", $date)->where("End", ">=", $date)->first();
            if (is_null($activeRotation)) {
                Log::info("Could not find an active rotation for Resident " . $residentName . " ID: " . $residentID);
            } else {
                $activeService = $activeRotation["Service"];
            }
            Log::info("Resident " . $residentName . " has active service id " . $activeService);
            $attendings = EvaluateData::where("date", $date)->where("resident_id", $residentID)->pluck("attending_id", "attending")->unique();
            foreach ($attendings as $attendingName => $attendingID) {
                $overallTime = EvaluateData::where("date", $date)->where("resident_id", $residentID)->where("attending_id", $attendingID)->sum("time_with_attending");
                Log::info("Resident: " . $residentName . "(" . $residentID . ") Attending: " . $attendingName . "(" . $attendingID . ") Total Time Together: " . $overallTime);
                $residentAndAttending = array(
                    "residentName" => $residentName,
                    "residentID" => $residentID,
                    "residentActiveService" => $activeService,
                    "attendingID" => $attendingID,
                    "attendingName" => $attendingName,
                    "overallTime" => $overallTime,
                );
                array_push($pairs, $residentAndAttending);
            }
        }
        return $pairs;
    }

    public function handle()
    {
        self::initiateEvaluations();
    }
}
