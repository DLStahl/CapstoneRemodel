<?php

namespace App\Console\Commands;

use App\Anesthesiologist;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;

class UpdateAnesthesiologistsData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update:anesthesiologists_data';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update the anesthesiologists table with information about who is working today';

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
    public function handle()
    {
        $json = file_get_contents('https://remodel.osuanes.com/QgendaSchedule/');
        $json_data = json_decode($json, true);

        //get scheduling date in format: "yyyy-mm-ddT00:00:00"
        $date = substr(date("c", strtotime('+2 day')), 0, -14) . "00:00:00";
        if(date("l", strtotime('today')) == 'Thursday' || date("l", strtotime('today')) == 'Friday'){
            $date = substr(date("c", strtotime('+4 day')), 0, -14) . "00:00:00";
        } elseif (date("l", strtotime('today'))=='Saturday') {
            $date = substr(date("c", strtotime('+3 day')), 0, -14) . "00:00:00";
        }
        //task abbrevs for attending that are available 
        $attendingTaskAbbrev = ["Endo 1", "Endo 2", "Endo 3", "Late 1", "Late 2", "Late 3", "Late 4","Late 5","Neuro1","Neuro2", "Offsite1", "OR","Ortho 1", "Pulmonary", "SDS-1",
        "SDS-2","SDS-3", "T1", "T2"];
        // filter for available attending for scheduling day
        foreach ($json_data as $staffMember) {
            if(strval($staffMember["Date"]) == $date){
                $taskAbbrev = strval($staffMember["TaskAbbrev"]);
                $isAvailableAttending = false;
                if(in_array($taskAbbrev, $attendingTaskAbbrev)){
                    $isAvailableAttending = true;
                }
                if ($isAvailableAttending) {
                    $first_name = strval($staffMember["StaffFName"]);
                    $last_name = strval($staffMember["StaffLName"]);
                    $staff_key = strval($staffMember["StaffKey"]);

                    $anest = Anesthesiologist::where('first_name', $first_name)
                        ->where('last_name', $last_name)
                        ->where('staff_key', $staff_key)
                        ->first();

                    if (is_null($anest)) {
                        Anesthesiologist::create(compact('first_name', 'last_name', 'staff_key'));
                    } else {
                        $anest->touch();
                    }
                }
            }
        }
    }
}
