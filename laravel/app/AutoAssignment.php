<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

use App\Option;
use App\Assignment;
use App\Resident;
use App\Probability;
use App\ScheduleData;
use Illuminate\Support\Facades\Log;

class AutoAssignment extends Model
{
    public static function assignment($date)
    {
        Log::info("auto assignment");
        if (Option::where('date', $date)->where('isValid', "1")->doesntExist()) {
            return;
        }

        $residents = Resident::orderBy('id', 'asc')->get();
        self::initalizeResidentProbabilities($residents);
        
        //get array of resident ids
        $residents = Resident::pluck('id')->toArray();
        //handle 1st preference assignments with ticketsAdded = 0
        $remainderFromFirstPref = self::assignResidentsForPref($residents, 1, 0, $date);
        //handle 2nd preference assignments with ticketsAdded = 1
        $remainderFromSecondPref = self::assignResidentsForPref($remainderFromFirstPref, 2, 1, $date);
        //handle 3rd preference assignments with ticketsAdded = 2
        $remainderFromThirdPref = self::assignResidentsForPref($remainderFromSecondPref, 3, 2, $date);
        //handle residents that are not assigned with ticketsAdded =3
        foreach($remainderFromThirdPref as $unassignedResident){
            if(Option::where('date', $date)
                ->where('resident', $unassignedResident)
                ->count() > 0){
                    self::updateProbability($unassignedResident, 3);
            }
        }
    }

    // Initialize total tickets if resident is not in Probability table
        // residents = array of resident objects
    private static function initalizeResidentProbabilities($residents){
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

    // Assigns residents for given preference number and returns array of residents not assigned 
        // residents = array of resident ids
        // prefNum = Preference Number corresponding to option in option table
        // ticketsToAdd = number of tickets to give resident after assignment
        // date = date of schedules that are being assigned
    private static function assignResidentsForPref($residents, $prefNum, $ticketsToAdd, $date){
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
                    self::addAssignment($schedulePref, $resident, $date);
                    self::updateProbability($resident, $ticketsToAdd); 
            // add schedule pref if it is not already in wantedSchedules array  
            } else if(!in_array($schedulePref, $wantedSchedules)){
                array_push($wantedSchedules, $schedulePref);
            }
        } 
        //determine who gets the schedule when multiple residents want the same one
        foreach($wantedSchedules as $wantedSchedule){
            $schedRotation = ScheduleData::where('id', $wantedSchedule)
                            ->value('rotation');
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
                $resName = Resident::where('id', $competingOption['resident'])
                            ->value('name');
                $resRotations = Rotations::where('name', $resName)
                                ->get();
                $serviceId ="";
                foreach($resRotations as $resRotation){
                    if($date >= $resRotation['Start'] && $date <= $resRotation['End']){
                        $serviceId = $resRotation['Service'];
                    }
                }
                $resRotation = EvaluationForms::where('id', $serviceId)
                                ->value('rotation');
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
                self::addAssignment($wantedSchedule, $onRotation[0]['resident'], $date);
                self::updateProbability($onRotation[0]['resident'], $ticketsToAdd);
            }
            // Case 2: multiple residents on same rotation 
            else if(count($onRotation) > 1){
                // find which resident on rotation has the most tickets
                foreach($onRotation as $onRotationOption){
                    // add losing residents to remainder array
                    if($maxTickets < Probability::where('resident', $onRotationOption['resident'])->value('total')){
                        $maxTickets = Probability::where('resident', $onRotationOption['resident'])->value('total');
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
                self::addAssignment($wantedSchedule, $winnerResident, $date);
                self::updateProbability($winnerResident, $ticketsToAdd);
            }
            // Case 3: No one is on same rotation as schedule
            else {
                // find which resident has the most tickets
                foreach($competingOptions as $competingOption){
                    // add losing residents to remainder array
                    if($maxTickets < Probability::where('resident', $competingOption['resident'])->value('total')){
                        $maxTickets = Probability::where('resident', $competingOption['resident'])->value('total');
                        if (!is_null($winnerResident)){
                            array_push($remainder, $winnerResident);
                        }
                        $winnerResident = $competingOption['resident'];
                    }else{
                        array_push($remainder, $competingOption['resident']);
                    }
                }
                // assign winning resident to schedule
                self::addAssignment($wantedSchedule, $winnerResident, $date);
                self::updateProbability($winnerResident, $ticketsToAdd);
            }
        }
        return $remainder;
    }

    // assign resident to schedule and update schedule to be taken
        // schedule = id for schedule 
        // resident = id for resident
        // date = date for schedule in YYYY-MM-DD format
    private static function addAssignment($schedule, $resident, $date)
    {
        // resident's other options/preferences made are now invalid
        Option::where('resident', $resident)->where('date', $date)->update([
            'isValid' => "0"
        ]);
        // other preferences for the schedule are now invalid
        Option::where('schedule', $schedule)->update([
            'isValid' => '0'
        ]);
        $attending = ScheduleData::where('id', $schedule)->value('lead_surgeon');
        $pos = strpos($attending, '[');
        $pos_end = strpos($attending, "]");
        $attending = substr($attending, $pos + 1, $pos_end - $pos - 1);

        $option = Option::where('resident', $resident)->where('date', $date)->where('schedule', $schedule)->value('id');

        Assignment::insert([
            'date' => $date, 'resident' => $resident, 'attending' => $attending, 'schedule' => $schedule, 'option' => $option
        ]);
    }

    // updates resident's total value in Probability table
        // resident = id for resident
        // ticketsToAdd = number of tickets that will be added to resident's total
    private static function updateProbability($resident, $ticketsToAdd)
    {
        $total = Probability::where('resident', $resident)->value('total') + $ticketsToAdd;
        Probability::where('resident', $resident)->update([
            'total' => $total
        ]);
    }
}
