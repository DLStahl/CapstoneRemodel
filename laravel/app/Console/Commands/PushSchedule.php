<?php

namespace App\Console\Commands;

use App\Models\Assignment;
use App\Models\Option;
use App\Models\Resident;
use App\Models\ScheduleData;
use App\Models\Milestone;
use App\Models\Anesthesiologist;
use Illuminate\Console\Command;
use Google_Client;
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

    /*Takes the date of the assignments as a parameter.
    Returns a nested array with all array elements being data
    from that day's assignments to be printed to the Google Sheet
	*/
    public static function updateAssignments($date)
    {
        $all_assignments = [];
        $assignments = ($date == null ? Assignment::orderBy('date', 'desc') : Assignment::where('date', $date))->get();

        //find all relavant data for the assignments
        foreach ($assignments as $assignment) {
            $resident_id = $assignment['resident'];
            $schedule_id = $assignment['schedule'];

            $date = $assignment['date'];
            $room = ScheduleData::where('id', $schedule_id)->value('room');
            $case_procedure = ScheduleData::where('id', $schedule_id)->value('case_procedure');
            $start_time = ScheduleData::where('id', $schedule_id)->value('start_time');
            $end_time = ScheduleData::where('id', $schedule_id)->value('end_time');
            $lead_surgeon = ScheduleData::where('id', $schedule_id)->value('lead_surgeon');
            $resident = Resident::where('id', $resident_id)->value('name');
            $option_id = $assignment['option'];
            $milestone_id = Option::where('id', $option_id)->value('milestones');
            $preference = Option::where('id', $option_id)->value('option');
            $milestones = Milestone::where('id', $milestone_id)->value('category');
            $objectives = Option::where('id', $option_id)->value('objectives');
            $pref_anest_id = $assignment['anesthesiologist_id'];
            if ($pref_anest_id != null) {
                $anest = Anesthesiologist::where('id', $pref_anest_id);
                $fname = $anest->value('first_name');
                $lname = $anest->value('last_name');
                $pref_anest_name = "$fname $lname";
                $pref_anest_staff_key = $anest->value('staff_key');
            } else {
                $pref_anest_name = 'No anesthesiologist assignment';
                $pref_anest_staff_key = null;
            }

            $cur_assignment = [
                $date,
                $room,
                $case_procedure,
                $start_time,
                $end_time,
                $lead_surgeon,
                $resident,
                $preference,
                $milestones,
                $objectives,
                $pref_anest_staff_key,
                $pref_anest_name,
            ];

            array_push($all_assignments, $cur_assignment);
        }
        return $all_assignments;
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
        $title = date('Y-m-d', strtotime('+1 day'));
        $index = 0;

        // create new sheet for today
        $newSheet = new Google_Service_Sheets_BatchUpdateSpreadsheetRequest([
            'requests' => [
                'addSheet' => [
                    'properties' => [
                        'title' => $title,
                        'index' => $index,
                    ],
                ],
            ],
        ]);
        $service->spreadsheets->batchUpdate($spreadsheetId, $newSheet);

        // setup today's sheet to be ready to be added to
        $title = "\'" . $title . "\'!";

        $date = date('Y-m-d', strtotime('+1 day'));

        //Relative path for assignment sheet
        $dir = '../downloads/assignment' . $date . '.csv';

        $mode = file_exists($dir) ? 'w' : 'c';
        $fp = fopen($dir, $mode);

        //print header for sheet
        fputcsv($fp, [
            'date',
            'room',
            'case procedure',
            'start time',
            'end time',
            'lead surgeon',
            'resident',
            'preference',
            'milestones',
            'objectives',
            'anest staff key',
            'anest name',
        ]);

        //Get the array that contains all assigments for day + 1.
        $all_assns = self::updateAssignments($date);

        //print all assignments to path
        foreach ($all_assns as $assignemnts) {
            fputcsv($fp, $assignemnts);
        }

        fclose($fp);

        $column_name = '';

        $count = count($all_assns) > 0 ? count($all_assns[0]) : 0;

        //Algorithm link https://www.geeksforgeeks.org/find-excel-column-name-given-number/
        while ($count > 0) {
            $rem = $count % 26;
            if ($rem == 0) {
                $column_name .= 'Z';
                $count = $count / 26 - 1;
            } else {
                $column_name .= chr($rem + ord('A')); // starts at A and counts up the alphabet from there
                $count = $count / 26;
                if ($count < 1) {
                    $count = 0;
                }
            }
        }

        $column_name = strrev($column_name);
        $row_number = count($all_assns) + 1;

        $range = $title . 'A1:' . $column_name . $row_number;

        // get the values from the assignment file and save them to an array
        $path = '../downloads/assignment' . $date . '.csv';
        $file = fopen($path, 'r');
        $csv = [];
        while (($line = fgetcsv($file)) !== false) {
            //$line is an array of the csv elements
            $csv[] = $line;
        }
        fclose($file);

        // create the correct update to send back to google sheets to fill the sheet with the array created above
        $body = new Google_Service_Sheets_ValueRange([
            'values' => $csv,
        ]);
        $params = [
            'valueInputOption' => 'USER_ENTERED',
        ];
        $result = $service->spreadsheets_values->update($spreadsheetId, $range, $body, $params);

        $response = $service->spreadsheets->get($spreadsheetId);

        if (count($response) > 30) {
            $lastEntry = $response[count($response) - 1];

            $properties = $lastEntry['properties'];

            $sheetId = $properties['sheetId'];

            $delete = new Google_Service_Sheets_BatchUpdateSpreadsheetRequest([
                'requests' => [
                    'deleteSheet' => [
                        'sheetId' => $sheetId,
                    ],
                ],
            ]);
            $service->spreadsheets->batchUpdate($spreadsheetId, $delete);
        }
    }
}
