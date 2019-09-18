<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

use App\Option;
use App\Assignment;
use App\Resident;
use App\Probability;
use App\ScheduleData;

class AutoAssignment extends Model
{
    public static function assignment($date)
    {
        if (Option::where('date', $date)->doesntExist())
        {
            return;
        }

        /**
         * Get the ids of people who have completed selecting option
         */
        $residents = Resident::orderBy('id', 'asc')->get();
        $candidates = array(); // Store duplicate first preference's schedule id
        $remainder = array(); //Store remaining residents id competing for second choice

        foreach ($residents as $resident)
        {

            /**
             * Initialize total tickets if resident is not in probability table
             */
            if (Probability::where('resident', $resident['id'])->doesntExist())
            {
                Probability::insert([
                    'resident'=>$resident['id'], 'total'=>"0", 'selected'=>"0", 'probability'=>"0"
                ]);
            }



            if (Option::where('date', $date)->where('resident', $resident['id'])->count() >= 1)
            {

                $firstPreference = Option::where('date', $date)->where('resident', $resident['id'])->where('option', "1")->value('schedule');
                if(is_null($firstPreference))
                {
                    array_push($remainder, $resident['id']);
                    continue;
                }
                if (Option::where('date', $date)->where('schedule', $firstPreference)->where('option', "1")->count() == 1)
                {
                    /**
                     * Add unique first preferences into database
                     */
                    self::addAssignment($firstPreference, $resident['id'], $date);
                    self::updateProbability($resident['id'], 0);
                }
                else if (!in_array($firstPreference, $candidates)) {
                    array_push($candidates, $firstPreference);
                }
            }
        }

        /**
         * Assign OR based on the original probability
         */
//        $remainder = array();
        foreach ($candidates as $candidate)
        {
            $competitors = Option::where('date', $date)->where('schedule', $candidate)->where('option', "1")->get();
            $maxScore=-1;
            $toPush = null;
            foreach ($competitors as $competitor)
            {
                if ($maxScore < Probability::where('resident', $competitor['resident'])->value('total')){
                    $maxScore = Probability::where('resident', $competitor['resident'])->value('total');
                    if (!is_null($toPush))
                    {
                        array_push($remainder, $toPush);
                    }
                    $toPush = $competitor['resident'];
                }
                else
                {
                    array_push($remainder, $competitor['resident']);
                }
            }
            self::addAssignment($candidate, $toPush, $date);
            self::updateProbability($toPush, 0);
        }

        /**
         * Assign OR for second preferences
         */
        $remainderForThird=array();
        $candidates=array();
        foreach ($remainder as $other)
        {
            $secondPreference = Option::where('date', $date)->where('resident', $other)->where('option', "2")->where('isValid',"1")->value('schedule');
            if (is_null($secondPreference))
            {
                array_push($remainderForThird,$other);
                continue;
            }

            if (Option::where('date', $date)->where('schedule', $secondPreference)->where('option', "2")->where('isValid',"1")->count() == 1)
            {
                /**
                 * Add unique second preferences into database
                 */
                self::addAssignment($secondPreference, $other, $date);
                self::updateProbability($other, 1);
            }
            else if (!in_array($secondPreference, $candidates)) {
                array_push($candidates, $secondPreference);
            }
        }

        /**
         * Assign OR based on the original probability
         */

        foreach ($candidates as $candidate)
        {
            $competitors = Option::where('date', $date)->where('schedule', $candidate)->where('option', "2")->where('isValid',"1")->get();
            $maxScore=-1;
            $toPush = null;
            foreach ($competitors as $competitor)
            {
                if ($maxScore < Probability::where('resident', $competitor['resident'])->value('total')){
                    $maxScore = Probability::where('resident', $competitor['resident'])->value('total');
                    if (!is_null($toPush))
                    {
                        array_push($remainderForThird, $toPush);
                    }
                    $toPush = $competitor['resident'];
                }
                else
                {
                    array_push($remainderForThird, $competitor['resident']);
                }
            }
                self::addAssignment($candidate, $toPush, $date);
                self::updateProbability($toPush, 1);

        }


//         /**
//          * Assign OR for third preferences
//          */
        $noMatching=array();
        $candidates=array();
        foreach ($remainderForThird as $other)
        {
            $thirdPreference = Option::where('date', $date)->where('resident', $other)->where('option', "3")->where('isValid',"1")->value('schedule');

            if (is_null($thirdPreference))
            {
                array_push($noMatching,$other);
                continue;
            }


            if (Option::where('date', $date)->where('schedule', $thirdPreference)->where('option', "3")->where('isValid',"1")->count() == 1)
            {
                /**
                 * Add unique third preferences into database
                 */
                self::addAssignment($thirdPreference, $other, $date);
                self::updateProbability($other, 2);
            }
            else if (!in_array($thirdPreference, $candidates)) {
                array_push($candidates, $thirdPreference);
            }
        }

        /**
         * Assign OR based on the original probability
         */
        foreach ($candidates as $candidate)
        {
            $competitors = Option::where('date', $date)->where('schedule', $candidate)->where('option', "3")->where('isValid',"1")->get();
            $maxScore=-1;
//            $minProb = 1;
            $toPush = null;
            foreach ($competitors as $competitor)
            {
                if ($maxScore < Probability::where('resident', $competitor['resident'])->value('total')){
                    $maxScore = Probability::where('resident', $competitor['resident'])->value('total');
                    if (!is_null($toPush))
                    {
                        array_push($noMatching, $toPush);
                    }
                    $toPush = $competitor['resident'];
                }
                else
                {
                    array_push($noMatching, $competitor['resident']);
                }
            }
//
                self::addAssignment($candidate, $toPush, $date);
                self::updateProbability($toPush, 2);
        }

        foreach ($noMatching as $other)
        {
            self::updateProbability($other,3);

        }



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

        $option = Option::where('resident', $resident)->where('date', $date)->where('schedule', $schedule);
        $preference = $option->value('option');
        $milestones = $option->value('milestones');
        $objectives = $option->value('objectives');

        Assignment::insert([
            'date'=>$date, 'resident'=>$resident, 'attending'=>$attending, 'schedule'=>$schedule, 'preference'=>$preference, 'milestones'=>$milestones, 'objectives'=>$objectives
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
