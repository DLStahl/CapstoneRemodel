<?php
/**
 * Created by PhpStorm.
 * User: shaw
 * Date: 10/12/18
 * Time: 3:50 PM
 */

namespace App\Console\Commands;
use App\Status;
use App\AutoAssignment;
use Illuminate\Console\Command;
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

class InitiateEvals extends Command
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

	// initiate evaluation for attending to eval resident
    // returns responseID - unique evaluation identifier (value is 0 if initiation failed)
    public function initAttendingEvalResidentPOST($evaluatorID, $evaluateeID, $evalID)
    {
        $callPath = 'evals/initiate';
        $evalType = 5;
        $programID = 73;
        $notify = true;
        $request = json_encode(array('evaluationID' => $evalID,'evaluation_type' => $evalType, 'evaluator_userID' => $evaluatorID, 'programID' => $programID, 'evaluatee_userID' => $evaluateeID));

        return self::medhubPOST($callPath, $request);
    }

	// initiate evaluation for resident to eval attending
	// returns responseID - unique evaluation identifier (value is 0 if initiation failed)
    public function initResidentEvalAttendingPOST($evaluatorID, $evaluateeID)
    {
        $callPath = 'evals/initiate';
        $evalID = 1353; // 'Resident Evaluation of Faculty' (v.2)
        $evalType = 2;
        $programID = 73;
        $notify = true;
        $request = json_encode(array('evaluationID' => $evalID,'evaluation_type' => array($evalType), 'evaluator_userID' => $evaluatorID, 'programID' => $programID, 'evaluatee_userID' => $evaluateeID));		
		return self::medhubPOST($callPath, $request);
    }
	
	public function initiateEvaluations()
	{
		$date = date('Y-m-d');
		$newdate = strtotime ( '-1 day' , strtotime ( $date ) ) ;
		$newdate = date ( 'Y-m-d' , $newdate );
		
		$residentID = DB::table('evaluation_data')->where('date', $newdate)->pluck('resident');
		$attendingName = DB::table('evaluation_data')->where('date', $newdate)->pluck('attending');	
		$evalID = DB::table('evaluation_data')->where('date', $newdate)->pluck('id');			
		
		$evalSent = 0; 
		for($i = 0; $i<count($residentID); $i++)
		{	
			
			
			$evaluateeName = $residentID[$i];
			
			print($evaluateeName.' '.$attendingName[$i]."\n");
			
			// piece together the name to match the way they are stored in th medhub system
			$rName = $evaluateeName;
			$pieces = explode(" ", $rName);
			if(count($pieces) == 3 && $pieces[1] != "Arce" && $pieces[1] != "Puertas" && $pieces[1] != "Zuleta")
			{
				$first = $pieces[0];
				$last = $pieces[2];
				$rName = $first.' '.$last;
			}
			elseif(count($pieces) == 4) 
			{
				$first = $pieces[0];
				$last = $pieces[2].' '.$pieces[3];
				$rName = $first.' '.$last;
			}
			elseif(count($pieces) == 5) 
			{
				$first = $pieces[0];
				$last = $pieces[2].' '.$pieces[3].' '.$pieces[4];
				$rName = $first.' '.$last;
			}
			
			$evaluateeID = Resident::where('name', $rName)->value('medhubId');
			
			// grab all of the start and end dates for the resident
			$evaluateeRotationsStart = Rotations::where('Name', $evaluateeName)->pluck('Start');
			$evaluateeRotationsEnd = Rotations::where('Name', $evaluateeName)->pluck('End');	

			// save todays date	
			$date = date('Y-m-d');
			$date=date('Y-m-d', strtotime($date));
			// temporary service variable
			$evaluateeService = 0; 
			// loop through all the residents start/end dates
			for ($j = 0; $j < count($evaluateeRotationsStart); $j++) {
				$startDate = date('Y-m-d', strtotime($evaluateeRotationsStart[$j])); //get the  start date 
				$endDate = date('Y-m-d', strtotime($evaluateeRotationsEnd[$j])); //get the  end date 
				
				if (($date> $startDate) && ($date<$endDate)){ // find the date range that fits today
					$evaluateeService = Rotations::where('Name', $evaluateeName)->where('Start', $evaluateeRotationsStart[$j])->value('Service');
					break; // break so we can save the evaluateeService
				}
			}
			
			//check if we even need to send an eval ( 0 for the service means we dont send it)
			if($evaluateeService == 0)
			{
				$evalSent++;
				// do nothing
			}
			else
			{
				// grab the attending id
				$aName = $attendingName[$i];
				$pieces = explode(" ", $aName);
				
				if(count($pieces) == 3 && $pieces[1] != "Del")
				{
					$first = $pieces[0];
					$last = $pieces[2];
					$aName = $first.' '.$last;
				}
				elseif(count($pieces) == 4) 
				{
					$first = $pieces[0];
					$last = $pieces[2].' '.$pieces[3];
					$aName = $first.' '.$last;
				}
				elseif(count($pieces) == 5) 
				{
					$first = $pieces[0];
					$last = $pieces[2].' '.$pieces[3].' '.$pieces[4];
					$aName = $first.' '.$last;
				}
				$evaluatorID = Attending::where('name', $aName)->value('id');
				
				
				// both the resident and attending are valid
				if($evaluateeID != null && $evaluatorID != null)
				{
					$evalSent++;
					try {
						self::initAttendingEvalResidentPOST($evaluatorID, $evaluateeID, $evaluateeService);
					}
					catch (\Exception $e){
						$evalSent--;
						Log::debug('Error on Attending Eval Resident. Eval ID'.$evalID[$i].' Resident Name: '.$evaluateeName.' Attending ID '.$evaluatorID.'  Attending Name '.$aName.' Service ID'.$evaluateeService);
					}
					try {
						self::initResidentEvalAttendingPOST($evaluateeID, $evaluatorID);
					}
					catch (\Exception $e){
						$evalSent--;
						Log::debug('Error on Resident Eval Attending. Eval ID'.$evalID[$i].' Attending ID: '.$evaluatorID.' Resident Name '.$evaluateeName.' Attending Name'.$aName);
					}
				}
			}	
			// either the resident of the attending werent valid, mark it down -- used for testing only
			if($evalSent == 0)
			{
				//print($evalID[$i].' '.$evaluateeID.' '.$evaluatorID);
			}					
		}
		Log::debug($evalSent);
		
	}
	 
    public function handle()
    {
        self::initiateEvaluations();
    }


}
