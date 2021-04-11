<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

use Illuminate\Support\Facades\DB;
use GuzzleHttp\Client;
use App\Models\Resident;
use App\Models\Rotations;
use App\Models\EvaluationForms;

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
            'base_uri' => 'https://osu.medhub.com/functions/api/'
        ]);
        $clientID='5006';
        $privateKey='331xyg1hl65o';
        $time = time();

        Log::info($request);
        return $client->request('POST', $callPath, [
            'form_params' => [
                'clientID' => $clientID,
                'ts' => $time,
                'type' => 'json',
                'request' => $request,
                'verify' => hash('sha256', "$clientID|$time|$privateKey|$request")
            ]
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
        $request = json_encode(array('evaluationID' => $evalID,'eval_type' => $evalType,'evaluator_userID' => $evaluatorID,'programID' => $programID,'evaluatee_userID' => intval($evaluateeID), 'notify' => $notify));
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
        $request = json_encode(array('evaluationID' => $evalID,'eval_type' => $evalType,'evaluator_userID' => intval($evaluatorID),'programID' => $programID,'evaluatee_userID' => $evaluateeID, 'notify' => $notify));
		return self::medhubPOST($callPath, $request);
    }
	
	public function initiateEvaluations()
	{
        Log::info("initiate evaluations");
		$date = date('Y-m-d');
		$yesterday = strtotime ( '-1 day' , strtotime ( $date ) ) ;
		$yesterday = date ( 'Y-m-d' , $yesterday );
		
		$residentId = DB::table('evaluation_data')->where('date', $yesterday)->pluck('resident_id');
		$attendingId = DB::table('evaluation_data')->where('date', $yesterday)->pluck('attending_id');	
		$evalID = DB::table('evaluation_data')->where('date', $yesterday)->pluck('id');			
		
        Log::info(sizeof($residentId).' residents: '.$residentId);
        Log::info(sizeof($attendingId).' attendings: '.$attendingId);
        Log::info(sizeof($evalID).' evaluation data: '.$evalID);
		$evalSent = 0; 
		for($i = 0; $i<count($residentId); $i++)
		{	
			$singleResidentID = $residentId[$i];
			$evaluateeID = Resident::where('id', $singleResidentID)->value('medhubId');
			$evaluateeName = Resident::where('id', $singleResidentID)->value('name');
			Log::info("resident medhub ID: ".$evaluateeID);

			// grab all of the start and end dates for the resident
			$evaluateeRotationsStart = Rotations::where('Name', $evaluateeName)->pluck('Start');
			$evaluateeRotationsEnd = Rotations::where('Name', $evaluateeName)->pluck('End');	

			// temporary service variable
			$evaluateeService = 0; 
			// loop through all the residents start/end dates
			for ($j = 0; $j < count($evaluateeRotationsStart); $j++) {
				$startDate = date('Y-m-d', strtotime($evaluateeRotationsStart[$j])); //get the  start date 
				$endDate = date('Y-m-d', strtotime($evaluateeRotationsEnd[$j])); //get the  end date 
				
				if (($yesterday>= $startDate) && ($yesterday<=$endDate)){ // find the date range that fits today
					$evaluationFormsId = Rotations::where('Name', $evaluateeName)->where('Start', $evaluateeRotationsStart[$j])->value('Service');
					$evaluateeService = EvaluationForms::where('id', $evaluationFormsId)->value('medhub_form_id');
					break; // break so we can save the evaluateeService
				}
			}
			Log::info('service #'.$evaluateeService);
			
			//check if we even need to send an eval ( 0 for the service means we dont send it)
			if($evaluateeService == 0)
			{
				// $evalSent++;
				// do nothing
				Log::info('No valid serviceID found for Resident Name:'.$evaluateeName.' Resident ID '.$evaluateeID);
			}
			else
			{
				// grab the attending id
				$evaluatorID = $attendingId[$i];
				Log::info("attending medhub ID: ".$evaluatorID);
				
				// both the resident and attending are valid
				if($evaluateeID != null && $evaluatorID != null)
				{
					$evalSent = 0;
					try {
						self::initAttendingEvalResidentPOST($evaluatorID, $evaluateeID, $evaluateeService);
						$evalSent++;
					}
					catch (\Exception $e){
						Log::debug('Error on Attending Eval Resident. Eval ID'.$evalID[$i].' Resident Name: '.$evaluateeName.' Resident ID '.$evaluateeID.' Attending ID '.$evaluatorID.' Service ID'.$evaluateeService);
					}
					try {
						self::initResidentEvalAttendingPOST($evaluateeID, $evaluatorID);
						$evalSent++;
					}
					catch (\Exception $e){
						Log::debug('Error on Resident Eval Attending. Eval ID'.$evalID[$i].' Attending ID: '.$evaluatorID.'  Resident Name '.$evaluateeName.' Resident ID '.$evaluateeID);
					}
					
					// if used REMODEL, fire off additional evaluation
					$hasOptions = DB::table('option')->where('resident', $evaluateeID)->where('date', $yesterday)->value('date');
					if ($hasOptions != null) {
						try {
							$additionalFormType = 'REMODEL feedback';
							$additionalService = EvaluationForms::where('form_type', $additionalFormType)->value('medhub_form_id'); 
							self::initAttendingEvalResidentPOST($evaluatorID, $evaluateeID, $additionalService);
							$evalSent++;
						}
						catch (\Exception $e){
							Log::debug('Error on Attending Eval Resident. Eval ID'.$evalID[$i].' Resident Name: '.$evaluateeName.' Resident ID '.$evaluateeID.' Attending ID '.$evaluatorID.' Service ID'.$evaluateeService);
						}
				}
					
				}else {
					Log::info('Cannot initiate evaluations because no resident ID or no attending ID can be found. Eval ID'.$evalID[$i].' Resident Name: '.$evaluateeName.' Resident ID '.$evaluateeID.' Attending ID '.$evaluatorID.' Service ID '.$evaluateeService);
				}
			}	
			// either the resident of the attending werent valid, mark it down -- used for testing only
			if($evalSent == 0)
			{
				//print($evalID[$i].' '.$evaluateeID.' '.$evaluatorID);
			}					
		}
		Log::debug($evalSent. ' evaluations sent');
		
	}
	 
    public function handle()
    {
        self::initiateEvaluations();
    }


}
