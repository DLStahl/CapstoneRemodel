<?php

namespace App\Console\Commands;
use App\Status;
use App\AutoAssignment;
use App\Assignment;
use App\Option;
use App\Resident;
use App\ScheduleData;
use App\Milestone;
use App\Anesthesiologist;
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
        $dir = "/usr/local/webs/remodel.anesthesiology_test/htdocs/downloads/assignment".$date.".csv";
        $fp = null;

        if (file_exists($dir)) {
            $fp = fopen($dir, 'w');
        } else {
            $fp = fopen($dir, 'c');
        }
        fputcsv($fp, array('date', 'room', 'case procedure', 'start time', 'end time',
                            'lead surgeon', 'resident', 'preference', 'milestones', 'objectives', 'anest staff key', 'anest name'));
        
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

            $case_procedure = ScheduleData::where('id', $schedule_id)->value('case_procedure');
            $start_time = ScheduleData::where('id', $schedule_id)->value('start_time');
            $end_time = ScheduleData::where('id', $schedule_id)->value('end_time');
            $lead_surgeon = ScheduleData::where('id', $schedule_id)->value('lead_surgeon');
            $resident = Resident::where('id', $resident_id)->value('name');

            $option_id = $option['option'];
            $milestone_id = Option::where('id', $option_id)->value('milestones');
            $preference = Option::where('id', $option_id)->value('option');
            $milestones = Milestone::where('id', $milestone_id)->value('category');
            $objectives = Option::where('id', $option_id)->value('objectives');
            $pref_anest_id = $option['anesthesiologist_id'];
            if ($pref_anest_id != NULL){
                $pref_anest_name = Anesthesiologist::where('id', $pref_anest_id)->value('first_name') ." ". Anesthesiologist::where('id', $pref_anest_id)->value('last_name');
                $pref_anest_staff_key = Anesthesiologist::where('id', $pref_anest_id)->value('staff_key');
            } else {
                $pref_anest_name = "No assignment";
                $pref_anest_staff_key = NULL;
            }

            fputcsv($fp, array($date, $room, $case_procedure, $start_time, $end_time,
        $lead_surgeon, $resident, $preference, $milestones, $objectives, $pref_anest_staff_key, $pref_anest_name));
        }
        fclose($fp);
    }
    
	/**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    { 
        $date = date("Y-m-d", strtotime('+2 day'));
        self::updateOption($date);
    }


}
