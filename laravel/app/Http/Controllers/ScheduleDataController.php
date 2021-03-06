<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\ScheduleData;
use App\Models\Resident;
use App\Models\Option;
use App\Models\Anesthesiologist;
use App\Models\FilterRotation;
use App\Models\Assignment;
use App\Models\Milestone;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;

class ScheduleDataController extends Controller
{
    /**
     * Protected members
     */
    protected $room = null;
    protected $leadSurgeon = null;
    protected $rotation = null;
    protected $start_time = null;
    protected $end_time = null;

    /**
     * Private functions.
     */

    /**
     * Filter data to output.
     */
    private static function updateData(array $args = [])
    {
        // Set up default input values.
        $date = $args['date'];
        $leadSurgeon = !isset($args['lead_surgeon']) ? 'TBD' : $args['lead_surgeon'];
        $start_time = !isset($args['start_time']) ? '00:00:00' : $args['start_time'];
        $end_time = !isset($args['end_time']) ? '23:59:59' : $args['end_time'];
        $room = !isset($args['room']) ? 'TBD' : $args['room'];
        $rotation = !isset($args['rotation']) ? 'TBD' : $args['rotation'];

        if (strcmp($leadSurgeon, 'null') == 0) {
            $leadSurgeon = 'TBD';
        }

        if (strcmp($room, 'null') == 0) {
            $room = 'TBD';
        }

        if (strcmp($rotation, 'null') == 0) {
            $rotation = 'TBD';
        }

        if (strcmp($start_time, 'null') == 0) {
            $start_time = '00:00:00';
        }
        if (strcmp($end_time, 'null') == 0) {
            $end_time = '23:59:59';
        }

        $schedule_data = null;
        // Get filtered schedule
        if (strcmp($room, 'TBD') != 0) {
            $schedule_data = ScheduleData::whereDate('date', $date)
                ->whereNotNull('start_time')
                ->whereNotNull('end_time')
                ->where('room', $room);
        } else {
            $schedule_data = ScheduleData::whereDate('date', $date)
                ->whereNotNull('start_time')
                ->whereNotNull('end_time');
        }
        if (strcmp($leadSurgeon, 'TBD') != 0) {
            $schedule_data = $schedule_data->where('lead_surgeon', 'LIKE', "%{$leadSurgeon}%");
        }
        if (strcmp($rotation, 'TBD') != 0) {
            $schedule_data = $schedule_data->where('rotation', 'LIKE', "%{$rotation}%");
        }
        if (strcmp($start_time, '00:00:00') != 0) {
            $schedule_data = $schedule_data->whereTime('start_time', '>=', $start_time);
        }
        if (strcmp($end_time, '23:59:59') != 0) {
            $schedule_data = $schedule_data->whereTime('end_time', '<=', $end_time);
        }
        $minTime = $schedule_data->min('start_time');
        $maxTime = $schedule_data->max('end_time');
        $schedule_data = $schedule_data->orderBy('room', 'asc')->get();

        $schedule = [];
        foreach ($schedule_data as $data) {
            $resident = null;
            if (Assignment::where('schedule', $data['id'])->exists()) {
                $resident_id = Assignment::where('schedule', $data['id'])->value('resident');
                $resident = Resident::where('id', $resident_id)->value('name');
            }
            array_push($schedule, [
                'date' => $data['date'],
                'room' => $data['room'],
                'lead_surgeon' => $data['lead_surgeon'],
                'id' => $data['id'],
                'resident' => $resident,
                'case_procedure' => $data['case_procedure'],
                'patient_class' => $data['patient_class'],
                'rotation' => $data['rotation'],
                'start_time' => $data['start_time'],
                'end_time' => $data['end_time'],
            ]);
        }

        $result = [
            'minTime' => $minTime,
            'maxTime' => $maxTime,
            'schedule' => $schedule,
        ];
        return $result;
    }

    private function processInput($room, $leadSurgeon, $rotation, $start_time_end_time)
    {
        if ($room == null && $leadSurgeon == null && $rotation == null && $start_time_end_time == null) {
            return;
        }

        // Get times
        $tp = stripos($start_time_end_time, '_');
        $this->start_time = substr($start_time_end_time, 0, $tp);
        $this->end_time = substr($start_time_end_time, $tp + 1);

        $this->room = $room;
        $this->leadSurgeon = $leadSurgeon;
        $this->rotation = $rotation;

        if (strcmp($this->room, 'null') == 0) {
            $this->room = null;
        }
        if (strcmp($this->leadSurgeon, 'null') == 0) {
            $this->leadSurgeon = null;
        }
        if (strcmp($this->rotation, 'null') == 0) {
            $this->rotation = null;
        }
        if (strcmp($this->start_time, 'null') == 0) {
            $this->start_time = null;
        }
        if (strcmp($this->end_time, 'null') == 0) {
            $this->end_time = null;
        }
    }

    // Get all rooms, surgeons and rotations of the given date
    private function getFilterOptions($date)
    {
        $schedule = ScheduleData::where('date', $date)
            ->whereNotNull('start_time')
            ->whereNotNull('end_time');
        $roomsData = $schedule->select('room')->get();
        $rooms = [];
        foreach ($roomsData as $room) {
            array_push($rooms, $room['room']);
        }
        sort($rooms);

        // lead surgeons with duplicates
        $allLeadSurgeons = $schedule->select('lead_surgeon')->get();
        // lead surgeons without duplicates
        $noDuplicateSurgeons = [];
        foreach ($allLeadSurgeons as $surgeons) {
            $leadSurgeon = $surgeons['lead_surgeon'];
            while (strlen($leadSurgeon) > 0) {
                $leadPos = strpos($leadSurgeon, "\n");
                $tmp_surgeon = substr($leadSurgeon, 0, $leadPos);
                // get rid of [number] after the surgeon name
                $ep = strpos($tmp_surgeon, '[');
                if ($ep != -1) {
                    $tmp_surgeon = substr($tmp_surgeon, 0, $ep);
                }
                $tmp_surgeon = trim($tmp_surgeon);
                if (!in_array($tmp_surgeon, $noDuplicateSurgeons)) {
                    array_push($noDuplicateSurgeons, $tmp_surgeon);
                }
                $leadSurgeon = substr($leadSurgeon, $leadPos + 1);
            }
        }
        sort($noDuplicateSurgeons);

        $rotations = [];

        $filterOptions = [
            'rooms' => $rooms,
            'leadSurgeons' => $noDuplicateSurgeons,
            'rotations' => $rotations,
        ];

        return $filterOptions;
    }

    /**
     * Public functions
     */
    public function getDay(
        $day = null,
        $room = null,
        $leadSurgeon = null,
        $rotation = null,
        $start_time_end_time = null
    ) {
        date_default_timezone_set('America/New_York');

        $day_translation_array = [
            'firstday' => 1,
            'secondday' => 2,
            'thirdday' => 3,
        ];

        if (!array_key_exists($day, $day_translation_array)) {
            abort(404);
        }

        $date = date('Y-m-d', strtotime('+' . $day_translation_array[$day] . ' Weekday'));

        $this->processInput($room, $leadSurgeon, $rotation, $start_time_end_time);
        $TimeRange_ScheduleData = self::updateData([
            'date' => $date,
            'lead_surgeon' => $this->leadSurgeon,
            'room' => $this->room,
            'rotation' => $this->rotation,
            'start_time' => $this->start_time,
            'end_time' => $this->end_time,
        ]);
        $minTime = $TimeRange_ScheduleData['minTime'];
        $maxTime = $TimeRange_ScheduleData['maxTime'];
        $schedule_data = $TimeRange_ScheduleData['schedule'];
        $filter_options = $this->getFilterOptions($date);
        $rotation_options = FilterRotation::select('rotation')
            ->distinct()
            ->get();
        return view(
            'schedules.resident.schedule_table',
            compact('minTime', 'maxTime', 'schedule_data', 'filter_options', 'rotation_options')
        );
    }

    public function getChoice()
    {
        // Exclude Admin from selecting preferences
        if (!Resident::where('email', $_SERVER['HTTP_EMAIL'])->exists()) {
            return view('nonpermit');
        }
        // get the id from the form
        $id = $_REQUEST['schedule_id'];
        $choiceTypes = ['First', 'Second', 'Third'];
        // id is stored as id1_id2_id3, need to split it to get the individual ids
        $trimmed_id = trim($id, '_');
        $schedule_data_ids = explode('_', $trimmed_id);
        // get current preferences
        foreach ($schedule_data_ids as $i => $schedule_data_id) {
            if ($schedule_data_id !== '' && $schedule_data_id !== '0') {
                $schedule = ScheduleData::where('id', $schedule_data_id)->get();
                $currentChoices[$i] = [
                    'schedule' => $schedule,
                    'case_procedure' => $this->parseCaseProcedure($schedule[0]['case_procedure']),
                    'milestone' => Milestone::where('id', $_REQUEST['milestones' . ($i + 1)])->get(),
                    'objective' => $_REQUEST['objectives' . ($i + 1)],
                    'anesthesiologist_pref' => Anesthesiologist::where('id', $_REQUEST['pref_anest' . ($i + 1)])->get(),
                ];
            } else {
                $currentChoices[$i] = null;
            }
        }
        // Get previous preferences of the same date
        $date = $currentChoices[0]['schedule'][0]['date'];
        $previousChoices = [];
        $resident = Resident::where('email', $_SERVER['HTTP_EMAIL'])->value('id');
        foreach ($schedule_data_ids as $i => $schedule_data_id) {
            $previousOption = Option::where('date', $date)
                ->where('resident', $resident)
                ->where('option', $i + 1)
                ->where('isValid', 1)
                ->get();
            if (sizeof($previousOption) > 0) {
                $schedule = ScheduleData::where('id', $previousOption[0]['schedule'])->get();
                $pref_anest = Anesthesiologist::where('id', $previousOption[0]['anesthesiologist_id'])->get();
                $previousChoices[$i] = [
                    'schedule' => $schedule,
                    'case_procedure' => $this->parseCaseProcedure($schedule[0]['case_procedure']),
                    'milestone' => Milestone::where('id', $previousOption[0]['milestones'])->get(),
                    'objective' => $previousOption[0]['objectives'],
                    'anesthesiologist_pref' => $pref_anest,
                ];
            } else {
                $previousChoices[$i] = null;
            }
        }
        return view(
            'schedules.resident.schedule_confirm',
            compact('id', 'currentChoices', 'previousChoices', 'choiceTypes')
        );
    }
    // parse case_procedure by removing time and case number
    public function parseCaseProcedure($case)
    {
        $case = str_replace(' [', '', $case);
        $case = preg_replace('/[0-9:()\[\]]/', '', $case);
        return $case;
    }

    public function selectMilestones($id)
    {
        // Get resident data
        $current_resident = Resident::where('email', $_SERVER['HTTP_EMAIL'])->get();
        $resident = $current_resident[0]['id'];

        // id is stored as id1_id2_id3, need to split it to get the individual ids
        $trimmed_id = trim($id, '_');
        $schedule_data_ids = explode('_', $trimmed_id);

        foreach ($schedule_data_ids as $i => $schedule_data_id) {
            $resident_choices[$i] = [
                'schedule' => null,
                'lead_surgeon' => null,
            ];
            if ($schedule_data_id !== '' && $schedule_data_id !== '0') {
                $schedule_data[$i] = ScheduleData::where('id', $schedule_data_id)->get();
                $lead_surgeon_string = $schedule_data[$i][0]['lead_surgeon'];
                preg_match('/(.+) \[(\d+)\]/', $lead_surgeon_string, $matches); // get name of the lead surgeon
                $lead_surgeon = count($matches) > 1 ? $matches[1] : 'OORA';
                $date = $schedule_data[$i][0]['date'];
                $resident_choices[$i]['schedule'] = $schedule_data[$i][0];
                $resident_choices[$i]['lead_surgeon'] = $lead_surgeon;
            }
        }

        $milestones = Milestone::where('exists', 1)->get();

        $anesthesiologists = Anesthesiologist::where('updated_at', '>', Carbon::today())
            ->orderBy('last_name')
            ->get();

        return view(
            'schedules.resident.milestone',
            compact('id', 'milestones', 'resident_choices', 'anesthesiologists')
        );
    }

    public function updateMilestones($id)
    {
        // Get resident data
        $current_resident = Resident::where('email', $_SERVER['HTTP_EMAIL'])->first();
        $resident_id = $current_resident['id'];
        // id is stored as id1_id2_id3, need to split it to get the individual ids
        $trimmed_id = trim($id, '_');
        $schedule_data_ids = explode('_', $trimmed_id);
        foreach ($schedule_data_ids as $i => $schedule_data_id) {
            $resident_choices[$i] = [
                'schedule' => null,
                'lead_surgeon' => null,
                'milestone' => null,
                'objective' => null,
                'pref_anest' => null,
            ];
            if ($schedule_data_id !== '' && $schedule_data_id !== '0') {
                $choice = $i + 1;
                $schedule_data[$i] = ScheduleData::where('id', $schedule_data_id)->get();
                $lead_surgeon_string = $schedule_data[$i][0]['lead_surgeon'];
                preg_match('/(.+) \[(\d+)\]/', $lead_surgeon_string, $matches); // get name of the lead surgeon
                $lead_surgeon = count($matches) > 1 ? $matches[1] : 'OORA';
                $date = $schedule_data[$i][0]['date'];
                $resident_choices[$i]['schedule'] = $schedule_data[$i][0];
                $resident_choices[$i]['lead_surgeon'] = $lead_surgeon;
                $option[$i] = Option::where('date', $date)
                    ->where('resident', $resident_id)
                    ->where('option', $choice)
                    ->get();

                if (sizeof($option[$i]) > 0) {
                    $milestone[$i] = Milestone::where('id', $option[$i][0]['milestones'])->get();
                    if (sizeof($milestone[$i]) > 0) {
                        $resident_choices[$i]['milestone'] = $milestone[$i][0];
                    }
                    $resident_choices[$i]['objective'] = $option[$i][0]['objectives'];
                    $resident_choices[$i]['pref_anest'] = $option[$i][0]['anesthesiologist_id'];
                }
            }
        }

        $milestones = Milestone::all();

        $anesthesiologists = Anesthesiologist::where('updated_at', '>', Carbon::today())
            ->orderBy('last_name')
            ->get();

        return view(
            'schedules.resident.milestone',
            compact('id', 'milestones', 'resident_choices', 'anesthesiologists')
        );
    }

    public function notifyResidentOverwrittenPreferences($toName, $toEmail, $residentName, $date, $overwrittenChoices)
    {
        $choice = implode(', ', $overwrittenChoices);

        $subject = config('app.env') == 'production' ? '' : '(' . config('app.env') . ') ';
        $subject .= "REMODEL: Resident Preference $choice Overwritten for $date";
        $body = "Resident $residentName has overwritten OR preferences $choice for $date. New preferences are now viewable on REMODEL website.";
        $heading = "Resident $residentName has overwritten OR preference $choice";
        $data = ['name' => $toName, 'heading' => $heading, 'body' => $body];

        Mail::send('emails.mail', $data, function ($message) use ($toName, $toEmail, $subject) {
            $message->to($toEmail, $toName)->subject($subject);
            $message->from(config('mail.username'));
        });
    }

    // Insert options if no preference exists.
    private function insertOption()
    {
        // variables to track if the use has overwritten a preference
        $overwrittenChoices = [];

        // get the id from the form
        $id = $_REQUEST['schedule_id'];

        // id is stored as id1_id2_id3, need to split it to get the individual ids
        $trimmed_id = trim($id, '_');
        $schedule_data_ids = explode('_', $trimmed_id);

        // Get resident
        $resident = Resident::where('email', $_SERVER['HTTP_EMAIL'])->first();
        $resident_id = $resident['id'];
        $residentName = $resident['name'];

        $date = ScheduleData::where('id', $schedule_data_ids[0])->first()->date;

        foreach ($schedule_data_ids as $i => $schedule_data_id) {
            $choice = $i + 1;
            if ($schedule_data_id !== '' && $schedule_data_id !== '0') {
                $pref_anest[$i] = null;
                $schedule_data[$i] = ScheduleData::where('id', $schedule_data_id)->get();
                $lead_surgeon_string = $schedule_data[$i][0]['lead_surgeon'];
                preg_match('/(.+) \[(\d+)\]/', $lead_surgeon_string, $matches); // get id of lead surgeon
                $lead_surgeon_medhub_id = count($matches) > 2 ? $matches[2] : -1; // OORA case, sets medhub id to -1 as no lead surgeons are specified
                if (isset($_REQUEST['pref_anest' . $choice]) && $_REQUEST['pref_anest' . $choice] != 0) {
                    // if they chose an anesthesiologist, add their ID to the DB, if not, add NULL
                    $pref_anest[$i] = $_REQUEST['pref_anest' . $choice];
                }

                if (
                    Option::where('date', $date)
                        ->where('resident', $resident_id)
                        ->where('option', $choice)
                        ->count() != 0
                ) {
                    array_push($overwrittenChoices, $i + 1);

                    Option::where('date', $date)
                        ->where('resident', $resident_id)
                        ->where('option', $choice)
                        ->update([
                            'schedule' => $schedule_data_id,
                            'attending' => $lead_surgeon_medhub_id,
                            'milestones' => $_REQUEST['milestones' . $choice],
                            'objectives' => $_REQUEST['objectives' . $choice],
                            'anesthesiologist_id' => $pref_anest[$i],
                            'isValid' => 1,
                        ]);
                } else {
                    // Insert data
                    if (!is_null($schedule_data[$i])) {
                        Option::insert([
                            'date' => $date,
                            'resident' => $resident_id,
                            'schedule' => $schedule_data_id,
                            'attending' => $lead_surgeon_medhub_id,
                            'option' => $choice,
                            'milestones' => $_REQUEST['milestones' . $choice],
                            'objectives' => $_REQUEST['objectives' . $choice],
                            'anesthesiologist_id' => $pref_anest[$i],
                            'isValid' => 1,
                        ]);
                    }
                }
            } else {
                Option::where('date', $date)
                    ->where('resident', $resident_id)
                    ->where('option', $choice)
                    ->delete();
            }
        }

        // if data was overwritten, send a notification
        if (count($overwrittenChoices) > 0) {
            $this->notifyResidentOverwrittenPreferences(
                $residentName,
                $_SERVER['HTTP_EMAIL'],
                $residentName,
                $date,
                $overwrittenChoices
            );
        }

        return view('schedules.resident.schedule_update');
    }

    public function postSubmit($day = null)
    {
        $this->insertOption();
        return view('schedules.resident.schedule_update');
    }

    public function clearOption($date)
    {
        $resident_id = Resident::where('email', $_SERVER['HTTP_EMAIL'])->first()->id;

        Option::where('date', $date)
            ->where('resident', $resident_id)
            ->delete();

        return view('schedules.resident.schedule_update');
    }
}
