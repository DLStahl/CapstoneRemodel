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
use App\Assignment;
use App\Milestone;
use Mail;
use GuzzleHttp\Client;

class ScheduleDataController extends Controller
{

    /**
     * Protected members
     */
    protected $room = null;
	protected $leadSurgeon = null;
	protected $patient_class = null;
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
		$patient_class = !isset($args['patient_class']) ? 'TBD' : $args['patient_class'];




        if (strcmp($leadSurgeon, "null") == 0) {
            $leadSurgeon = "TBD";
        }

		if (strcmp($room, "null") == 0) {
            $room = "TBD";
        }

		if (strcmp($patient_class, "null") == 0) {
            $patient_class = "TBD";
        }

        if (strcmp($start_time, "null") == 0) {
            $start_time = "00:00:00";
        }
        if (strcmp($end_time, "null") == 0) {
            $end_time = "23:59:59";
        }

        $schedule_data = null;
		// check that all filters are filled out
        if (strcmp($room, "TBD") != 0 && strcmp($leadSurgeon, "TBD") != 0 && strcmp($patient_class, "TBD") != 0 && strcmp($start_time, "00:00:00") != 0 && strcmp($end_time, "23:59:59") != 0)
        {
             $schedule_data = ScheduleData::whereDate('date', $date)
                                ->where('room', $room)
								->where('lead_surgeon', 'LIKE', "%{$leadSurgeon}%")
								->where('patient_class', 'LIKE', "%{$patient_class}%")
                                ->whereTime('start_time', '>=', $start_time)
                                ->whereTime('end_time', '<=', $end_time)
                                ->orderBy('room', 'asc')
                                ->get();
        }
		// only 4 filters are filled out
		elseif (strcmp($room, "TBD") != 0 && strcmp($leadSurgeon, "TBD") != 0 && strcmp($patient_class, "TBD") != 0 && strcmp($start_time, "00:00:00") != 0)
        {
             $schedule_data = ScheduleData::whereDate('date', $date)
                                ->where('room', $room)
								->where('lead_surgeon', 'LIKE', "%{$leadSurgeon}%")
								->where('patient_class', 'LIKE', "%{$patient_class}%")
                                ->whereTime('start_time', '>=', $start_time)
                                ->orderBy('room', 'asc')
                                ->get();
        }
		elseif (strcmp($room, "TBD") != 0 && strcmp($leadSurgeon, "TBD") != 0 && strcmp($patient_class, "TBD") != 0 && strcmp($end_time, "23:59:59") != 0)
        {
             $schedule_data = ScheduleData::whereDate('date', $date)
                                ->where('room', $room)
								->where('lead_surgeon', 'LIKE', "%{$leadSurgeon}%")
								->where('patient_class', 'LIKE', "%{$patient_class}%")
                                ->whereTime('end_time', '<=', $end_time)
                                ->orderBy('room', 'asc')
                                ->get();
        }
		elseif (strcmp($room, "TBD") != 0 && strcmp($leadSurgeon, "TBD") != 0 && strcmp($start_time, "00:00:00") != 0 && strcmp($end_time, "23:59:59") != 0)
        {
             $schedule_data = ScheduleData::whereDate('date', $date)
                                ->where('room', $room)
								->where('lead_surgeon', 'LIKE', "%{$leadSurgeon}%")
                                ->whereTime('start_time', '>=', $start_time)
                                ->whereTime('end_time', '<=', $end_time)
                                ->orderBy('room', 'asc')
                                ->get();
        }
		elseif (strcmp($room, "TBD") != 0 && strcmp($patient_class, "TBD") != 0 && strcmp($start_time, "00:00:00") != 0 && strcmp($end_time, "23:59:59") != 0)
        {
             $schedule_data = ScheduleData::whereDate('date', $date)
                                ->where('room', $room)
								->where('patient_class', 'LIKE', "%{$patient_class}%")
                                ->whereTime('start_time', '>=', $start_time)
                                ->whereTime('end_time', '<=', $end_time)
                                ->orderBy('room', 'asc')
                                ->get();
        }
		elseif ( strcmp($leadSurgeon, "TBD") != 0 && strcmp($patient_class, "TBD") != 0 && strcmp($start_time, "00:00:00") != 0 && strcmp($end_time, "23:59:59") != 0)
        {
             $schedule_data = ScheduleData::whereDate('date', $date)
								->where('lead_surgeon', 'LIKE', "%{$leadSurgeon}%")
								->where('patient_class', 'LIKE', "%{$patient_class}%")
                                ->whereTime('start_time', '>=', $start_time)
                                ->whereTime('end_time', '<=', $end_time)
                                ->orderBy('room', 'asc')
                                ->get();
        }
		// only 3 filters are filled out
		elseif (strcmp($room, "TBD") != 0 && strcmp($leadSurgeon, "TBD") != 0 && strcmp($patient_class, "TBD") != 0)
        {
             $schedule_data = ScheduleData::whereDate('date', $date)
                                ->where('room', $room)
								->where('lead_surgeon', 'LIKE', "%{$leadSurgeon}%")
								->where('patient_class', $patient_class)
                                ->orderBy('room', 'asc')
                                ->get();
        }
		elseif (strcmp($room, "TBD") != 0 && strcmp($leadSurgeon, "TBD") != 0  && strcmp($end_time, "23:59:59") != 0)
        {
             $schedule_data = ScheduleData::whereDate('date', $date)
                                ->where('room', $room)
								->where('lead_surgeon', 'LIKE', "%{$leadSurgeon}%")
                                ->whereTime('end_time', '<=', $end_time)
                                ->orderBy('room', 'asc')
                                ->get();
        }
		elseif (strcmp($room, "TBD") != 0 && strcmp($leadSurgeon, "TBD") != 0 &&  strcmp($start_time, "00:00:00") != 0 )
        {
             $schedule_data = ScheduleData::whereDate('date', $date)
                                ->where('room', $room)
								->where('lead_surgeon', 'LIKE', "%{$leadSurgeon}%")
                                ->whereTime('start_time', '>=', $start_time)
                                ->orderBy('room', 'asc')
                                ->get();
        }
		elseif (strcmp($room, "TBD") != 0 && strcmp($start_time, "00:00:00") != 0 && strcmp($end_time, "23:59:59") != 0)
        {
             $schedule_data = ScheduleData::whereDate('date', $date)
                                ->where('room', $room)
                                ->whereTime('start_time', '>=', $start_time)
                                ->whereTime('end_time', '<=', $end_time)
                                ->orderBy('room', 'asc')
                                ->get();
        }
		elseif (strcmp($room, "TBD") != 0 && strcmp($patient_class, "TBD") != 0  && strcmp($end_time, "23:59:59") != 0)
        {
             $schedule_data = ScheduleData::whereDate('date', $date)
                                ->where('room', $room)
								->where('patient_class', 'LIKE', "%{$patient_class}%")
                                ->whereTime('end_time', '<=', $end_time)
                                ->orderBy('room', 'asc')
                                ->get();
        }
		elseif (strcmp($room, "TBD") != 0 && strcmp($patient_class, "TBD") != 0 && strcmp($start_time, "00:00:00") != 0 )
        {
             $schedule_data = ScheduleData::whereDate('date', $date)
                                ->where('room', $room)
								->where('patient_class', 'LIKE', "%{$patient_class}%")
                                ->whereTime('start_time', '>=', $start_time)
                                ->orderBy('room', 'asc')
                                ->get();
        }
		elseif ( strcmp($patient_class, "TBD") != 0 && strcmp($start_time, "00:00:00") != 0 && strcmp($end_time, "23:59:59") != 0)
        {
             $schedule_data = ScheduleData::whereDate('date', $date)
								->where('patient_class', 'LIKE', "%{$patient_class}%")
                                ->whereTime('start_time', '>=', $start_time)
                                ->whereTime('end_time', '<=', $end_time)
                                ->orderBy('room', 'asc')
                                ->get();
        }
		elseif (strcmp($leadSurgeon, "TBD") != 0  && strcmp($start_time, "00:00:00") != 0 && strcmp($end_time, "23:59:59") != 0)
        {
             $schedule_data = ScheduleData::whereDate('date', $date)
								->where('lead_surgeon', 'LIKE', "%{$leadSurgeon}%")
                                ->whereTime('start_time', '>=', $start_time)
                                ->whereTime('end_time', '<=', $end_time)
                                ->orderBy('room', 'asc')
                                ->get();
        }
		elseif (strcmp($leadSurgeon, "TBD") != 0 && strcmp($patient_class, "TBD") != 0  && strcmp($end_time, "23:59:59") != 0)
        {
             $schedule_data = ScheduleData::whereDate('date', $date)
								->where('lead_surgeon', 'LIKE', "%{$leadSurgeon}%")
								->where('patient_class', 'LIKE', "%{$patient_class}%")
                                ->whereTime('end_time', '<=', $end_time)
                                ->orderBy('room', 'asc')
                                ->get();
        }
		elseif ( strcmp($leadSurgeon, "TBD") != 0 && strcmp($patient_class, "TBD") != 0 && strcmp($start_time, "00:00:00") != 0 )
        {
             $schedule_data = ScheduleData::whereDate('date', $date)
								->where('lead_surgeon', 'LIKE', "%{$leadSurgeon}%")
								->where('patient_class', 'LIKE', "%{$patient_class}%")
                                ->whereTime('start_time', '>=', $start_time)
                                ->orderBy('room', 'asc')
                                ->get();
        }
		// only 2 filters are filled out
		elseif (strcmp($room, "TBD") != 0 && strcmp($leadSurgeon, "TBD") != 0 )
        {
             $schedule_data = ScheduleData::whereDate('date', $date)
                                ->where('room', $room)
								->where('lead_surgeon', 'LIKE', "%{$leadSurgeon}%")
                                ->orderBy('room', 'asc')
                                ->get();
        }
		elseif (strcmp($room, "TBD") != 0 && strcmp($patient_class, "TBD") != 0 )
        {
             $schedule_data = ScheduleData::whereDate('date', $date)
                                ->where('room', $room)
								->where('patient_class', 'LIKE', "%{$patient_class}%")
                                ->orderBy('room', 'asc')
                                ->get();
        }
		elseif (strcmp($leadSurgeon, "TBD") != 0 && strcmp($patient_class, "TBD") != 0 )
        {
             $schedule_data = ScheduleData::whereDate('date', $date)
								->where('lead_surgeon','LIKE', "%{$leadSurgeon}%")
								->where('patient_class', 'LIKE', "%{$patient_class}%")
                                ->orderBy('room', 'asc')
                                ->get();
        }
		elseif (strcmp($room, "TBD") != 0 && strcmp($end_time, "23:59:59") != 0)
        {
             $schedule_data = ScheduleData::whereDate('date', $date)
                                ->where('room', $room)
                                ->whereTime('end_time', '<=', $end_time)
                                ->orderBy('room', 'asc')
                                ->get();
        }
		elseif (strcmp($room, "TBD") != 0 && strcmp($start_time, "00:00:00") != 0)
        {
             $schedule_data = ScheduleData::whereDate('date', $date)
                                ->where('room', $room)
                                ->whereTime('start_time', '>=', $start_time)
                                ->orderBy('room', 'asc')
                                ->get();
        }
		elseif (strcmp($leadSurgeon, "TBD") != 0  && strcmp($start_time, "00:00:00") != 0 )
        {
             $schedule_data = ScheduleData::whereDate('date', $date)
								->where('lead_surgeon', 'LIKE', "%{$leadSurgeon}%")
                                ->whereTime('start_time', '>=', $start_time)
                                ->orderBy('room', 'asc')
                                ->get();
        }
		elseif (strcmp($leadSurgeon, "TBD") != 0  && strcmp($end_time, "23:59:59") != 0)
        {
             $schedule_data = ScheduleData::whereDate('date', $date)
								->where('lead_surgeon', 'LIKE', "%{$leadSurgeon}%")
                                ->whereTime('end_time', '<=', $end_time)
                                ->orderBy('room', 'asc')
                                ->get();
        }
		elseif (strcmp($patient_class, "TBD") != 0 && strcmp($start_time, "00:00:00") != 0)
        {
             $schedule_data = ScheduleData::whereDate('date', $date)
								->where('patient_class', 'LIKE', "%{$patient_class}%")
                                ->whereTime('start_time', '>=', $start_time)
                                ->orderBy('room', 'asc')
                                ->get();
        }
		elseif (strcmp($patient_class, "TBD") != 0 && strcmp($end_time, "23:59:59") != 0)
        {
             $schedule_data = ScheduleData::whereDate('date', $date)
								->where('patient_class','LIKE', "%{$patient_class}%")
                                ->whereTime('end_time', '<=', $end_time)
                                ->orderBy('room', 'asc')
                                ->get();
        }
		elseif (strcmp($start_time, "00:00:00") != 0 && strcmp($end_time, "23:59:59") != 0)
        {
             $schedule_data = ScheduleData::whereDate('date', $date)
                                ->whereTime('start_time', '>=', $start_time)
                                ->whereTime('end_time', '<=', $end_time)
                                ->orderBy('room', 'asc')
                                ->get();
        }
		// only 1 filter is filled out
        elseif (strcmp($room, "TBD") != 0)
        {
            $schedule_data = ScheduleData::whereDate('date', $date)
                                ->where('room', $room)
                                ->orderBy('room', 'asc')
                                ->get();
        }
		elseif (strcmp($patient_class, "TBD") != 0)
        {
            $schedule_data = ScheduleData::whereDate('date', $date)
								->where('patient_class', 'LIKE', "%{$patient_class}%")
                                ->orderBy('room', 'asc')
                                ->get();
        }
		elseif (strcmp($leadSurgeon, "TBD") != 0)
        {
            $schedule_data = ScheduleData::whereDate('date', $date)
								->where('lead_surgeon', 'LIKE', "%{$leadSurgeon}%")
                                ->orderBy('room', 'asc')
                                ->get();
        }
		elseif (strcmp($start_time, "00:00:00") != 0)
        {
            $schedule_data = ScheduleData::whereDate('date', $date)
                                ->whereTime('start_time', '>=', $start_time)
                                ->orderBy('room', 'asc')
                                ->get();
        }
		elseif (strcmp($end_time, "23:59:59") != 0)
        {
            $schedule_data = ScheduleData::whereDate('date', $date)
                                ->whereTime('end_time', '<=', $end_time)
                                ->orderBy('room', 'asc')
                                ->get();
        }
		else
		{
			$schedule_data = ScheduleData::whereDate('date', $date)
                                ->orderBy('room', 'asc')
                                ->get();
		}

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
                'date'=>$data['date'], 'room'=>$data['room'], 'lead_surgeon'=>$data['lead_surgeon'],
                'id'=>$data['id'], 'resident'=>$resident, 'case_procedure'=>$data['case_procedure'],
                'patient_class'=>$data['patient_class'], 'start_time'=>$data['start_time'], 'end_time'=>$data['end_time']
            ));
        }
        return $schedule;
    }

    private function processInput($room, $leadSurgeon, $patient_class, $start_time_end_time)
    {
        if ($room == null && $leadSurgeon == null && $patient_class == null && $start_time_end_time == null) return;

        /**
         * Get times
         */
        $tp = stripos($start_time_end_time, '_');
        $this->start_time = substr($start_time_end_time, 0, $tp);
        $this->end_time = substr($start_time_end_time, $tp + 1);

		$this->room = $room;
		$this->leadSurgeon = $leadSurgeon;
		$this->patient_class = $patient_class;

        if (strcmp($this->room, "null") == 0) {
            $this->room = null;
        }
		if (strcmp($this->leadSurgeon, "null") == 0) {
            $this->leadSurgeon = null;
        }
		if (strcmp($this->patient_class, "null") == 0) {
            $this->patient_class = null;
        }
        if (strcmp($this->start_time, "null") == 0) {
            $this->start_time = null;
        }
        if (strcmp($this->end_time, "null") == 0) {
            $this->end_time = null;
        }

    }


    /**
     * Public functions
     */
    public function getFirstDay($room = null, $leadSurgeon = null, $patient_class = null, $start_time_end_time=null)
    {
        // // Test
        // $parser = new ScheduleParser("20180614");
        // $parser->processScheduleData();
        date_default_timezone_set('America/New_York');
        $year = date("o", strtotime('+1 day'));
        $mon = date('m',strtotime('+1 day'));
        $day = date('j',strtotime('+1 day'));
        if (date("l", strtotime('today'))=='Friday') {
            $year = date("o", strtotime('+3 day'));
            $mon = date('m',strtotime('+3 day'));
            $day = date('j',strtotime('+3 day'));
        } else if (date("l", strtotime('today'))=='Saturday') {
            $year = date("o", strtotime('+2 day'));
            $mon = date('m',strtotime('+2 day'));
            $day = date('j',strtotime('+2 day'));
        }

        $date =  $year.'-'.$mon.'-'.$day;

        $this->processInput($room, $leadSurgeon, $patient_class, $start_time_end_time);
        $schedule_data = self::updateData(array('date' => $date, 'lead_surgeon' => $this->leadSurgeon, 'room' => $this->room, 'patient_class' => $this->patient_class,
                                                'start_time' => $this->start_time, 'end_time' => $this->end_time));
        $flag = 1;

        return view('schedules.resident.schedule_table',compact('schedule_data', 'year', 'mon', 'day', 'flag'));

    }

    public function getSecondDay($room = null, $leadSurgeon = null, $patient_class = null, $start_time_end_time=null)
    {
        date_default_timezone_set('America/New_York');
        $year = date("o", strtotime('+2 day'));
        $mon = date('m',strtotime('+2 day'));
        $day = date('j',strtotime('+2 day'));
        if (date("l", strtotime('today'))=='Thursday' || date("l", strtotime('today'))=='Friday') {
            $year = date("o", strtotime('+4 day'));
            $mon = date('m',strtotime('+4 day'));
            $day = date('j',strtotime('+4 day'));
        } else if (date("l", strtotime('today'))=='Saturday') {
            $year = date("o", strtotime('+3 day'));
            $mon = date('m',strtotime('+3 day'));
            $day = date('j',strtotime('+3 day'));
        }

        $date =  $year.'-'.$mon.'-'.$day;

        $this->processInput($room, $leadSurgeon, $patient_class, $start_time_end_time);
        $schedule_data = self::updateData(array('date' => $date, 'lead_surgeon' => $this->leadSurgeon, 'room' => $this->room, 'patient_class' => $this->patient_class,
                                                'start_time' => $this->start_time, 'end_time' => $this->end_time));
        $flag = 2;

        return view('schedules.resident.schedule_table',compact('schedule_data', 'year', 'mon', 'day', 'flag'));
    }

    public function getThirdDay($room = null, $leadSurgeon = null, $patient_class = null, $start_time_end_time=null)
    {
        date_default_timezone_set('America/New_York');
        $year = date("o", strtotime('+3 day'));
        $mon = date('m',strtotime('+3 day'));
        $day = date('j',strtotime('+3 day'));
        if (date("l", strtotime('today'))=='Wednesday' || date("l", strtotime('today'))=='Thursday' || date("l", strtotime('today'))=='Friday') {
            $year = date("o", strtotime('+5 day'));
            $mon = date('m',strtotime('+5 day'));
            $day = date('j',strtotime('+5 day'));
        } else if (date("l", strtotime('today'))=='Saturday') {
            $year = date("o", strtotime('+4 day'));
            $mon = date('m',strtotime('+4 day'));
            $day = date('j',strtotime('+4 day'));
        }

        $date =  $year.'-'.$mon.'-'.$day;

        $this->processInput($room, $leadSurgeon, $patient_class, $start_time_end_time);
        $schedule_data = self::updateData(array('date' => $date, 'lead_surgeon' => $this->leadSurgeon, 'room' => $this->room, 'patient_class' => $this->patient_class,
                                                'start_time' => $this->start_time, 'end_time' => $this->end_time));
        $flag = 3;

        return view('schedules.resident.schedule_table',compact('schedule_data', 'year', 'mon', 'day', 'flag'));

    }

    // public function getChoice($id)
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
            'id'=>$split[0], 'choice'=>$choice
        );

    	// get the second choices data
    	$choice++;
    	// check if the second choice exists
        if ($split[1] != 0){
        	$schedule_data2 = ScheduleData::where('id', $split[1])->get();
        } else {
        	$schedule_data2 = NULL;
        }
        $input[1] = array(
        	'id'=>$split[1], 'choice'=>$choice
 		);

    	// get the third choices data
        $choice++;
    	// check if the third choice exists
        if ($split[2] != 0){
        		$schedule_data3 = ScheduleData::where('id', $split[2])->get();
        } else {
            $schedule_data3 = NULL;
        }
        $input[2] = array(
            'id'=>$split[2], 'choice'=>$choice
        );

        $milestones1=$_REQUEST['milestones1'];
        $objectives1=$_REQUEST['objectives1'];
        return view('schedules.resident.schedule_confirm', compact('schedule_data1', 'schedule_data2', 'schedule_data3','input', 'milestones1', 'objectives1'));
    }

	public function selectMilestones($id){

		// id is stored as id1_id2_id3, need to split it to get the individual ids
		$split = explode("_", $id);

		// get the schedule data for the 3 choices
    	$schedule_data1 = ScheduleData::where('id', $split[0])->get();
		// get information for first choice
		$choice = 1;
		$resident_data = Resident::where('email', $_SERVER["HTTP_EMAIL"])->get();
		$resident = $resident_data[0]['id'];
		$attending_string = $schedule_data1[0]['lead_surgeon'];
		$attending = substr($attending_string, strpos($attending_string, "[")+1, strpos($attending_string, "]")-(strpos($attending_string, "[")+1));

		//save the room and attending needed for the milestone page
		$room1 = $schedule_data1[0]['room'];
		$attending1 = substr($attending_string, 0, strpos($attending_string, "["));

		// If the second choice exists, get information for second choice
    	if($split[1] != 0){
    		$schedule_data2 = ScheduleData::where('id', $split[1])->get();
    		$choice++;
    		$attending_string = $schedule_data2[0]['lead_surgeon'];
    		$attending = substr($attending_string, strpos($attending_string, "[")+1, strpos($attending_string, "]")-(strpos($attending_string, "[")+1));

    		//save the room and attending needed for the milestone page
    		$room2 = $schedule_data2[0]['room'];
    		$attending2 = substr($attending_string, 0, strpos($attending_string, "["));
	    } else {
	        $room2 = NULL;
	        $attending2 = NULL;
	    }
		// If the third choice exists, get information for third choice
    	if($split[2] != 0){
        	$schedule_data3 = ScheduleData::where('id', $split[2])->get();
    		$choice++;
    		$attending_string = $schedule_data3[0]['lead_surgeon'];
    		$attending = substr($attending_string, strpos($attending_string, "[")+1, strpos($attending_string, "]")-(strpos($attending_string, "[")+1));
    		//save the room and attending needed for the milestone page
    		$room3 = $schedule_data3[0]['room'];
    		$attending3 = substr($attending_string, 0, strpos($attending_string, "["));
	    } else {
	        $room3 = NULL;
	        $attending3 = NULL;
	    }

      $milestones = Milestone::all();

		return view('schedules.resident.milestone', compact('room1', 'attending1', 'room2', 'attending2', 'room3', 'attending3', 'id', 'milestones'));
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

    private function insertOption()
    {
		// variables to track if the use has overwritten a preference
		$notify = false;
		$overwrittenChoices = array();


		// get the id from the form
		$id = $_REQUEST['schedule_id'];

        /**
         * Retrieve schedule data from schedule_data table
         */

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
        /**
         * Remove old option 1 data
         */
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
                    ->delete();
        } else {
        	$overwrittenChoices[0] = 0;
        }
        // Insert data
        Option::insert(
            ['date' => $date, 'resident' => $resident, 'schedule' => $split[0],
            'attending' => $attending, 'option' => $choice, 'milestones'=>$_REQUEST['milestones1'],
            'objectives'=>$_REQUEST['objectives1'], 'isValid'=>1]
        );

        //insert second choice data
        $choice++;
        /**
         * Remove old option 2 data
         */
        if (Option::where('date', $date)
                    ->where('resident', $resident)
                    ->where('option',$choice)
                    ->count() != 0)
        {
  			// generate notification and delete data
  			$nofity = true;
  			$overwrittenChoices[1] = 2;

            Option::where('date', $date)
                    ->where('resident', $resident)
                    ->where('option',$choice)
                    ->delete();
        } else {
          $overwrittenChoices[1] = 0;
        }
        // Insert second choice data if it exists
        if($split[1] != 0){
			// Get schedule data of the 2nd preference
			$schedule_data2 = ScheduleData::where('id', $split[1])->get();
			// Get attending id
			$attending_string = $schedule_data2[0]['lead_surgeon'];
			$attending = substr($attending_string, strpos($attending_string, "[")+1,
			                  strpos($attending_string, "]")-(strpos($attending_string, "[")+1));

			// Insert data
			Option::insert(
			  ['date' => $date, 'resident' => $resident, 'schedule' => $split[1],
			  'attending' => $attending, 'option' => $choice, 'milestones'=>$_REQUEST['milestones2'],
			  'objectives'=>$_REQUEST['objectives2'], 'isValid'=>1]
			);
        }


		//insert third choice data
		$choice++;
		/**
		* Remove old option 3 data
		*/
		if (Option::where('date', $date)
		          ->where('resident', $resident)
		          ->where('option',$choice)
		          ->count() != 0)
		  {
			// generate notification and delete data
			$nofity = true;
			$overwrittenChoices[2] = 3;

			Option::where('date', $date)
			      ->where('resident', $resident)
			      ->where('option',$choice)
			      ->delete();
		  }
		else {
			$overwrittenChoices[2] = 0;
		}

        // Insert third choice data if it exists
		if($split[2] != 0){
			$schedule_data3 = ScheduleData::where('id', $split[2])->get();
			// Get attending id
			$attending_string = $schedule_data3[0]['lead_surgeon'];
			$attending = substr($attending_string, strpos($attending_string, "[")+1,
			                    strpos($attending_string, "]")-(strpos($attending_string, "[")+1));

			// Insert data
			Option::insert(
			    ['date' => $date, 'resident' => $resident, 'schedule' => $split[2],
			    'attending' => $attending, 'option' => $choice, 'milestones'=>$_REQUEST['milestones3'],
			    'objectives'=>$_REQUEST['objectives3'], 'isValid'=>1]
			);
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
