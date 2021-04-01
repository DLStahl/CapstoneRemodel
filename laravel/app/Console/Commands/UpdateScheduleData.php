<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\ScheduleData;
use App\ScheduleParser;
use App\Models\Status;
use App\Models\ScheduleDataStatic;

class UpdateScheduleData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = "update:schedule_data";

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = "Command description";

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
        $date = date("Y-m-d", strtotime("today"));
        if (Status::where("date", $date)->doesntExist()) {
            Status::insert([
                "date" => $date,
            ]);
        }

        if ((int) Status::where("date", $date)->value("schedule") < 1) {
            $parser = new ScheduleParser(
                date("o", strtotime("today")) . date("m", strtotime("today")) . date("d", strtotime("today")),
                true
            );

            // insert static options
            $staticDate = date("Y-m-d", strtotime("+2 Weekday"));
            $allStaticChoices = ScheduleDataStatic::all();
            $allStaticChoices->each(function ($choice) use ($staticDate) {
                $this->addData(
                    $staticDate,
                    $choice->location,
                    $choice->room,
                    $choice->case_procedure,
                    $choice->case_procedure_code,
                    $choice->lead_surgeon,
                    $choice->lead_surgeon_code,
                    $choice->patient_class,
                    $choice->start_time,
                    $choice->end_time,
                    $choice->rotation
                );
            });
            // end insert static options

            Status::where("date", $date)->update([
                "schedule" => true,
            ]);
        }
    }

    public function addData(
        $date,
        $location,
        $room,
        $case_procedure,
        $case_procedure_code,
        $lead_surgeon,
        $lead_surgeon_code,
        $patient_class,
        $start_time,
        $end_time,
        $rotation
    ) {
        if (
            ScheduleData::where("date", $date)
                ->where("room", $room)
                ->doesntExist()
        ) {
            $case = "(" . $start_time . "-" . $end_time . ")" . $case_procedure . " [" . $case_procedure_code . "]\n";
            $surgeon = $lead_surgeon . " [" . $lead_surgeon_code . "]\n";
            if (strcmp($start_time, $end_time) < 0) {
                ScheduleData::insert([
                    "date" => $date,
                    "location" => $location,
                    "room" => $room,
                    "case_procedure" => $case,
                    "lead_surgeon" => $surgeon,
                    "patient_class" => $patient_class,
                    "start_time" => $start_time,
                    "end_time" => $end_time,
                    "rotation" => $rotation,
                ]);
            }
        }
    }
}
