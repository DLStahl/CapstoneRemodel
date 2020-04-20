<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

use App\Option;
use App\Assignment;
use App\Resident;
use App\Probability;
use App\ScheduleData;
use Illuminate\Support\Facades\Log;

class AutoAssignment extends Model {
    public static function assignment($date) {
        Log::info("auto assignment");
        if (Option::where('date', $date)->where('isValid', "1")->doesntExist()) {
            return;
        }

        /**
         * Get the ids of people who have completed selecting option
         */
        $residents = Resident::orderBy('id', 'asc')->get();
        $candidates = array(); // Store duplicate first preference's schedule id
        $remainder = array(); //Store remaining residents id competing for second choice

        foreach ($residents as $resident) {
            //Initialize total tickets if resident is not in probability table
            if (Probability::where('resident', $resident['id'])->doesntExist()) {
                Probability::insert([
                    'resident'=>$resident['id'], 'total'=>"0", 'selected'=>"0", 'probability'=>"0"
                ]);
            }

            if (Option::where('date', $date)->where('resident', $resident['id'])->where('isValid',"1")->count() >= 1) {
                $firstPreference = Option::where('date', $date)->where('resident', $resident['id'])->where('option', "1")->where('isValid',"1")->value('schedule');
                if(is_null($firstPreference)) {
                    array_push($remainder, $resident['id']);
                    continue;
                }
                if (Option::where('date', $date)->where('schedule', $firstPreference)->where('option', "1")->where('isValid',"1")->count() == 1) {
                     //Add unique first preferences into database
                    self::addAssignment($firstPreference, $resident['id'], $date);
                    self::updateProbability($resident['id'], 0);
                }
                else if (!in_array($firstPreference, $candidates)) {
                    array_push($candidates, $firstPreference);
                }
            }
        }

        //determine who gets their preference when multiple people want the same schedule
        foreach ($candidates as $candidate) {
            $schedRotation = ScheduleData::where('id', $candidate)->value('rotation');
	    //Log::info($schedRotation);
            $competitors = Option::where('date', $date)->where('schedule', $candidate)->where('option', "1")->where('isValid',"1")->get();
            $maxScore=-1;
            $toPush = null;
            $rotationMatches = array();
            $notOnRotation = array();

            //get each resident's rotation
            foreach ($competitors as $competitor) {
              $resname = Resident::where('id', $competitor['resident'])->value('name');
              $resRotations = Rotations::where('name', $resname)->get();
              foreach ($resRotations as $resRotation) {
                if (($date >= $resRotation['Start']) && ($date <= $resRotation['End'])) {
                    $serviceId = $resRotation['Service'];
                }
              }
              $resRotation = EvaluationForms::where('id', $serviceId)->value('rotation');
	      //Log::info($resRotation);

	      $resRotation = strval($resRotation);
	      $schedRotation = strval($schedRotation);
              //check which residents are on the same rotation as the schedule
	      if($schedRotation == "") {
	      	array_push($notOnRotation, $competitor['resident']);
	      } else if ($resRotation == ""){
		array_push($notOnRotation, $competitor['resident']);
	      } else {
		if(strpos($schedRotation, $resRotation) !== false) {
                	array_push($rotationMatches, $competitor);
              	} else {
                	array_push($notOnRotation, $competitor['resident']);
              	}
	      }
              
            }

            //residents on the schedule rotation get priority over max tickets
            //if only one person is on the rotation they get it
            if(count($rotationMatches) == 1) {
              //keep those not on the rotation in the remainder array
              $remainder = array_merge($remainder, $notOnRotation);

              self::addAssignment($candidate, $rotationMatches[0]['resident'], $date);
              self::updateProbability($rotationMatches[0]['resident'], 0);
            }
            //if multiple people are on the rotation it goes to top tickets
            else if (count($rotationMatches) > 1) {
              foreach ($rotationMatches as $rotationMatch) {
                if ($maxScore < Probability::where('resident', $rotationMatch['resident'])->value('total')){
                    $maxScore = Probability::where('resident', $rotationMatch['resident'])->value('total');
                    if (!is_null($toPush)) {
                        array_push($remainder, $toPush);
                    }
                    $toPush = $rotationMatch['resident'];
                }
                else {
                    array_push($remainder, $rotationMatch['resident']);
                }
              }
              //keep those not on the rotation in the remainder array
              $remainder = array_merge($remainder, $notOnRotation);

              self::addAssignment($candidate, $toPush, $date);
              self::updateProbability($toPush, 0);
            }
            //if no one is on the rotation it is based on tickets
            else {
              //highest ticket score gets their pick
              foreach ($competitors as $competitor) {
                  if ($maxScore < Probability::where('resident', $competitor['resident'])->value('total')){
                      $maxScore = Probability::where('resident', $competitor['resident'])->value('total');
                      if (!is_null($toPush)) {
                          array_push($remainder, $toPush);
                      }
                      $toPush = $competitor['resident'];
                  }
                  else {
                      array_push($remainder, $competitor['resident']);
                  }
              }
                  self::addAssignment($candidate, $toPush, $date);
                  self::updateProbability($toPush, 0);
          }
        }


      $remainderForSecond = self::findWinner($remainder, 2, 1, $date);
	//Log:info($remainderForSecond);
      $remainderForThird = self::findWinner($remainderForSecond, 3, 2, $date);
	//Log::info($remainderForThird);
      foreach ($remainderForThird as $other){
          self::updateProbability($other,3);
      }

    }

    //$remainder = remainder array of resident ids from previous choice
    //$option = preference number
    //$tickets = tickets added to total for getting your choice
    private static function findWinner($remainder, $option, $tickets, $date) {
        $newRemainder=array(); //remaining unassigned residents
        $candidates=array(); //schedule ids

        foreach ($remainder as $other) {
            $preference = Option::where('date', $date)->where('resident', $other)->where('option', $option)->where('isValid',"1")->value('schedule');
		Log::info($preference.' preference');
            if (is_null($preference)) {
                array_push($newRemainder, $other);
                continue;
            }

            if (Option::where('date', $date)->where('schedule', $preference)->where('option', $option)->where('isValid',"1")->count() == 1) {
                //add unique preferences to database
		Log::info("unique pref");
                self::addAssignment($preference, $other, $date);
                self::updateProbability($other, $tickets);
            }
            else if (!in_array($preference, $candidates)) {
                array_push($candidates, $preference);
            }
        }

        //determine who gets there preference when multiple people want the same schedule
        foreach ($candidates as $candidate) {
            $schedRotation = ScheduleData::where('id', $candidate)->value('rotation');
	    Log::info($schedRotation);
            $competitors = Option::where('date', $date)->where('schedule', $candidate)->where('option', $option)->where('isValid',"1")->get();
            $maxScore=-1;
            $toPush = null;
            $rotationMatches = array();
            $notOnRotation = array();

            //get each resident's rotation
            foreach ($competitors as $competitor) {
              $resname = Resident::where('id', $competitor['resident'])->value('name');
              $resRotations = Rotations::where('name', $resname)->get();
              foreach ($resRotations as $resRotation) {
                if (($date >= $resRotation['Start']) && ($date <= $resRotation['End'])) {
                    $serviceId = $resRotation['Service'];
                }
              }
              $resRotation = EvaluationForms::where('id', $serviceId)->value('rotation');
	      Log::info($resRotation);

	      //check which residents are on the same rotation as the schedule
	      $resRotation = strval($resRotation);
	      $schedRotation = strval($schedRotation);
	      if($schedRotation == "") {
	      	array_push($notOnRotation, $competitor['resident']);
	      } else if ($resRotation == ""){
		array_push($notOnRotation, $competitor['resident']);
	      } else {
		if(strpos($schedRotation, $resRotation) !== false) {
                	array_push($rotationMatches, $competitor);
              	} else {
                	array_push($notOnRotation, $competitor['resident']);
              	}
	      }

            }

            //residents on the schedule rotation get priority over max tickets
            //if only one person is on the rotation they get it
            if(count($rotationMatches) == 1) {
              //keep those not on the rotation in the remainder array
              $newRemainder = array_merge($newRemainder, $notOnRotation);

              self::addAssignment($candidate, $rotationMatches[0]['resident'], $date);
              self::updateProbability($rotationMatches[0]['resident'], $tickets);
            }
            //if multiple people are on the rotation it goes to top tickets
            else if (count($rotationMatches) > 1) {
              foreach ($rotationMatches as $rotationMatch) {
                if ($maxScore < Probability::where('resident', $rotationMatch['resident'])->value('total')){
                    $maxScore = Probability::where('resident', $rotationMatch['resident'])->value('total');
                    if (!is_null($toPush)) {
                        array_push($newRemainder, $toPush);
                    }
                    $toPush = $rotationMatch['resident'];
                }
                else {
                    array_push($newRemainder, $rotationMatch['resident']);
                }
              }
              //keep those not on the rotation in the remainder array
              $newRemainder = array_merge($newRemainder, $notOnRotation);

              self::addAssignment($candidate, $toPush, $date);
              self::updateProbability($toPush, $tickets);
            }
            //if no one is on the rotation it is based on tickets
            else {
              //highest ticket score gets their pick
              foreach ($competitors as $competitor) {
                  if ($maxScore < Probability::where('resident', $competitor['resident'])->value('total')){
                      $maxScore = Probability::where('resident', $competitor['resident'])->value('total');
                      if (!is_null($toPush)) {
                          array_push($newRemainder, $toPush);
                      }
                      $toPush = $competitor['resident'];
                  }
                  else {
                      array_push($newRemainder, $competitor['resident']);
                  }
              }
                  self::addAssignment($candidate, $toPush, $date);
                  self::updateProbability($toPush, $tickets);
          }
        }
        return $newRemainder;
    }

    private static function addAssignment($schedule, $resident, $date)
    {
//        $isValid = $selected * 1.0 / $total;
        Option::where('resident', $resident)->where('date', $date)->update([
            'isValid'=>"0"
        ]);
        Option::where('schedule', $schedule)->update([
            'isValid'=>'0'
        ]);
        $attending = ScheduleData::where('id', $schedule)->value('lead_surgeon');
        $pos = strpos($attending, '[');
        $pos_end = strpos($attending, "]");
        $attending = substr($attending, $pos+1, $pos_end-$pos-1);

        $option = Option::where('resident', $resident)->where('date', $date)->where('schedule', $schedule)->value('id');

        Assignment::insert([
            'date'=>$date, 'resident'=>$resident, 'attending'=>$attending, 'schedule'=>$schedule, 'option'=>$option
        ]);
    }

    private static function updateProbability($resident, $kind)
    {

        $total=Probability::where('resident',$resident)->value('total')+$kind;
        Probability::where('resident', $resident)->update([
            'total'=>$total
        ]);
    }
}
