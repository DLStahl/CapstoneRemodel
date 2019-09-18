<?php

namespace App\Console\Commands;
use App\Status;
use App\AutoAssignment;
use App\Assignment;
use App\Option;
use App\Resident;
use App\ScheduleData;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Google_Client;
use Google_Service_Drive;
use Google_Service_Sheets;
use Google_Service_Sheets_ValueRange;
use Google_Service_Sheets_BatchUpdateSpreadsheetRequest;

 require __DIR__ . '/../../../../google/vendor/autoload.php';
        /**
         * Returns an authorized API client.
         * @return Google_Client the authorized client object
         */


class PushSchedule extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'pushAPI';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'push API';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

	public static function updateOption($date)
    {
        $dir = "/usr/local/webs/remodel.anesthesiology/htdocs/downloads/assignment".$date.".csv";
        $fp = null;

        if (file_exists($dir)) {
            $fp = fopen($dir, 'w');
        } else {
            $fp = fopen($dir, 'c');
        }

       //MEGAN CHANGE
        fputcsv($fp, array('date', 'room', 'case procedure', 'start time', 'end time',
                            'lead surgeon', 'resident', 'preference', 'milestones', 'objectives'));
        $options = null;
        if ($date == null) {
            $options = Assignment::orderBy('date', 'desc')->get();
        }
        else {
            $options = Assignment::where('date', $date)->get();
        }

        foreach ($options as $option)
        {
            $schedule_id = $option['schedule'];
            $resident_id = $option['resident'];
			$schedule_id = $option['schedule'];
            $date = $option['date'];

            $room = ScheduleData::where('id', $schedule_id)->value('room');
            //MEGAN CHANGE
            $case_procedure = ScheduleData::where('id', $schedule_id)->value('case_procedure');
            $start_time = ScheduleData::where('id', $schedule_id)->value('start_time');
            $end_time = ScheduleData::where('id', $schedule_id)->value('end_time');
            $lead_surgeon = ScheduleData::where('id', $schedule_id)->value('lead_surgeon');
            $resident = Resident::where('id', $resident_id)->value('name');


            $preference = $option['preference'];
            $milestones = $option['milestones'];
            $objectives = $option['objectives'];

            //MEGAN CHANGE
            fputcsv($fp, array($date, $room, $case_procedure, $start_time, $end_time,
                            $lead_surgeon, $resident, $preference, $milestones, $objectives));
        }
        fclose($fp);
    }

	/**
	* Returns an authorized API client.
	* @return Google_Client the authorized client object
	*/
	public function getClient()
	{
		$client = new Google_Client();
		$client->setApplicationName('REMODEL');
		$client->setScopes(Google_Service_Sheets::DRIVE);
		$client->addScope(Google_Service_Sheets::DRIVE_FILE);
		$client->addScope(Google_Service_Sheets::SPREADSHEETS);
		$client->setAuthConfig('/usr/local/webs/remodel.anesthesiology/htdocs/REMODEL-0dfb917af5de.json');

		// Load previously authorized token from a file, if it exists.
		$tokenPath = '/htdocs/token.json';
		if (file_exists($tokenPath)) {
			$accessToken = json_decode(file_get_contents($tokenPath), true);
			$client->setAccessToken($accessToken);
		}
		return $client;
	}

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {

        // connect to the api and our sheet
        $client = self::getClient();
        $service = new Google_Service_Sheets($client);
        $spreadsheetId = '1npNBs_j6BvmZO29GHlEJ-mROGhtBEqM7_KNKdAnNLxY';
        $title = date("Y-m-d");
		$index = 0;

        // create new sheet for today
        $newSheet = new Google_Service_Sheets_BatchUpdateSpreadsheetRequest(array(
                'requests' => array(
                    'addSheet' => array(
                        'properties' => array(
                            'title' => $title,
							'index' => $index
                        )
                    )
                )
            ));
        $service->spreadsheets->batchUpdate('1npNBs_j6BvmZO29GHlEJ-mROGhtBEqM7_KNKdAnNLxY', $newSheet);

        // setup today's sheet to be ready to be added to
        $spreadsheetId = '1npNBs_j6BvmZO29GHlEJ-mROGhtBEqM7_KNKdAnNLxY';
        $title = '\''.$title.'\'!';
        $range = $title.'A1:K15';

		//update the values in the options sheet
		$date = date("Y-m-d");
		self::updateOption($date);

        // get the values from the options file and save them to an array
		$path = "/usr/local/webs/remodel.anesthesiology/htdocs/downloads/assignment".$date.".csv";
        $file = fopen($path, 'r');
        $csv = array();
        while (($line = fgetcsv($file)) !== FALSE) {
            //$line is an array of the csv elements
            $csv[] = $line;
        }
        fclose($file);

        // create the correct update to send back to google sheets to fill the sheet with the array created above
        $body = new Google_Service_Sheets_ValueRange([
            'values' => $csv
        ]);
        $params = [
            'valueInputOption' => 'USER_ENTERED'
        ];
        $result = $service->spreadsheets_values->update($spreadsheetId, $range, $body, $params);


		$response = $service->spreadsheets->get($spreadsheetId);

		if(count($response) > 30)
		{
			$lastEntry =  $response[count($response)-1];

			$properties = $lastEntry['properties'];

			$sheetId = $properties['sheetId'];

			$delete = new Google_Service_Sheets_BatchUpdateSpreadsheetRequest(array(
				'requests' => array(
					'deleteSheet' => array(
							'sheetId' => $sheetId
					)
				)
			));
			$service->spreadsheets->batchUpdate('1npNBs_j6BvmZO29GHlEJ-mROGhtBEqM7_KNKdAnNLxY', $delete);
		}

    }


}
