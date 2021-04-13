<?php

namespace App;

use App\Constant;
use App\Models\ScheduleData;
use App\Models\Option;
use App\Models\Resident;
use App\Models\FilterRotation;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class ScheduleParser
{
    /**
     * Protected members
     */
    protected $filepath;
    protected $date;
    protected $fileExists = true;

    /**
     * Private functions
     */

    /**
     * Parse date
     *
     * @var string
     */
    private static function getLineDate($line)
    {
        return date('Y-m-d', strtotime($line[Constant::DATE]));
    }

    /**
     * Parse time
     *
     * @var string
     *
     * @var int
     *
     */
    private static function getLineTime($line, $index)
    {
        $time = "00:00:00";
        $length = strlen($line[$index]); 
        if (strcmp($line[$index], "") == 0) {
            $time = NULL;
        }else if($length == 4){
            $time = substr($line[$index], 0, 2) . ":" . substr($line[$index], 2). ":00";
        }else if($length == 3){
            $time = "0" . $line[$index][0] . ":" . substr($line[$index], 1). ":00";
        }else if($length == 2){
            $time = substr($line[$index], 0, 2) . ":00:00";
        } else{
            $time = "0" . $line[$index][0] . ":00:00";
        }
        return $time;
    }

    // Identify rotation based on surgeon
    private static function getRotation($surgeon)
    {
        if (strlen($surgeon) == 0) {
            return null;
        }
        $name = $surgeon;
        if (strpos($surgeon, ",")) {
            $name = substr($surgeon, 0, strpos($surgeon, ","));
        }
        $name = explode(" ", $name);
        $name_first = $name[0];
        $name_last = $name[1];
        // Escape middle name
        if (sizeof($name) > 2) {
            $name_last = $name[2];
        }
        $surgeon_rotations = FilterRotation::where("surgeon", "LIKE", "%{$name_last}%")
            ->where("surgeon", "LIKE", "%{$name_first}%")
            ->get();
        $rotation = "";
        // store rotation as "rotation1 rotation2 rotation3 ..."
        foreach ($surgeon_rotations as $row) {
            $rotation .= $row["rotation"] . " ";
        }
        $rotation = trim($rotation);
        return $rotation;
    }

    /**
     * Insert data into database.
     */
    private function insertScheduleData($datefile)
    {
        if (!file_exists($this->filepath)) {
            Log::info($this->filepath);
            return false;
        }

        date_default_timezone_set("America/New_York");
        ScheduleData::where("date", date("Y-m-d", strtotime($datefile . "+2 day")))->delete();
        ScheduleData::where("date", date("Y-m-d", strtotime($datefile . "+4 day")))->delete();
        Log::info("delete succ");

        // Open file
        $fp = fopen($this->filepath, "r");

        // Read the first row
        fgetcsv($fp);

        // store dates that updated schedule
        $datesUpdated = [];
        while (($line = fgetcsv($fp)) !== false) {
            $date = self::getLineDate($line);
            if (!in_array($date, $datesUpdated)) {
                array_push($datesUpdated, $date);
            }
            $location = $line[Constant::LOCATION];
            $room = $line[Constant::ROOM];
            if (strlen($room) < 1) {
                if (strpos($location, "CCCT")) {
                    $room = "CCCT TBD";
                } elseif (strpos($location, "UH")) {
                    $room = "UH TBD";
                } elseif (strpos($location, "ROSS")) {
                    $room = "ROSS TBD";
                } else {
                    $room = "TBD";
                }
            }
            $case_procedure = $line[Constant::CASE_PROCEDURE];
            $lead_surgeon = $line[Constant::LEAD_SURGEON];
            $patient_class = $line[Constant::PATIENT_CLASS];
            $rotation = self::getRotation($lead_surgeon);
            $start_time = self::getLineTime($line, Constant::START_TIME);
            $end_time = self::getLineTime($line, Constant::END_TIME);

            ScheduleData::insert([
                "date" => $date,
                "location" => $location,
                "room" => $room,
                "case_procedure" => $case_procedure,
                "lead_surgeon" => $lead_surgeon,
                "patient_class" => $patient_class,
                "rotation" => $rotation,
                "start_time" => $start_time,
                "end_time" => $end_time,
            ]);
        }

        // If residents already made preferences and the schedule changed, delete existing preferences and notify residents to select new preferences.
        foreach ($datesUpdated as $date) {
            $options = Option::where("date", $date)
                ->get()
                ->groupBy("resident");
            if (sizeof($options) > 0) {
                foreach ($options as $residentOptions) {
                    $residentId = $residentOptions[0]["resident"];
                    $residentName = Resident::where("id", $residentId)->value("name");
                    $residentEmail = Resident::where("id", $residentId)->value("email");
                    Log::info($residentName);
                    Log::info($residentEmail);
                    if (
                        Option::where("date", $date)
                            ->where("resident", $residentId)
                            ->delete()
                    ) {
                        Log::info($residentName . " options deleted");
                    }
                    self::notifySelectNewPreferences($residentName, $residentEmail, $date);
                }
            }
        }
        // Close file
        fclose($fp);

        return true;
    }

    // Notify residents to select new preferences
    public function notifySelectNewPreferences($toName, $toEmail, $date)
    {
        Log::info("send email");
        $subject = "REMODEL: Please select new preferences for date " . $date;
        $body =
            "Your previous preferences for date " .
            $date .
            " were deleted because the schedule is updated. Please select new preferences on REMODEL website.";
        $heading = "Please select new preferences for date " . $date;
        $data = ["name" => $toName, "heading" => $heading, "body" => $body];

        Mail::send("emails.mail", $data, function ($message) use ($toName, $toEmail, $subject) {
            $message->to($toEmail, $toName)->subject($subject);
            $message->from("OhioStateAnesthesiology@gmail.com");
        });
    }

    /**
     * Public functions
     */

    /**
     * Constructor of ScheduleBuffer.
     *
     * @var string
     *      {@code date} Formate: "year" + "month" + "day"
     */
    public function __construct($datefile, $isConsole = false)
    {
        Log::info("parse schedule data");
        $this->filepath = $isConsole
            ? Constant::CONSOLE_PATH . $datefile . Constant::EXTENSION
            : Constant::WEB_PATH . $datefile . Constant::EXTENSION;

        Log::info($datefile);

        // Assign value to date
        $year = intval(substr($datefile, 0, 4));
        $month = intval(substr($datefile, 4, 2));
        $day = intval(substr($datefile, 6));
        $this->date = $year . "-" . $month . "-" . $day;
        Log::info($this->date);
        if (!$this->insertScheduleData($datefile)) {
            $this->fileExists = false;
        }

        date_default_timezone_set("America/New_York");
        $this->processScheduleData(date("Y-m-d", strtotime($datefile . "+2 day")));
        $this->processScheduleData(date("Y-m-d", strtotime($datefile . "+4 day")));
    }

    public function fileExists()
    {
        return $this->fileExists;
    }

    /**
     * Combine data row with same date and room
     */
    public function processScheduleData($date)
    {
        // find rooms that has not null start/end time
        $items = ScheduleData::select("room")
            ->where("date", $date)
            ->whereNotNull("start_time")
            ->whereNotNull("end_time")
            ->distinct()
            ->get();
        foreach ($items as $item) {
            // find schedule data that has not null start/end time
            $records = ScheduleData::where("date", $date)
                ->where("room", $item["room"])
                ->whereNotNull("start_time")
                ->whereNotNull("end_time")
                ->orderBy("start_time")
                ->get();
            $location = null;
            $room = null;
            $case_procedure = null;
            $lead_surgeon = null;
            $patient_class = null;
            $rotation = null;
            $start_time = null;
            $end_time = null;
            Log::info($item["room"] . " numbers\n " . count($records));
            for ($i = 0; $i < count($records); $i++) {
                if ($i == 0) {
                    $location = $records[$i]["location"];
                    $room = $records[$i]["room"];
                    $start_time = $records[$i]["start_time"];
                }
                if ($i == count($records) - 1) {
                    $end_time = $records[$i]["end_time"];
                }

                $line =
                    "(" .
                    $records[$i]["start_time"] .
                    "-" .
                    $records[$i]["end_time"] .
                    ")" .
                    $records[$i]["case_procedure"] .
                    "\n";
                // $line=$records[$i]['case_procedure']."\n";
                $case_procedure = $case_procedure . $line;
                $lead_surgeon = $lead_surgeon . $records[$i]["lead_surgeon"] . "\n";
                $patient_class = $patient_class . $records[$i]["patient_class"] . "\n";
                $rotation = $rotation . $records[$i]["rotation"] . "\n";
            }
            // delete schedule data that has not null start/end time
            ScheduleData::where("date", $date)
                ->where("room", $room)
                ->whereNotNull("start_time")
                ->whereNotNull("end_time")
                ->delete();
            ScheduleData::insert([
                "date" => $date,
                "location" => $location,
                "room" => $room,
                "case_procedure" => $case_procedure,
                "lead_surgeon" => $lead_surgeon,
                "patient_class" => $patient_class,
                "rotation" => $rotation,
                "start_time" => $start_time,
                "end_time" => $end_time,
            ]);
        }
    }
}
