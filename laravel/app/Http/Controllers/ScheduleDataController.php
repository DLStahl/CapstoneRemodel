<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

/**
 * Models used in ScheduleDataController
 */
use App\ScheduleData;
use App\ScheduleParser;
use App\Resident;
use App\Option;
use App\Admin;
use App\Anesthesiologist;
use App\FilterRotation;
use App\Assignment;
use App\Milestone;
use Carbon\Carbon;
use Mail;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Mail as FacadesMail;

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
    private static function updateData(array $args = array())
    {
        /**
         * Set up default input values.
         */
        $date = $args['date'];
        $leadSurgeon = !isset($args['lead_surgeon']) ? "TBD" : $args['lead_surgeon'];
        $start_time = !isset($args['start_time']) ? '00:00:00' : $args['start_time'];
        $end_time = !isset($args['end_time']) ? '23:59:59' : $args['end_time'];
		$room = !isset($args['room']) ? 'TBD' : $args['room'];
		$rotation = !isset($args['rotation']) ? 'TBD' : $args['rotation'];

        if (strcmp($leadSurgeon, "null") == 0) {
            $leadSurgeon = "TBD";
        }

		if (strcmp($room, "null") == 0) {
            $room = "TBD";
        }

		if (strcmp($rotation, "null") == 0) {
            $rotation = "TBD";
        }

        if (strcmp($start_time, "null") == 0) {
            $start_time = "00:00:00";
        }
        if (strcmp($end_time, "null") == 0) {
            $end_time = "23:59:59";
        }

        $schedule_data = null;
        // Get filtered schedule
        if (strcmp($room, "TBD") != 0){
            $schedule_data = ScheduleData::whereDate('date', $date)
                                ->whereNotNull('start_time')
                                ->whereNotNull('end_time')
                                ->where('room', $room);
        } else {
            $schedule_data = ScheduleData::whereDate('date', $date)
                                ->whereNotNull('start_time')
                                ->whereNotNull('end_time');
        }
        if (strcmp($leadSurgeon, "TBD") != 0){
            $schedule_data = $schedule_data->where('lead_surgeon', 'LIKE', "%{$leadSurgeon}%");
        }
        if (strcmp($rotation, "TBD") != 0){
            $schedule_data = $schedule_data->where('rotation', 'LIKE', "%{$rotation}%");
        }
        if (strcmp($start_time, "00:00:00") != 0){
            $schedule_data = $schedule_data->whereTime('start_time', '>=', $start_time);
        }
        if (strcmp($end_time, "23:59:59") != 0){
            $schedule_data = $schedule_data->whereTime('end_time', '<=', $end_time);
        }
        $minTime = $schedule_data->min('start_time');
        $maxTime = $schedule_data->max('end_time');
        $schedule_data = $schedule_data->orderBy('room', 'asc')->get();

        $schedule = array();
        foreach ($schedule_data as $data)
        {
            $resident = null;
            if (Assignment::where('schedule', $data['id'])->exists())
            {
                $resident_id = Assignment::where('schedule', $data['id'])->value('resident');
                $resident = Resident::where('id', $resident_id)->value('name');
            }
            array_push($schedule, array(
                'date'=>$data['date'], 
                'room'=>$data['room'], 
                'lead_surgeon'=>$data['lead_surgeon'],
                'id'=>$data['id'], 
                'resident'=>$resident, 
                'case_procedure'=>$data['case_procedure'], 
                'patient_class'=>$data['patient_class'], 
                'rotation'=>$data['rotation'], 
                'start_time'=>$data['start_time'], 
                'end_time'=>$data['end_time']
            ));
        }

		#echo gettype($schedule);
		#echo $schedule[0]['room'];
		#$element = array_pop($schedule);
		#array_unshift($schedule, $element);
		#echo $schedule[0]['room'];
		
        $result = array(
            "minTime" => $minTime,
            "maxTime" => $maxTime,
            "schedule" => $schedule
        );
        return $result;
    }

    private function processInput($room, $leadSurgeon, $rotation, $start_time_end_time)
    {
        if ($room == null && $leadSurgeon == null && $rotation == null && $start_time_end_time == null) return;

        /**
         * Get times
         */
        $tp = stripos($start_time_end_time, '_');
        $this->start_time = substr($start_time_end_time, 0, $tp);
        $this->end_time = substr($start_time_end_time, $tp + 1);

		$this->room = $room;
		$this->leadSurgeon = $leadSurgeon;
		$this->rotation = $rotation;

        if (strcmp($this->room, "null") == 0) {
            $this->room = null;
        }
		if (strcmp($this->leadSurgeon, "null") == 0) {
            $this->leadSurgeon = null;
        }
		if (strcmp($this->rotation, "null") == 0) {
            $this->rotation = null;
        }
        if (strcmp($this->start_time, "null") == 0) {
            $this->start_time = null;
        }
        if (strcmp($this->end_time, "null") == 0) {
            $this->end_time = null;
        }

    }

    // Get all rooms, surgeons and rotations of the given date
    private function getFilterOptions($date){
        $schedule = ScheduleData::where('date', $date)
                                ->whereNotNull('start_time')
                                ->whereNotNull('end_time');
        // [{room: 'Room name'}, {room: 'Room name'}, {room: 'Room name'}, ...]
        $roomsData = $schedule->select('room')->get();
        $rooms = array();
        foreach($roomsData as $room){
            array_push($rooms, $room['room']);
        }
        sort($rooms);

        // lead surgeons with duplicates
        $allLeadSurgeons = $schedule->select('lead_surgeon')->get();
        // lead surgeons without duplicates
        $noDuplicateSurgeons = array();
        foreach ($allLeadSurgeons as $surgeons){
            $leadSurgeon = $surgeons['lead_surgeon'];
            while (strlen($leadSurgeon) > 0){
                $leadPos = strpos($leadSurgeon, "\n");
                $tmp_surgeon= substr($leadSurgeon, 0, $leadPos);
                // get rid of [number] after the surgeon name
                $ep = strpos($tmp_surgeon, '[');
                if ($ep != -1){
                    $tmp_surgeon = substr($tmp_surgeon, 0, $ep);
                }
                $tmp_surgeon = trim($tmp_surgeon);
                if (!in_array($tmp_surgeon, $noDuplicateSurgeons)){
                    array_push($noDuplicateSurgeons, $tmp_surgeon);
                }
                $leadSurgeon = substr($leadSurgeon, $leadPos+1);
            }
        }
        sort($noDuplicateSurgeons);

        $rotations = array();

        $filterOptions = array(
            "rooms" => $rooms,
            "leadSurgeons" => $noDuplicateSurgeons,
            "rotations" => $rotations
        );

        return $filterOptions;
    }


    /**
     * Public functions
     */

    public function getDay($day = null, $room = null, $leadSurgeon = null, $rotation = null, $start_time_end_time=null) {
        date_default_timezone_set('America/New_York');

        $day_translation_array = array(
            "firstday" => 1,
            "secondday" => 2,
            "thirdday" => 3
        );

        // TODO: refactor all the firstday secondday thirdday stuff out
        if(!array_key_exists($day, $day_translation_array)) {
            abort(404);
        }

        $date = date("Y-m-d", strtotime("+".$day_translation_array[$day]." Weekday"));

        $this->processInput($room, $leadSurgeon, $rotation, $start_time_end_time);
        $TimeRange_ScheduleData = self::updateData(array('date' => $date, 'lead_surgeon' => $this->leadSurgeon, 'room' => $this->room, 'rotation' => $this->rotation, 'start_time' => $this->start_time, 'end_time' => $this->end_time));
        $minTime = $TimeRange_ScheduleData['minTime'];
        $maxTime = $TimeRange_ScheduleData['maxTime'];
        $schedule_data = $TimeRange_ScheduleData['schedule'];
        $filter_options = self::getFilterOptions($date);
        $rotation_options = FilterRotation::select('rotation')->distinct()->get();
        return view('schedules.resident.schedule_table',compact('minTime', 'maxTime', 'schedule_data', 'filter_options', 'rotation_options'));
    }

    public function getChoice()
    {
        /**
         * Exclude Admin from selecting preferences
         */
        if (!Resident::where('email', $_SERVER["HTTP_EMAIL"])->exists()) {
            return view('nonpermit');
        }
        // get the id from the form
        $id = $_REQUEST['schedule_id'];

		// id is stored as id1_id2_id3, need to split it to get the individual ids
		$split = explode("_", $id);

		//start with the first choice
		$choice = 1;
		// get the first choices data
        $schedule_data1 = ScheduleData::where('id', $split[0])->get();
        $input[0] = array(
            'id'=>$split[0],
            'schedule' => ScheduleData::where('id', $split[0])->get(),
            'choice'=>$choice,
            'milestones'=>Milestone::where('id', $_REQUEST['milestones1'])->get(),
            'objectives'=>$_REQUEST['objectives1']
        );

    	// get the second choices data
    	$choice++;
    	// check if the second choice exists
        if ($split[1] != 0){
        	$schedule_data2 = ScheduleData::where('id', $split[1])->get();
            $input[1] = array(
                'id'=>$split[1],
                'schedule'=>ScheduleData::where('id', $split[1])->get(),
                'choice'=>$choice,
                'milestones'=>Milestone::where('id', $_REQUEST['milestones2'])->get(),
                'objectives'=>$_REQUEST['objectives2']
            );
        } else {
            $input[1] = NULL;
        }


    	// get the third choices data
        $choice++;
    	// check if the third choice exists
        if ($split[2] != 0){
    		$schedule_data3 = ScheduleData::where('id', $split[2])->get();
            $input[2] = array(
                'id'=>$split[2],
                'schedule'=>ScheduleData::where('id', $split[2])->get(),
                'choice'=>$choice,
                'milestones'=>Milestone::where('id', $_REQUEST['milestones3'])->get(),
                'objectives'=>$_REQUEST['objectives3']
            );
        } else {
            $input[2] = NULL;
        }

        // Get previous preferences of the same date
        $date = $schedule_data1[0]['date'];
        $resident_data = Resident::where('email', $_SERVER["HTTP_EMAIL"])->get();
        $resident = $resident_data[0]['id'];
        $prevFirst = Option::where('date', $date)->where('resident', $resident)->where('option', 1)->where('isValid', 1)->get();
        $prevSecond = Option::where('date', $date)->where('resident', $resident)->where('option', 2)->where('isValid', 1)->get();
        $prevThird = Option::where('date', $date)->where('resident', $resident)->where('option', 3)->where('isValid', 1)->get();
        if (sizeof($prevFirst) > 0){
            $previous[0] = array(
                'prevPref'=> $prevFirst,
                'schedule'=> ScheduleData::where('id', $prevFirst[0]['schedule'])->get(),
                'milestone'=> Milestone::where('id', $prevFirst[0]['milestones'])->get()
            );
        } else {
            $previous[0] = NULL;
        }

        if (sizeof($prevSecond) > 0){
            $previous[1] = array(
                'prevPref'=> $prevSecond,
                'schedule'=> ScheduleData::where('id', $prevSecond[0]['schedule'])->get(),
                'milestone'=> Milestone::where('id', $prevSecond[0]['milestones'])->get()
            );
        } else {
            $previous[1] = NULL;
        }

        if (sizeof($prevThird) > 0){
            $previous[2] = array(
                'prevPref'=> $prevThird,
                'schedule'=> ScheduleData::where('id', $prevThird[0]['schedule'])->get(),
                'milestone'=> Milestone::where('id', $prevThird[0]['milestones'])->get()
            );
        } else {
            $previous[2] = NULL;
        }

        return view('schedules.resident.schedule_confirm', compact('id', 'input', 'previous'));
    }

	public function selectMilestones($id){
        // Get resident data
        $resident_data = Resident::where('email', $_SERVER["HTTP_EMAIL"])->get();
        $resident = $resident_data[0]['id'];

        // id is stored as id1_id2_id3, need to split it to get the individual ids
		$split = explode("_", $id);

        //Initiate first choice data
        $data1 = array(
          'schedule' => null,
          'attending' => null
        );
        // get information for first choice
        $choice = 1;
    	$schedule_data1 = ScheduleData::where('id', $split[0])->get();
		$attending_string = $schedule_data1[0]['lead_surgeon'];
		$attending = substr($attending_string, strpos($attending_string, "[")+1, strpos($attending_string, "]")-(strpos($attending_string, "[")+1));
		$attending1 = substr($attending_string, 0, strpos($attending_string, "["));
        // Store first choice data
        $date = $schedule_data1[0]['date'];
        $data1['schedule'] = $schedule_data1[0];
        $data1['attending'] = $attending1;

        //Initiate second choice data
        $data2 = array(
          'schedule' => null,
          'attending' => null
        );
        $choice++;
		// If the second choice exists, get information for second choice
    	if ($split[1] != 0){
    		$schedule_data2 = ScheduleData::where('id', $split[1])->get();
    		$attending_string = $schedule_data2[0]['lead_surgeon'];
    		$attending = substr($attending_string, strpos($attending_string, "[")+1, strpos($attending_string, "]")-(strpos($attending_string, "[")+1));
    		$attending2 = substr($attending_string, 0, strpos($attending_string, "["));
            // Store second choice data
            $data2['schedule'] = $schedule_data2[0];
            $data2['attending'] = $attending2;
	    }

        //Initiate second choice data
        $data3 = array(
            'schedule' => null,
            'attending' => null
        );
        $choice++;
		// If the third choice exists, get information for third choice
    	if ($split[2] != 0){
        	$schedule_data3 = ScheduleData::where('id', $split[2])->get();
    		$attending_string = $schedule_data3[0]['lead_surgeon'];
    		$attending = substr($attending_string, strpos($attending_string, "[")+1, strpos($attending_string, "]")-(strpos($attending_string, "[")+1));
    		$attending3 = substr($attending_string, 0, strpos($attending_string, "["));
            // Store second choice data
            $data3['schedule'] = $schedule_data3[0];
            $data3['attending'] = $attending3;
        }
        
        $milestones = Milestone::where('exists', 1)->get();

        $anesthesiologists = Anesthesiologist::where('updated_at', '>', Carbon::today())
            ->orderBy('last_name')
            ->get();

        return view('schedules.resident.milestone', compact('id', 'milestones', 'data1', 'data2', 'data3', 'anesthesiologists'));
	}

    public function updateMilestones($id){
        // Get resident data
        $resident_data = Resident::where('email', $_SERVER["HTTP_EMAIL"])->get();
        $resident = $resident_data[0]['id'];

        // id is stored as id1_id2_id3, need to split it to get the individual ids
        $split = explode("_", $id);

        //Initiate first choice data
        $data1 = array(
            'schedule' => null,
            'attending' => null,
            'milestone' => null,
            'objective'=> null
        );
        // get information for first choice
        $choice = 1;
        $schedule_data1 = ScheduleData::where('id', $split[0])->get();
        $attending_string = $schedule_data1[0]['lead_surgeon'];
        $attending = substr($attending_string, strpos($attending_string, "[")+1, strpos($attending_string, "]")-(strpos($attending_string, "[")+1));
        $attending1 = substr($attending_string, 0, strpos($attending_string, "["));

        // Store first choice data
        $date = $schedule_data1[0]['date'];
        $data1['schedule'] = $schedule_data1[0];
        $data1['attending'] = $attending1;
        // Find previous milestone and educational objective
        $option1 = Option::where('date', $date)->where('resident', $resident)->where('option', 1)->get();
        if (sizeof($option1) > 0){
            $milestone1 = Milestone::where('id', $option1[0]['milestones'])->get();
            if(sizeof($milestone1) > 0){
                $data1['milestone'] = $milestone1[0];
            }
            $data1['objective'] = $option1[0]['objectives'];
        }

        //Initiate second choice data
        $data2 = array(
            'schedule' => null,
            'attending' => null,
            'milestone' => null,
            'objective'=> null
        );
        $choice++;
        // If the second choice exists, get information for second choice
        if ($split[1] != 0){
            $schedule_data2 = ScheduleData::where('id', $split[1])->get();
            $attending_string = $schedule_data2[0]['lead_surgeon'];
            $attending = substr($attending_string, strpos($attending_string, "[")+1, strpos($attending_string, "]")-(strpos($attending_string, "[")+1));
            $attending2 = substr($attending_string, 0, strpos($attending_string, "["));

            // Store second choice data
            $data2['schedule'] = $schedule_data2[0];
            $data2['attending'] = $attending2;
            // Find previous milestone and educational objective
            $option2 = Option::where('date', $date)->where('resident', $resident)->where('option', 2)->get();
            if (sizeof($option2) > 0){
                $milestone2 = Milestone::where('id', $option2[0]['milestones'])->get();
                if(sizeof($milestone2) > 0){
                    $data2['milestone'] = $milestone2[0];
                }
                $data2['objective'] = $option2[0]['objectives'];
            }
        }

        //Initiate second choice data
        $data3 = array(
            'schedule' => null,
            'attending' => null,
            'milestone' => null,
            'objective'=> null
        );
        $choice++;
        // If the third choice exists, get information for third choice
        if ($split[2] != 0){
            $schedule_data3 = ScheduleData::where('id', $split[2])->get();
            $attending_string = $schedule_data3[0]['lead_surgeon'];
            $attending = substr($attending_string, strpos($attending_string, "[")+1, strpos($attending_string, "]")-(strpos($attending_string, "[")+1));
            $attending3 = substr($attending_string, 0, strpos($attending_string, "["));

            // Store second choice data
            $data3['schedule'] = $schedule_data3[0];
            $data3['attending'] = $attending3;
            // Find previous milestone and educational objective
            $option3 = Option::where('date', $date)->where('resident', $resident)->where('option', 3)->get();
            if (sizeof($option3) > 0){
                $milestone3 = Milestone::where('id', $option3[0]['milestones'])->get();
                if(sizeof($milestone3) > 0){
                    $data3['milestone'] = $milestone3[0];
                }
                $data3['objective'] = $option3[0]['objectives'];
            }
        }

        $milestones = Milestone::all();

        $anesthesiologists = Anesthesiologist::where('updated_at', '>', Carbon::today())
            ->orderBy('last_name')
            ->get();

        return view('schedules.resident.milestone_edit', compact('id', 'milestones', 'data1', 'data2', 'data3', 'anesthesiologists'));
    }

	public function notifyResidentOverwrittenPreferences($toName, $toEmail, $residentName, $date, $overwrittenChoices)
    {

		$choice = "";
		if($overwrittenChoices[0] == 1){
			$choice = "1";
		}

		if($overwrittenChoices[1] == 2){
			$choice = $choice." 2";
		}

		if($overwrittenChoices[2] == 3){
			$choice = $choice." 3";
		}

		$subject = 'REMODEL: Resident Preference '.$choice.' Overwritten for '.$date;
		$body = "Resident $residentName has overwritten OR preferences  ".$choice." for ".$date.". New preferences are now viewable on REMODEL website.";
		$heading = "Resident $residentName has overwritten OR preference ".$choice;
        $data = array('name'=>$toName, 'heading'=>$heading, 'body'=>$body);

        Mail::send('emails.mail', $data, function($message) use ($toName, $toEmail, $subject) {
            $message->to($toEmail, $toName)->subject($subject);
            $message->from('OhioStateAnesthesiology@gmail.com');
        });
		return true;
    }

    // Insert options if no preference exists.
    // Update options when overwriting preferences.
    private function insertOption()
    {
		// variables to track if the use has overwritten a preference
		$notify = false;
		$overwrittenChoices = array();

		// get the id from the form
		$id = $_REQUEST['schedule_id'];

		// id is stored as id1_id2_id3, need to split it to get the individual ids
        $split = explode("_", $id);

		// get the schedule data for the first choice
        $schedule_data1 = ScheduleData::where('id', $split[0])->get();

        // Get resident
        $resident_data = Resident::where('email', $_SERVER["HTTP_EMAIL"])->get();
        $resident = $resident_data[0]['id'];
        $residentName = $resident_data[0]['name'];

		// Get date. (Dates are the same for 3 preferences)
        $date = $schedule_data1[0]['date'];

		//insert first choice data
		$choice = 1;
        // Get attending id
        $attending_string = $schedule_data1[0]['lead_surgeon'];
        $attending = substr($attending_string, strpos($attending_string, "[")+1,
                            strpos($attending_string, "]")-(strpos($attending_string, "[")+1));

        // Update or insert option 1 data
        if (Option::where('date', $date)
                    ->where('resident', $resident)
                    ->where('option',$choice)
                    ->count() != 0)
        {
			// generate notification and delete data
			$notify = true;
			$overwrittenChoices[0] = 1;

            Option::where('date', $date)
                    ->where('resident', $resident)
                    ->where('option',$choice)
                    ->update([
                        'schedule' => $split[0],
                        'attending' => $attending,
                        'milestones'=>$_REQUEST['milestones1'],
                        'objectives'=>$_REQUEST['objectives1'],
                        'isValid'=>1
                    ]);
        } else {
        	$overwrittenChoices[0] = 0;
            // Insert data
            Option::insert(
                ['date' => $date, 'resident' => $resident, 'schedule' => $split[0],
                'attending' => $attending, 'option' => $choice, 'milestones'=>$_REQUEST['milestones1'],
                'objectives'=>$_REQUEST['objectives1'], 'isValid'=>1]
            );
        }


        //Update second choice data
        $choice++;
        $schedule_data2 = NULL;
        $attending = NULL;
        // Insert second choice data if it exists
        if($split[1] != 0){
            // Get schedule data of the 2nd preference
            $schedule_data2 = ScheduleData::where('id', $split[1])->get();
            // Get attending id
            $attending_string = $schedule_data2[0]['lead_surgeon'];
            $attending = substr($attending_string, strpos($attending_string, "[")+1,
                              strpos($attending_string, "]")-(strpos($attending_string, "[")+1));
        }

        // Update/Insert option 2 data
        if (Option::where('date', $date)
                    ->where('resident', $resident)
                    ->where('option',$choice)
                    ->count() != 0)
        {
  			// generate notification and update/delete data
  			$nofity = true;
  			$overwrittenChoices[1] = 2;
            // If user enters 2nd choice, update option 2 data; otherwise, delete old 2nd choice data.
            if (!is_null($schedule_data2)){
                Option::where('date', $date)
                        ->where('resident', $resident)
                        ->where('option',$choice)
                        ->update([
                            'schedule' => $split[1],
                            'attending' => $attending,
                            'milestones'=>$_REQUEST['milestones2'],
                            'objectives'=>$_REQUEST['objectives2'],
                            'isValid'=>1
                        ]);
            } else {
                Option::where('date', $date)
                        ->where('resident', $resident)
                        ->where('option',$choice)
                        ->delete();
            }
        } else {
            $overwrittenChoices[1] = 0;
            if (!is_null($schedule_data2)){
                // Insert data
                Option::insert([
                    'date' => $date,
                    'resident' => $resident,
                    'schedule' => $split[1],
                    'attending' => $attending,
                    'option' => $choice,
                    'milestones'=>$_REQUEST['milestones2'],
                    'objectives'=>$_REQUEST['objectives2'],
                    'isValid'=>1
                ]);
            }
        }



		//Third choice data
		$choice++;
        $schedule_data3 = NULL;
        $attending = NULL;
        // Insert third choice data if it exists
        if($split[2] != 0){
            $schedule_data3 = ScheduleData::where('id', $split[2])->get();
            // Get attending id
            $attending_string = $schedule_data3[0]['lead_surgeon'];
            $attending = substr($attending_string, strpos($attending_string, "[")+1,
                                strpos($attending_string, "]")-(strpos($attending_string, "[")+1));


        }
		//Insert/Update old option 3 data
		if (Option::where('date', $date)
		          ->where('resident', $resident)
		          ->where('option',$choice)
		          ->count() != 0)
		  {
			// generate notification and delete data
			$nofity = true;
			$overwrittenChoices[2] = 3;
            // If user enters 3rd choice, update option 3 data; otherwise, make old 3nd choice data invalid.
            if(!is_null($schedule_data3)){
                Option::where('date', $date)
                        ->where('resident', $resident)
                        ->where('option',$choice)
                        ->update([
                            'schedule' => $split[2],
                            'attending' => $attending,
                            'milestones'=>$_REQUEST['milestones3'],
                            'objectives'=>$_REQUEST['objectives3'],
                            'isValid'=>1
                        ]);
            } else {
                Option::where('date', $date)
                  ->where('resident', $resident)
                  ->where('option',$choice)
                  ->delete();
                  // ->update(['isValid'=>0]);
              }
		  }
		else {
			$overwrittenChoices[2] = 0;
            if(!is_null($schedule_data3)){
                // Insert data
                Option::insert([
                    'date' => $date,
                    'resident' => $resident,
                    'schedule' => $split[2],
                    'attending' => $attending,
                    'option' => $choice,
                    'milestones'=>$_REQUEST['milestones3'],
                    'objectives'=>$_REQUEST['objectives3'],
                    'isValid'=>1]
                );
            }
		}


		// data was overwritten, send a notification
		if($notify == true){
			// please make sure to change the email here
			self::notifyResidentOverwrittenPreferences('', $_SERVER["HTTP_EMAIL"], $residentName, $date, $overwrittenChoices);
		}
    }

	public function clearOption($date)
    {
        // Get resident
        $resident_data = Resident::where('email', $_SERVER["HTTP_EMAIL"])->get();
        $resident = $resident_data[0]['id'];

		//delete first choice data
		$choice = 1;
        if (Option::where('date', $date)
                    ->where('resident', $resident)
                    ->where('option',$choice)
                    ->count() != 0)
        {
            Option::where('date', $date)
                    ->where('resident', $resident)
                    ->where('option',$choice)
                    ->delete();
        }
		//delete second choice data
		$choice++;
		if (Option::where('date', $date)
                    ->where('resident', $resident)
                    ->where('option',$choice)
                    ->count() != 0)
        {
            Option::where('date', $date)
                    ->where('resident', $resident)
                    ->where('option',$choice)
                    ->delete();
        }
		//delete third choice data
		$choice++;
        if (Option::where('date', $date)
                    ->where('resident', $resident)
                    ->where('option',$choice)
                    ->count() != 0)
        {
            Option::where('date', $date)
                    ->where('resident', $resident)
                    ->where('option',$choice)
                    ->delete();
        }
		return view('schedules.resident.schedule_update');
    }

    public function postSubmit($day=null)
    {
        self::insertOption();
        return view('schedules.resident.schedule_update');
    }

}
