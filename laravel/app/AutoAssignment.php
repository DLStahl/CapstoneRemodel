<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

use App\Models\Option;
use App\Models\Assignment;
use App\Models\Resident;
use App\Models\Probability;
use App\Models\ScheduleData;
use App\Models\Rotations;
use App\Models\EvaluationForms;
use Illuminate\Support\Facades\Log;

class AutoAssignment extends Model
{
    public static function assignment($date) {
        Log::info("auto assignment");
        if (Option::where('date', $date)->where('isValid', "1")->doesntExist()) {
            return;
        }

        $residents = Resident::orderBy('id', 'asc')->get();
        self::initalizeResidentProbabilities($residents);
        
        //get array of resident ids
        $residents = Resident::pluck('id')->toArray();
        $anestsAssigned = array();
        //handle 1st preference assignments with ticketsAdded = 0
        $arrayFromFirstPref = self::assignResidentsForPref($residents, 1, 0, $date, $anestsAssigned);
        //handle 2nd preference assignments with ticketsAdded = 1
        $arrayFromSecondPref = self::assignResidentsForPref($arrayFromFirstPref[0], 2, 2, $date, $arrayFromFirstPref[1]);
        //handle 3rd preference assignments with ticketsAdded = 4
        $arrayFromThirdPref = self::assignResidentsForPref($arrayFromSecondPref[0], 3, 4, $date, $arrayFromSecondPref[1]);
        //handle residents that are not assigned with ticketsAdded = 6
        foreach($arrayFromThirdPref[0] as $unassignedResident){
            //if the resident made at least one preference then increase tickets
            if (Option::where('date', $date)
                ->where('resident', $unassignedResident)
                ->count() > 0){
                    self::increaseProbability($unassignedResident, 6);
            }
        }
    }

    // Initialize total tickets if resident is not in Probability table
        // residents = array of resident objects
    private static function initalizeResidentProbabilities($residents) {
        foreach($residents as $resident){
            if (Probability::where('resident', $resident['id'])->doesntExist()) {
                Probability::insert([
                    'resident' => $resident['id'], 
                    'total' => "0", 
                    'selected' => "0", 
                    'probability' => "0"
                ]);
            }
        }
    }

    // Assigns residents for given preference number
    //returns Array of arrays [array of residents not assigned, array of assigned anests]
        // residents = array of resident ids
        // prefNum = Preference Number corresponding to option in option table
        // ticketsToAdd = number of tickets to give resident after assignment
        // date = date of schedules that are being assigned
        // anestsAssigned = array that holds anesthesiologists already assigned where key=anest_id and value = number of assignments
    private static function assignResidentsForPref($residents, $prefNum, $ticketsToAdd, $date, $anestsAssigned) {
        $remainder = array(); // remaining unassigned residents
        $wantedSchedules = array(); //schedule ids for schedules wanted by +1 residents

        //Identify schedules residents are competiting for
        //store them in wantedSchedules if more than 1 residents wants it
        foreach($residents as $resident){
            $schedulePref = Option::where('date', $date)
                            ->where('resident', $resident)
                            ->where('option', $prefNum)
                            ->where('isValid', "1")
                            ->value('schedule');
            // if resident does not have preference add to remainder              
            if(is_null($schedulePref)){
                array_push($remainder, $resident);
            }
            // if there is exactly one pref made for the schedule,
            // assign the resident to the schedule
            else if(Option::where('date', $date)
                ->where('schedule', $schedulePref)
                ->where('option', $prefNum)
                ->where('isValid', "1")
                ->count() == 1){
                    $anestPref = Option::where('date', $date)
                                ->where('schedule', $schedulePref)
                                ->where('option', $prefNum)
                                ->where('isValid', "1")
                                ->where('resident', $resident)
                                ->value('anesthesiologist_id');
                    $anestsAssigned = self::handleAssignment($schedulePref, $resident, $date, $ticketsToAdd, $anestPref, $anestsAssigned); 
            // for competing schedules add schedule pref if it is not already in wantedSchedules array  
            } else if(!in_array($schedulePref, $wantedSchedules)){
                array_push($wantedSchedules, $schedulePref);
            }
        } 
        //determine who gets the schedule when multiple residents want the same one
        foreach($wantedSchedules as $wantedSchedule){
            $schedRotation = ScheduleData::where('id', $wantedSchedule)->value('rotation');
            // holds all valid options that want same schedule and have same preference rank                
            $competingOptions = Option::where('date', $date)
                            ->where('schedule', $wantedSchedule)
                            ->where('option', $prefNum)
                            ->where('isValid', "1")
                            ->get();
            $maxTickets = -1;
            $winnerResident = null;
            // holds options for residents on same rotation as schedule
            $onRotation = array(); 
            // holds resident ids for residents not on the schedule's rotation
            $notOnRotation = array(); 
            // identify which residents have/don't have same rotation as the schedule rotation 
            foreach($competingOptions as $competingOption){
                $resName = Resident::where('id',$competingOption['resident'])->value('name');
                $resRotations = Rotations::where('name', $resName)->get();
                $serviceId ="";
                foreach($resRotations as $resRotation){
                    if($resRotation['Start'] <= $date && $date <= $resRotation['End']){
                        $serviceId = $resRotation['Service'];
                    }
                }
                $resRotation = EvaluationForms::where('id', $serviceId)->value('rotation');
                $resRotation = strval($resRotation);
                $schedRotation = strval($schedRotation);
                // check which residents are on same rotation as schedule rotation
                if($resRotation != "" && $schedRotation != "" && strpos($schedRotation, $resRotation) !== false){
                    array_push($onRotation, $competingOption);
                }else{
                    array_push($notOnRotation, $competingOption['resident']);
                }
            }
            // Residents on same rotation as schedule get priority
            // Case 1: if only 1 resident on same rotation
            if(count($onRotation) == 1){
                // residents not on rotation are added to remainder array
                $remainder = array_merge($remainder, $notOnRotation);
                // assign resident to schedule
                $anestPref = Option::where('date', $date)
                                ->where('schedule', $wantedSchedule)
                                ->where('option', $prefNum)
                                ->where('isValid', "1")
                                ->where('resident', $onRotation[0]['resident'])
                                ->value('anesthesiologist_id');
                $anestsAssigned = self::handleAssignment($wantedSchedule, $onRotation[0]['resident'], $date, $ticketsToAdd, $anestPref, $anestsAssigned);
            }
            // Case 2: multiple residents on same rotation 
            else if(count($onRotation) > 1){
                // find which resident on rotation has the most tickets
                foreach($onRotation as $onRotationOption){
                    // add losing residents to remainder array
                    $residentTickets = Probability::where('resident', $onRotationOption['resident'])->value('total');
                    if($maxTickets < $residentTickets){
                        $maxTickets = $residentTickets;
                        if (!is_null($winnerResident)){
                            array_push($remainder, $winnerResident);
                        }
                        $winnerResident = $onRotationOption['resident'];
                    }else{
                        array_push($remainder, $onRotationOption['resident']);
                    }
                }
                // residents not on rotation are added to remainder array
                $remainder = array_merge($remainder, $notOnRotation);
                // assign winning resident to schedule
                $anestPref = Option::where('date', $date)
                                ->where('schedule', $wantedSchedule)
                                ->where('option', $prefNum)
                                ->where('isValid', "1")
                                ->where('resident', $winnerResident)
                                ->value('anesthesiologist_id');
                $anestsAssigned = self::handleAssignment($wantedSchedule, $winnerResident, $date, $ticketsToAdd, $anestPref, $anestsAssigned);
            }
            // Case 3: No one is on same rotation as schedule
            else {
                // find which resident has the most tickets
                foreach($competingOptions as $competingOption){
                    // add losing residents to remainder array
                    $residentTickets = Probability::where('resident', $competingOption['resident'])->value('total');
                    if($maxTickets < $residentTickets){
                        $maxTickets = $residentTickets;
                        if (!is_null($winnerResident)){
                            array_push($remainder, $winnerResident);
                        }
                        $winnerResident = $competingOption['resident'];
                    }else{
                        array_push($remainder, $competingOption['resident']);
                    }
                }
                // assign winning resident to schedule
                $anestPref = Option::where('date', $date)
                                ->where('schedule', $wantedSchedule)
                                ->where('option', $prefNum)
                                ->where('isValid', "1")
                                ->where('resident', $winnerResident)
                                ->value('anesthesiologist_id');
                $anestsAssigned = self::handleAssignment($wantedSchedule, $winnerResident, $date, $ticketsToAdd, $anestPref, $anestsAssigned);
            }
        }
        return [$remainder,$anestsAssigned];;
    }

    // handles a resident's assignment checking if anest can be assigned and updating tickets 
        // schedule = id for schedule 
        // resident = id for resident
        // date = date for schedule in YYYY-MM-DD format
        // ticketsToAdd = initial number of tickets that will be added to resident's total
        // anestPref = id for anesthesiologist
        // anestsAssigned = array of assigned anests where key=anest_id and value=# of assignments
    private static function handleAssignment($schedule, $resident, $date, $ticketsToAdd, $anestPref, $anestsAssigned) {
        // if resident has an anesthesiologist pref - check if anest can be assigned
        if(!is_null($anestPref)){
            // get room
            $room = ScheduleData::where('id', $schedule)->value('room');
            $room = strval($room);
            //if anest_id isn't a key in array -> anest hasn't been assigned yet
            if(!array_key_exists($anestPref, $anestsAssigned)){
                // CCCT and UH rooms can have anest assigned twice
                //for leasing or temp rooms, default to CCCT instead of UH 
                if($room != "" && strpos($room,"CCCT") !== false){;
                    $anestsAssigned[$anestPref] = "CCCT";
                } else if($room != "" && strpos($room,"UH") !== false) {
                     $anestsAssigned[$anestPref] = "UH";
                } else{
                    // any other room type- anests can only be assigned once
                    $anestsAssigned[$anestPref] = "Max Assignment";
                }
            // anest has already been assigned once
            }else{
                if($anestsAssigned[$anestPref] == "CCCT" && $room != "" && strpos($room,"CCCT") !== false){
                    $anestsAssigned[$anestPref] = "Max Assignment";
                }else if($anestsAssigned[$anestPref] == "UH" && $room != "" && strpos($room,"UH") !== false  && strpos($room,"CCCT") === false){
                    $anestsAssigned[$anestPref] = "Max Assignment";
                }else{
                    // anest pref is not granted, give resident an extra ticket
                    $anestPref = null;
                    $ticketsToAdd++;
                };
            }
        }
        self::addAssignment($schedule, $resident, $date, $anestPref);
        self::increaseProbability($resident, $ticketsToAdd);
        return $anestsAssigned;
    }

    // assign resident to schedule and update schedule to be taken
        // schedule = id for schedule 
        // resident = id for resident
        // date = date for schedule in YYYY-MM-DD format
        // anestId = id for anesthesiologist
    private static function addAssignment($schedule, $resident, $date, $anestId) {
        // resident's other options/preferences made are now invalid
        Option::where('resident', $resident)->where('date', $date)->update([
            'isValid' => "0"
        ]);
        // other preferences for the schedule are now invalid
        Option::where('schedule', $schedule)->update([
            'isValid' => '0'
        ]);
        $attending =ScheduleData::where('id', $schedule)->value('lead_surgeon');
        $pos = strpos($attending, '[');
        $pos_end = strpos($attending, "]");
        $attending = substr($attending, $pos + 1, $pos_end - $pos - 1);

        $option = Option::where('resident', $resident)->where('date', $date)->where('schedule', $schedule)->value('id');

        Assignment::insert([
            'date' => $date, 
            'resident' => $resident, 
            'attending' => $attending, 
            'schedule' => $schedule, 
            'option' => $option,
            'anesthesiologist_id' => $anestId
        ]);
    }

    // updates resident's total value in Probability table
        // resident = id for resident
        // ticketsToAdd = number of tickets that will be added to resident's total
    private static function increaseProbability($resident, $ticketsToAdd) {
        $total = Probability::where('resident', $resident)->value('total') + $ticketsToAdd;
        Probability::where('resident', $resident)->update([
            'total' => $total
        ]);
    }
}
