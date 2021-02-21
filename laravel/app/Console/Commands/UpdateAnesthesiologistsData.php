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
        // TODO: change file location to url 
        $json = file_get_contents("QgendaSchedule-edited.json");
        $json_data = json_decode($json, true);

        //get scheduling date in format: "yyyy-mm-ddT00:00:00"
        $date = substr(date("c", strtotime('+2 day')), 0, -14) . "00:00:00";
        if(date("l", strtotime('today')) == 'Thursday' || date("l", strtotime('today')) == 'Friday'){
            $date = substr(date("c", strtotime('+4 day')), 0, -14) . "00:00:00";
        } elseif (date("l", strtotime('today'))=='Saturday') {
            $date = substr(date("c", strtotime('+3 day')), 0, -14) . "00:00:00";
        }
        // filter for available attending for scheduling day
        foreach ($json_data as $staffMember) {
            if(strval($staffMember["Date"]) == $date){
                $taskAbbrev = strval($staffMember["TaskAbbrev"]);
                $label = "";
                switch($taskAbbrev){
                    case "Endo 1":
                        $label = "Attending";
                        break;
                    case "Endo 2":
                        $label = "Attending"; 
                        break;
                    case "Endo 3":
                        $label = "Attending"; 
                        break;
                    case "Late 1":
                        $label = "Attending";
                        break;
                    case "Late 2":
                        $label = "Attending"; 
                        break;
                    case "Late 3":
                        $label = "Attending"; 
                        break;
                    case "Late 4":
                        $label = "Attending"; 
                        break;
                    case "Late 5":
                        $label = "Attending"; 
                        break;
                    case "Neuro1":
                        $label = "Attending"; 
                        break;
                    case "Neuro2":
                        $label = "Attending";
                        break;
                    case "Offsite1":
                        $label = "Attending"; 
                        break;
                    case "OR":
                        $label = "Attending"; 
                        break;
                    case "Ortho 1":
                        $label = "Attending";
                        break;
                    case "Pulmonary":
                        $label = "Attending"; 
                        break;
                    case "SDS-1":
                        $label = "Attending"; 
                        break;
                    case "SDS-2":
                        $label = "Attending"; 
                        break;
                    case "SDS-3":
                        $label = "Attending"; 
                        break;
                    case "T1":
                        $label = "Attending"; 
                        break;
                    case "T2":
                        $label = "Attending"; 
                        break;
                    default:
                        $label = "";
                        break;
                }

                if ($label == "Attending") {
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
