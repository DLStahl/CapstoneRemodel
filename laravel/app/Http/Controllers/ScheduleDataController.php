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

class ScheduleDataController extends Controller
{

    /**
     * Protected members
     */
    protected $doctor = null;
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
        $doctor = !isset($args['lead_surgeon']) ? "TBD" : $args['lead_surgeon'];
        $start_time = !isset($args['start_time']) ? '00:00:00' : $args['start_time'];
        $end_time = !isset($args['end_time']) ? '23:59:59' : $args['end_time'];

        if (strcmp($doctor, "null") == 0) {
            $doctor = "TBD";
        }
        if (strcmp($start_time, "null") == 0) {
            $start_time = "00:00:00";
        }
        if (strcmp($end_time, "null") == 0) {
            $end_time = "23:59:59";
        }

        $schedule_data = null;
        if (strcmp($doctor, "TBD") == 0)
        {
            $schedule_data = ScheduleData::whereDate('date', $date)
                                ->where('room', '<>', '')
                                ->whereTime('start_time', '>=', $start_time)
                                ->whereTime('start_time', '<>', '00:00:00')
                                ->whereTime('end_time', '<=', $end_time)
                                ->orderBy('room', 'asc')
                                ->get();
        }
        else
        {           
            $schedule_data = ScheduleData::whereDate('date', $date)
                                ->where('lead_surgeon', $doctor)
                                ->where('room', '<>', '')
                                ->whereTime('start_time', '>=', $start_time)
                                ->whereTime('start_time', '<>', '00:00:00')
                                ->whereTime('end_time', '<=', $end_time)
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

    private function processInput($doctor_start_time_end_time)
    {
        if ($doctor_start_time_end_time == null) return;

        $tp = stripos($doctor_start_time_end_time, '_');
        
        /**
         * Get doctor
         */
        $this->doctor = substr($doctor_start_time_end_time, 0, $tp);
        str_replace("%20", " ", $this->doctor);

        /**
         * Get times
         */
        $time_string = substr($doctor_start_time_end_time, $tp + 1);
        $tp = stripos($time_string, '_');
        $this->start_time = substr($time_string, 0, $tp);
        $this->end_time = substr($time_string, $tp + 1);

        if (strcmp($this->doctor, "null") == 0) {
            $this->doctor = null;
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
    public function getFirstDay($doctor_start_time_end_time=null)
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

        $this->processInput($doctor_start_time_end_time);
        $schedule_data = self::updateData(array('date' => $date, 'lead_surgeon' => $this->doctor,
                                                'start_time' => $this->start_time, 'end_time' => $this->end_time));
        $flag = 1;

        return view('schedules.resident.schedule_table',compact('schedule_data', 'year', 'mon', 'day', 'flag'));
 
    }

    public function getSecondDay($doctor_start_time_end_time=null)
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

        $this->processInput($doctor_start_time_end_time);
        $schedule_data = self::updateData(array('date' => $date, 'lead_surgeon' => $this->doctor,
                                                'start_time' => $this->start_time, 'end_time' => $this->end_time));
        $flag = 2;

        return view('schedules.resident.schedule_table',compact('schedule_data', 'year', 'mon', 'day', 'flag'));
    }

    public function getThirdDay($doctor_start_time_end_time=null)
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

        $this->processInput($doctor_start_time_end_time);
        $schedule_data = self::updateData(array('date' => $date, 'lead_surgeon' => $this->doctor,
                                                'start_time' => $this->start_time, 'end_time' => $this->end_time));
        $flag = 3;

        return view('schedules.resident.schedule_table',compact('schedule_data', 'year', 'mon', 'day', 'flag'));

    }

    public function getChoice($id, $choice, $flag=null)
    {
        /**
         * Exclude Admin from selecting preferences
         */
        if (!Resident::where('email', $_SERVER["HTTP_EMAIL"])->exists()) {
            return view('nonpermit');
        }

        $schedule_data = ScheduleData::where('id', $id)->get();
        $input = array(
            'id'=>$id, 'choice'=>$choice
        );
        $choice = (int)$choice;
        
        if ($flag != null)
        {
            /**
             * Route to milestone selection page
             */

            $resident_data = Resident::where('email', $_SERVER["HTTP_EMAIL"])->get();
            $resident = $resident_data[0]['id'];
            $attending_string = $schedule_data[0]['lead_surgeon'];
            $attending = substr($attending_string, strpos($attending_string, "[")+1, strpos($attending_string, "]")-(strpos($attending_string, "[")+1));

            /**
             * Check whether the input is valid
             */
            if (Option::where('schedule', $id)->where('resident', $resident)->count() > 0 && 
                Option::where('schedule', $id)->where('resident', $resident)->where('option',$choice)->count() == 0)
            {
                return view('schedules.resident.schedule_error');
            }
            
            $room = $schedule_data[0]['room'];
            $attending = substr($attending_string, 0, strpos($attending_string, "["));
            return view('schedules.resident.milestone', compact('room', 'attending', 'id', 'choice'));
        }

        return view('schedules.resident.schedule_confirm', compact('schedule_data', 'input'));

    }

    private function insertOption()
    {
        /**
         * Retrieve schedule data from schedule_data table
         */
        $schedule_data = ScheduleData::where('id', $_REQUEST['schedule_id'])->get();

        /**
         * Convert choice to an integer
         */
        $choice = (int)$_REQUEST['choice'];

        // Get date
        $date = $schedule_data[0]['date'];
        // Get resident
        $resident_data = Resident::where('email', $_SERVER["HTTP_EMAIL"])->get();
        $resident = $resident_data[0]['id'];
        // Get attending id
        $attending_string = $schedule_data[0]['lead_surgeon'];
        $attending = substr($attending_string, strpos($attending_string, "[")+1, 
                            strpos($attending_string, "]")-(strpos($attending_string, "[")+1));
    
        /**
         * Remove old option data
         */
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

        // Insert data
        Option::insert(
            ['date' => $date, 'resident' => $resident, 'schedule' => $_REQUEST['schedule_id'], 
            'attending' => $attending, 'option' => $choice, 'milestones'=>$_REQUEST['milestones'], 
            'objectives'=>$_REQUEST['objectives']]
        );
    
    }

    public function postSubmit($day=null)
    {
        self::insertOption();
        return view('schedules.resident.schedule_update');
    }

}
