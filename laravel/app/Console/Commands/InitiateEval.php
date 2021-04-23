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
use App\Models\Resident;

class InitiateEval extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'initiateEvals';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'initiate evals';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        self::initiateEvaluations();
    }

    // request format: json_encode(array('programID' => 73));
    // or just a JSON encoded string: '{"programID":73}'
    public function medhubPOST($callPath, $request = '{"programID":73}')
    {
        $client = new Client([
            'base_uri' => 'https://osu.medhub.com/functions/api/',
        ]);
        $clientID = '5006';
        $privateKey = '331xyg1hl65o';
        $time = time();

        Log::info($request);
        return $client->request('POST', $callPath, [
            'form_params' => [
                'clientID' => $clientID,
                'ts' => $time,
                'type' => 'json',
                'request' => $request,
                'verify' => hash('sha256', "$clientID|$time|$privateKey|$request"),
            ],
        ]);
    }

    // initiate evaluation for attending to eval resident
    // returns responseID - unique evaluation identifier (value is 0 if initiation failed)
    public function initAttendingEvalResidentPOST($evaluatorID, $evaluateeID, $evalID)
    {
        $callPath = 'evals/initiate';
        $evalType = 5;
        $programID = 73;
        $notify = true;
        $request = json_encode([
            'evaluationID' => $evalID,
            'eval_type' => $evalType,
            'evaluator_userID' => $evaluatorID,
            'programID' => $programID,
            'evaluatee_userID' => intval($evaluateeID),
            'notify' => $notify,
        ]);
        return self::medhubPOST($callPath, $request);
    }

    // initiate evaluation for resident to eval attending
    // returns responseID - unique evaluation identifier (value is 0 if initiation failed)
    public function initResidentEvalAttendingPOST($evaluatorID, $evaluateeID)
    {
        $callPath = 'evals/initiate';
        $evalID = EvaluationForms::where('form_type', 'resident evaluation of faculty')->value('medhub_form_id');
        $evalType = 2;
        $programID = 73;
        $notify = true;
        $request = json_encode([
            'evaluationID' => $evalID,
            'eval_type' => $evalType,
            'evaluator_userID' => intval($evaluatorID),
            'programID' => $programID,
            'evaluatee_userID' => $evaluateeID,
            'notify' => $notify,
        ]);
        return self::medhubPOST($callPath, $request);
    }

    // send Medhub Evaluations for a resident and attending that worked together
    public function sendEvaluations($residentID, $residentName, $medhubFormId, $attendingID, $attendingName, $date)
    {
        $evalsSent = 0;
        $residentMedhubID = intval(Resident::where('id', $residentID)->value('medhubId'));
        // send attending an evaluation for a resident
        try {
            self::initAttendingEvalResidentPOST($attendingID, $residentMedhubID, $medhubFormId);
            $evalsSent++;
        } catch (\Exception $e) {
            Log::debug(
                "Failed to send evaluation: Attending Evaluating Resident. Resident: $residentName Resident ID: $residentID Attending: $attendingName Attending ID: $attendingID Medhub Form ID: $medhubFormId"
            );
            Log::debug('Associated Exception code: ' . $e->getCode() . ' Exception Message: ' . $e->getMessage());
        }
        // send resident an evaluation for an attending
        try {
            self::initResidentEvalAttendingPOST($residentMedhubID, $attendingID);
            $evalsSent++;
        } catch (\Exception $e) {
            Log::debug(
                "Failed to send evaluation: Resident Evaluating Resident. Attending name: $attendingName Attending ID: $attendingID Resident Name: $residentName Resident ID: $residentID"
            );
            Log::debug('Associated Exception code: ' . $e->getCode() . ' Exception Message: ' . $e->getMessage());
        }

        // if resident used REMODEL, fire off additional evaluation
        $hasOptions = Option::where('resident', $residentID)
            ->where('date', $date)
            ->get();
        if (count($hasOptions) > 0) {
            try {
                $additionalFormType = 'REMODEL feedback';
                $additionalService = EvaluationForms::where('form_type', $additionalFormType)->value('medhub_form_id');
                self::initAttendingEvalResidentPOST($attendingID, $residentMedhubID, $additionalService);
                $evalsSent++;
            } catch (\Exception $e) {
                Log::debug(
                    "Failed to send evaluation: REMODEL Evaluation. Resident Name: $residentName Resident ID: $residentID Attending Name: $attendingName Attending ID: $attendingID Medhub form ID: $medhubFormId"
                );
                Log::debug('Associated Exception code: ' . $e->getCode() . ' Exception Message: ' . $e->getMessage());
            }
        }
        return $evalsSent;
    }

    // send evaluations for resident and attending pairs in the evaluation_data table with the date of yesterday
    public function initiateEvaluations()
    {
        $yesterday = date('Y-m-d', strtotime('yesterday'));
        Log::info("initiate evaluations for $yesterday evaluation data");

        $alwaysEvalServices = ['1', '2', '21', '23']; // hardcoded for now - needs to be changed when db updated
        $totalEvalsSent = 0;
        $time_difference = intval(
            DB::table('variables')
                ->where('name', 'time_before_attending_evaluates_resident')
                ->value('value')
        );
        $residentAndAttendingData = self::getResidentAndAttendingEvalData($yesterday);
        // for each Resident/Attending pair in the evaluation_data table - send eval if necessary
        foreach ($residentAndAttendingData as $residentAttendingEvalData) {
            if (
                !is_null($residentAttendingEvalData['residentID']) &&
                !is_null($residentAttendingEvalData['attendingID'])
            ) {
                // get medhub form id for resident's active service
                $medhubFormId = EvaluationForms::where(
                    'id',
                    $residentAttendingEvalData['residentActiveService']
                )->value('medhub_form_id');
                if (in_array($residentAttendingEvalData['residentActiveService'], $alwaysEvalServices)) {
                    Log::info(
                        'Resident: ' .
                            $residentAttendingEvalData['residentName'] .
                            ' Active Service: ' .
                            $residentAttendingEvalData['residentActiveService'] .
                            ' is special. Attending ' .
                            $residentAttendingEvalData['attendingName']
                    );
                    $totalEvalsSent += self::sendEvaluations(
                        $residentAttendingEvalData['residentID'],
                        $residentAttendingEvalData['residentName'],
                        $medhubFormId,
                        $residentAttendingEvalData['attendingID'],
                        $residentAttendingEvalData['attendingName'],
                        $yesterday
                    );
                } elseif ($medhubFormId == 0) {
                    Log::debug(
                        'Medhub Form ID is 0 for Resident ' .
                            $residentAttendingEvalData['residentName'] .
                            "'s active service " .
                            $residentAttendingEvalData['residentActiveService']
                    );
                } else {
                    if ($residentAttendingEvalData['overallTime'] >= $time_difference) {
                        Log::info(
                            'Resident/Attending Pair send evaluation due to overall time. Resident: ' .
                                $residentAttendingEvalData['residentName'] .
                                ' Attending: ' .
                                $residentAttendingEvalData['attendingName'] .
                                ' Time: ' .
                                $residentAttendingEvalData['overallTime'] .
                                ' >= ' .
                                $time_difference
                        );
                        $totalEvalsSent += self::sendEvaluations(
                            $residentAttendingEvalData['residentID'],
                            $residentAttendingEvalData['residentName'],
                            $medhubFormId,
                            $residentAttendingEvalData['attendingID'],
                            $residentAttendingEvalData['attendingName'],
                            $yesterday
                        );
                    }
                }
            } else {
                Log::debug(
                    'Cannot initiate evaluation because no resident ID for ' .
                        $residentAttendingEvalData['residentName'] .
                        ' or no attending ID for ' .
                        $residentAttendingEvalData['attendingName'] .
                        ' can be found.'
                );
            }
        }
        Log::debug("Total Number of Evaluations Succesfully sent: $totalEvalsSent");
    }

    // return Resident and Attending pairs with total time worked together for a given date in the evaluation_data table
    public function getResidentAndAttendingEvalData($date)
    {
        $residentAndAttendingData = [];
        //get residents that have evaluation data in DB on day
        $residents = EvaluateData::where('date', $date)
            ->pluck('rId', 'resident')
            ->unique();
        Log::info("Residents in Evaluation Data with date $date: $residents");
        // for each resident get their active rotation service and create pairs of residents and attendings that worked together
        foreach ($residents as $residentName => $residentID) {
            //get Resident's active rotation service
            $activeService = 0;
            $activeRotation = Rotations::where('name', $residentName)
                ->where('Start', '<=', $date)
                ->where('End', '>=', $date)
                ->first();
            if (is_null($activeRotation)) {
                Log::info("Could not find an active rotation for Resident $residentName ID: $residentID");
            } else {
                $activeService = $activeRotation['Service'];
            }
            Log::info("Resident $residentName has active service id $activeService");
            $attendings = EvaluateData::where('date', $date)
                ->where('rId', $residentID)
                ->pluck('aId', 'attending')
                ->unique();
            foreach ($attendings as $attendingName => $attendingID) {
                $overallTime = EvaluateData::where('date', $date)
                    ->where('rId', $residentID)
                    ->where('aId', $attendingID)
                    ->sum('diff');
                Log::info(
                    "Resident: $residentName ($residentID) Attending: $attendingName ($attendingID) Total Time Together: $overallTime"
                );
                $residentAndAttendingEvalData = [
                    'residentName' => $residentName,
                    'residentID' => $residentID,
                    'residentActiveService' => $activeService,
                    'attendingID' => $attendingID,
                    'attendingName' => $attendingName,
                    'overallTime' => $overallTime,
                ];
                array_push($residentAndAttendingData, $residentAndAttendingEvalData);
            }
        }
        return $residentAndAttendingData;
    }
}
