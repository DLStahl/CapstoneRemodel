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

        foreach ($json_data as $oStaffMember) {
            $color = strtolower($oStaffMember["BgColor"]);
            $sRet = "";
            switch ($color) {
                case "000000":
                    $sRet = "Spacer";
                    break;
                case "660033":
                    $sRet = "Attending"; //ICU
                    break;
                case "66cc99":
                    $sRet = "Attending"; //Neuro
                    break;
                case "66cccc":
                    $sRet = "Attending"; //OOD
                    break;
                case "66ccff":
                    $sRet = "Attending"; //Pain
                    break;
                case "66ffff":
                    $sRet = "Attending"; //C$harge
                    break;
                case "99cccc":
                    $sRet = "Attending";
                    break;
                case "99ccff":
                    $sRet = "CRNA"; //General
                    break;
                case "9966cc":
                    $sRet = "CA-1";
                    break;
                case "cc99cc":
                    $sRet = "CA-1";
                    break;
                case "ccccff":
                    $sRet = "Attending"; //PartTime
                    break;
                case "ccffff":
                    $sRet = "CRNA"; //PartTime
                    break;
                case "f5faf5":
                    if (strpos($oStaffMember["Abbrev"], "SRNA") !== false) {
                        $sRet = "SRNA";
                    } else {
                        $sRet = "Attending"; //General
                    }
                    break;
                case "ff0000":
                    $sRet = "CRNA"; //Cardiac
                    break;
                case "ff99cc":
                    $sRet = "CA-2";
                case "ff9999":
                    if ($oStaffMember["ExtCallSysId"] == "PGY") {
                        $sRet = "CA-2";
                    } else {
                        $sRet = "Attending"; //Cardiac
                    }
                    break;
                case "ffcc99":
                    if (strpos($oStaffMember["Abbrev"], "Med Student") !== false) {
                        $sRet = "Student";
                    } else if (strpos($oStaffMember["Abbrev"], "Outside") !== false) {
                        $sRet = "Student";
                    } else {
                        $sRet = "CA-3";
                    }
                    break;
                case "ffcccc":
                    $sRet = "CA-1";
                    break;
                case "ffccff":
                    $sRet = "Fellow";
                    break;
                case "ffff99":
                    $sRet = "Attending"; //East
                    break;
                case "ffffff":
                    $sRet = "Attending";
                    break;
                default:
                    $sRet = "";
                    break;
            }

            if ($sRet == "Attending") {
                $first_name = strval($oStaffMember["FirstName"]);
                $last_name = strval($oStaffMember["LastName"]);

                $anest = Anesthesiologist::where('first_name', $first_name)
                    ->where('last_name', $last_name)
                    ->first();

                if (is_null($anest)) {
                    Anesthesiologist::create(compact('first_name', 'last_name'));
                } else {
                    $anest->touch();
                }

            }
        }

    }
}
