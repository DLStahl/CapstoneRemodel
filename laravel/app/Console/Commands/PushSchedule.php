<?php

namespace App\Console\Commands;

use App\Models\Assignment;
use App\Models\Option;
use App\Models\Resident;
use App\Models\ScheduleData;
use App\Models\Milestone;
use App\Models\Anesthesiologist;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

use Google_Client;
use Google_Service_Sheets;
use Google_Service_Sheets_ValueRange;
use Google_Service_Sheets_BatchUpdateSpreadsheetRequest;
use Google_Service_Sheets_Spreadsheet;
use RuntimeException;

// require Google's API
require __DIR__ . '/../../../../google/vendor/autoload.php';

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
    protected $description = 'upload the schedule to Google Sheets';

    /**
     * Columns to write to the spreadsheet
     */
    private static $SPREADSHEET_COLUMN_NAMES = [
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
    ];

    private static $MAX_SPREADSHEETS = 30;

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
     * @return int
     */
    public function handle()
    {
        // connect to the api and our sheet
        $client = self::getClient();
        $service = new Google_Service_Sheets($client);

        // TODO: environment variable
        $spreadsheetId = '1npNBs_j6BvmZO29GHlEJ-mROGhtBEqM7_KNKdAnNLxY';

        $tomorrow = date('Y-m-d', strtotime('+1 day'));
        $title = $tomorrow;

        // create new sheet for today
        $newSheet = new Google_Service_Sheets_BatchUpdateSpreadsheetRequest([
            'requests' => [
                'addSheet' => [
                    'properties' => [
                        'title' => $title,
                        'index' => 0,
                    ],
                ],
            ],
        ]);
        $newSheet = $service->spreadsheets->batchUpdate($spreadsheetId, $newSheet);
        Log::info("Created spreadsheet: \"$title\" with ID: $newSheet->spreadsheetId");

        // get the array that contains all assigments for day + 1.
        $all_assns = self::getAssignments($tomorrow);

        // relative path for assignment sheet
        $assignmentCSVPath = "../downloads/assignment$tomorrow.csv";
        $mode = file_exists($assignmentCSVPath) ? 'w' : 'c';
        $fp = fopen($assignmentCSVPath, $mode);

        // print header for sheet
        fputcsv($fp, PushSchedule::$SPREADSHEET_COLUMN_NAMES);
        // print all assignments to path
        foreach ($all_assns as $assignemnts) {
            fputcsv($fp, $assignemnts);
        }

        fclose($fp);

        $range = "'$title'!A1:ZZZ1000";

        // get the values from the assignment file and save them to an array
        $file = fopen($assignmentCSVPath, 'r');
        $csv = [];
        while (($line = fgetcsv($file)) !== false) {
            // $line is an array of the csv elements
            $csv[] = $line;
        }
        fclose($file);

        // create the correct update to send back to google sheets to fill the sheet with the array created above
        $body = new Google_Service_Sheets_ValueRange(['values' => $csv]);
        $params = ['valueInputOption' => 'USER_ENTERED'];
        $result = $service->spreadsheets_values->update($spreadsheetId, $range, $body, $params);

        // remove the oldest spreadsheet if there are over MAX_SPREADSHEETS spreadsheets
        $response = $service->spreadsheets->get($spreadsheetId);
        if (count($response) > PushSchedule::$MAX_SPREADSHEETS) {
            $lastEntry = $response[count($response) - 1];
            $lastSheetId = $lastEntry['properties']['sheetId'];
            $delete = new Google_Service_Sheets_BatchUpdateSpreadsheetRequest([
                'requests' => [
                    'deleteSheet' => [
                        'sheetId' => $lastSheetId,
                    ],
                ],
            ]);
            $service->spreadsheets->batchUpdate($spreadsheetId, $delete);
        }

        return 0;
    }

    /**
     * https:// developers.google.com/sheets/api/quickstart/php#step_3_set_up_the_sample
     *
     * @return Google_Client the authorized client object
     */
    public static function getClient()
    {
        $client = new Google_Client();
        $client->setApplicationName('REMODEL');
        $client->setScopes([
            Google_Service_Sheets::DRIVE,
            Google_Service_Sheets::DRIVE_FILE,
            Google_Service_Sheets::SPREADSHEETS,
        ]);
        $authConfigPath = base_path('../REMODEL-0dfb917af5de.json');
        $client->setAuthConfig($authConfigPath);

        // Load previously authorized token from a file.
        $tokenPath = base_path('../token.json');
        if (file_exists($tokenPath)) {
            $accessToken = json_decode(file_get_contents($tokenPath), true);
            $client->setAccessToken($accessToken);
        } else {
            Log::error('Google API token file not found');
            throw new RuntimeException('Google API token file not found');
        }

        return $client;
    }

    /**
     * get assignments for Google Sheets output
     *
     * @param string $date date to find assignments for
     * @return Array daily assignment information
     */
    public static function getAssignments($date)
    {
        $all_assignments = [];
        $assignments = Assignment::where('date', $date)->get();

        // find all relavant data for the assignments
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
                $anesthesiologist = Anesthesiologist::find($pref_anest_id);
                $first_name = $anesthesiologist->first_name;
                $last_name = $anesthesiologist->last_name;
                $pref_anest_name = "$first_name $last_name";
                $pref_anest_staff_key = $anesthesiologist->staff_key;
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
}
