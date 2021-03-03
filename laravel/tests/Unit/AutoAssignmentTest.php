<?php

namespace Tests\Unit;

use App\AutoAssignment;
use Tests\TestCase;
use App\Option;
use App\Assignment;
use App\Probability;

class AutoAssignmentTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    // Tests for ticketing given types assignments 
    
    public function testPreferenceTicketingWithAnestsGranted() {
        $date = "2021-03-03";
        // create options for residents
        $ResidentAOption1CCCT = Option::insertGetId([
            "date" => $date,
            "resident" => 113,
            "schedule" => 143907,
            "attending" => 349746,
            "option" => 1,
            "isValid" => 1,
            "anesthesiologist_id" => 1
        ]);
        $ResidentBOption1CCCT = Option::insertGetId([
            "date" => $date,
            "resident" => 270,
            "schedule" => 143907,
            "attending" => 349746,
            "option" => 1,
            "isValid" => 1,
            "anesthesiologist_id" => 1
        ]);
        $ResidentBOption2CCCT = Option::insertGetId([
            "date" => $date,
            "resident" => 270,
            "schedule" => 143910,
            "attending" => 349746,
            "option" => 2,
            "isValid" => 1,
            "anesthesiologist_id" => 2
        ]);
        $ResidentCOption1CCCCT = Option::insertGetId([
            "date" => $date,
            "resident" => 300,
            "schedule" => 143907,
            "attending" => 349746,
            "option" => 1,
            "isValid" => 1,
            "anesthesiologist_id" => 1
        ]);
        $ResidentCOption2CCCT = Option::insertGetId([
            "date" => $date,
            "resident" => 300,
            "schedule" => 143910,
            "attending" => 349746,
            "option" => 2,
            "isValid" => 1,
            "anesthesiologist_id" => 2
        ]);
        $ResidentCOption3CCCT = Option::insertGetId([
            "date" => $date,
            "resident" => 300,
            "schedule" => 143914,
            "attending" => 349746,
            "option" => 3,
            "isValid" => 1,
            "anesthesiologist_id" => 3
        ]);
        // update total for residents needed for correct assignment
        Probability::where('resident', 113)->update(['total' => 2]);
        Probability::where('resident', 270)->update(['total' => 1]);
        Probability::where('resident', 300)->update(['total' => 0]);
        // assignment method call
        $autoAssignment = new AutoAssignment();
        $autoAssignment->assignment($date);
        // bools and values for assignments existing and current total for residents
        $foundAssignmentForA = Assignment::where('date', $date)
                            ->where('resident', 113)
                            ->where('schedule', 143907)
                            ->where('anesthesiologist_id', 1)
                            ->exists();
        $ProbTotalResidentA = Probability::where('resident', 113)->value('total');
        $foundAssignmentForB =Assignment::where('date', $date)
                            ->where('resident', 270)
                            ->where('schedule', 143910)
                            ->where('anesthesiologist_id', 2)
                            ->exists();
        $ProbTotalResidentB = Probability::where('resident', 270)->value('total');
        $foundAssignmentForC =Assignment::where('date', $date)
                            ->where('resident',300)
                            ->where('schedule', 143914)
                            ->where('anesthesiologist_id', 3)
                            ->exists();
        $ProbTotalResidentC = Probability::where('resident', 300)->value('total');
        // assertions for correct assignments                    
        $this->assertTrue($foundAssignmentForA);
        $this->assertTrue($foundAssignmentForB);
        $this->assertTrue($foundAssignmentForC);
        // assertions for correct total in probability table after assignment
        $this->assertEquals($ProbTotalResidentA, 2);
        $this->assertEquals($ProbTotalResidentB, 3);
        $this->assertEquals($ProbTotalResidentC, 4);
        // delete dummy data for option table
        Option::find($ResidentAOption1CCCT)->delete();
        Option::find($ResidentBOption1CCCT)->delete();
        Option::find($ResidentBOption2CCCT)->delete();
        Option::find($ResidentCOption1CCCCT)->delete();
        Option::find($ResidentCOption2CCCT)->delete();
        Option::find($ResidentCOption3CCCT)->delete();
        // delete dummy assignments
        Assignment::where('date', $date)
            ->where('resident', 113)
            ->where('schedule', 143907)
            ->where('anesthesiologist_id', 1)
            ->delete();
        Assignment::where('date', $date)
            ->where('resident', 270)
            ->where('schedule', 143910)
            ->where('anesthesiologist_id', 2)
            ->delete();
        Assignment::where('date', $date)
            ->where('resident', 300)
            ->where('schedule', 143914)
            ->where('anesthesiologist_id', 3)
            ->delete();
    }

    public function testPreferenceTicketingWithAnestsNotGranted() {
        $date = "2021-03-03";
        // create options for residents 
        $ResidentAOption1EP = Option::insertGetId([
            "date" => $date,
            "resident" => 113,
            "schedule" => 143908,
            "attending" => 349746,
            "option" => 1,
            "isValid" => 1,
            "anesthesiologist_id" => 1
        ]);
        $ResidentBOption1CCCT = Option::insertGetId([
            "date" => $date,
            "resident" => 270,
            "schedule" => 143907,
            "attending" => 349746,
            "option" => 1,
            "isValid" => 1,
            "anesthesiologist_id" => 1
        ]);
        $ResidentCOption1CCCT = Option::insertGetId([
            "date" => $date,
            "resident" => 300,
            "schedule" => 143907,
            "attending" => 349746,
            "option" => 1,
            "isValid" => 1,
            "anesthesiologist_id" => 1
        ]);
        $ResidentCOption2CCCT = Option::insertGetId([
            "date" => $date,
            "resident" => 300,
            "schedule" => 143910,
            "attending" => 349746,
            "option" => 2,
            "isValid" => 1,
            "anesthesiologist_id" => 1
        ]);
        $ResidentDOption1CCCCT = Option::insertGetId([
            "date" => $date,
            "resident" => 881,
            "schedule" => 143907,
            "attending" => 349746,
            "option" => 1,
            "isValid" => 1,
            "anesthesiologist_id" => 1
        ]);
        $ResidentDOption2CCCT = Option::insertGetId([
            "date" => $date,
            "resident" => 881,
            "schedule" => 143910,
            "attending" => 349746,
            "option" => 2,
            "isValid" => 1,
            "anesthesiologist_id" => 1
        ]);
        $ResidentDOption3CCCT = Option::insertGetId([
            "date" => $date,
            "resident" => 881,
            "schedule" => 143914,
            "attending" => 349746,
            "option" => 3,
            "isValid" => 1,
            "anesthesiologist_id" => 1
        ]);
        // update total for residents needed for correct assignment
        Probability::where('resident', 113)->update(['total' => 0]);
        Probability::where('resident', 270)->update(['total' => 2]);
        Probability::where('resident', 300)->update(['total' => 1]);
        Probability::where('resident', 881)->update(['total' => 0]);
        // assignment method call
        $autoAssignment = new AutoAssignment();
        $autoAssignment->assignment($date);
        // bools and values for assignments existing and current total for residents
        $foundAssignmentForA = Assignment::where('date', $date)
                            ->where('resident', 113)
                            ->where('schedule', 143908)
                            ->where('anesthesiologist_id', 1)
                            ->exists();
        $ProbTotalResidentA = Probability::where('resident', 113)->value('total');
        $foundAssignmentForB =Assignment::where('date', $date)
                            ->where('resident', 270)
                            ->where('schedule', 143907)
                            ->where('anesthesiologist_id', NULL)
                            ->exists();
        $ProbTotalResidentB = Probability::where('resident', 270)->value('total');
        $foundAssignmentForC =Assignment::where('date', $date)
                            ->where('resident', 300)
                            ->where('schedule', 143910)
                            ->where('anesthesiologist_id', NULL)
                            ->exists();
        $ProbTotalResidentC = Probability::where('resident', 300)->value('total');
        $foundAssignmentForD =Assignment::where('date', $date)
                            ->where('resident', 881)
                            ->where('schedule', 143914)
                            ->where('anesthesiologist_id', NULL)
                            ->exists();
        $ProbTotalResidentD = Probability::where('resident', 881)->value('total');
        // assertions for correct assignments                    
        $this->assertTrue($foundAssignmentForA);
        $this->assertTrue($foundAssignmentForB);
        $this->assertTrue($foundAssignmentForC);
        $this->assertTrue($foundAssignmentForD);
        // assertions for correct total in probability table after assignment
        $this->assertEquals($ProbTotalResidentA, 0);
        $this->assertEquals($ProbTotalResidentB, 3);
        $this->assertEquals($ProbTotalResidentC, 4);
        $this->assertEquals($ProbTotalResidentD, 5);
        // delete dummy data for option table
        Option::find($ResidentAOption1EP)->delete();
        Option::find($ResidentBOption1CCCT)->delete();
        Option::find($ResidentCOption1CCCT)->delete();
        Option::find($ResidentCOption2CCCT)->delete();
        Option::find($ResidentDOption1CCCCT)->delete();
        Option::find($ResidentDOption2CCCT)->delete();
        Option::find($ResidentDOption3CCCT)->delete();
        // delete dummy assignments
        Assignment::where('date', $date)
            ->where('resident', 113)
            ->where('schedule', 143908)
            ->where('anesthesiologist_id', 1)
            ->delete();
        Assignment::where('date', $date)
            ->where('resident', 270)
            ->where('schedule', 143907)
            ->where('anesthesiologist_id', NULL)
            ->delete();
        Assignment::where('date', $date)
            ->where('resident', 300)
            ->where('schedule', 143910)
            ->where('anesthesiologist_id', NULL)
            ->delete();
        Assignment::where('date', $date)
            ->where('resident', 881)
            ->where('schedule', 143914)
            ->where('anesthesiologist_id', NULL)
            ->delete();
    }
    
    public function testPreferenceTicketingNoAnestsPreferences() {
        $date = "2021-03-03";
        // create options for residents 
        $ResidentAOption1CCCT = Option::insertGetId([
            "date" => $date,
            "resident" => 113,
            "schedule" => 143907,
            "attending" => 349746,
            "option" => 1,
            "isValid" => 1,
            "anesthesiologist_id" => NULL
        ]);
        $ResidentBOption1CCCT = Option::insertGetId([
            "date" => $date,
            "resident" => 270,
            "schedule" => 143907,
            "attending" => 349746,
            "option" => 1,
            "isValid" => 1,
            "anesthesiologist_id" => NULL
        ]);
        $ResidentBOption2CCCT = Option::insertGetId([
            "date" => $date,
            "resident" => 270,
            "schedule" => 143910,
            "attending" => 349746,
            "option" => 2,
            "isValid" => 1,
            "anesthesiologist_id" => NULL
        ]);
        $ResidentCOption1CCCCT = Option::insertGetId([
            "date" => $date,
            "resident" => 300,
            "schedule" => 143907,
            "attending" => 349746,
            "option" => 1,
            "isValid" => 1,
            "anesthesiologist_id" => NULL
        ]);
        $ResidentCOption2CCCT = Option::insertGetId([
            "date" => $date,
            "resident" => 300,
            "schedule" => 143910,
            "attending" => 349746,
            "option" => 2,
            "isValid" => 1,
            "anesthesiologist_id" => NULL
        ]);
        $ResidentCOption3CCCT = Option::insertGetId([
            "date" => $date,
            "resident" => 300,
            "schedule" => 143914,
            "attending" => 349746,
            "option" => 3,
            "isValid" => 1,
            "anesthesiologist_id" => NULL
        ]);
        // update total for residents needed for correct assignment
        Probability::where('resident', 113)->update(['total' => 2]);
        Probability::where('resident', 270)->update(['total' => 1]);
        Probability::where('resident', 300)->update(['total' => 0]);
        // assignment method call
        $autoAssignment = new AutoAssignment();
        $autoAssignment->assignment($date);
        // bools and values for assignments existing and current total for residents
        $foundAssignmentForA = Assignment::where('date', $date)
                            ->where('resident', 113)
                            ->where('schedule', 143907)
                            ->where('anesthesiologist_id', NULL)
                            ->exists();
        $ProbTotalResidentA = Probability::where('resident', 113)->value('total');
        $foundAssignmentForB =Assignment::where('date', $date)
                            ->where('resident', 270)
                            ->where('schedule', 143910)
                            ->where('anesthesiologist_id', NULL)
                            ->exists();
        $ProbTotalResidentB = Probability::where('resident', 270)->value('total');
        $foundAssignmentForC =Assignment::where('date', $date)
                            ->where('resident',300)
                            ->where('schedule', 143914)
                            ->where('anesthesiologist_id', NULL)
                            ->exists();
        $ProbTotalResidentC = Probability::where('resident', 300)->value('total');
        // assertions for correct assignments                    
        $this->assertTrue($foundAssignmentForA);
        $this->assertTrue($foundAssignmentForB);
        $this->assertTrue($foundAssignmentForC);
        // assertions for correct total in probability table after assignment
        $this->assertEquals($ProbTotalResidentA, 2);
        $this->assertEquals($ProbTotalResidentB, 3);
        $this->assertEquals($ProbTotalResidentC, 4);
        // delete dummy data for option table
        Option::find($ResidentAOption1CCCT)->delete();
        Option::find($ResidentBOption1CCCT)->delete();
        Option::find($ResidentBOption2CCCT)->delete();
        Option::find($ResidentCOption1CCCCT)->delete();
        Option::find($ResidentCOption2CCCT)->delete();
        Option::find($ResidentCOption3CCCT)->delete();
        // delete dummy assignments
        Assignment::where('date', $date)
            ->where('resident', 113)
            ->where('schedule', 143907)
            ->where('anesthesiologist_id', NULL)
            ->delete();
        Assignment::where('date', $date)
            ->where('resident', 270)
            ->where('schedule', 143910)
            ->where('anesthesiologist_id', NULL)
            ->delete();
        Assignment::where('date', $date)
            ->where('resident', 300)
            ->where('schedule', 143914)
            ->where('anesthesiologist_id', NULL)
            ->delete();
    }

    public function testPreferenceTicketingForUnassignedResidents() {
        $date = "2021-03-03"; 
        // create options for residents
        $ResidentAOption1CCCT = Option::insertGetId([
            "date" => $date,
            "resident" => 113,
            "schedule" => 143910,
            "attending" => 349746,
            "option" => 1,
            "isValid" => 1,
            "anesthesiologist_id" => 1
        ]);
        $ResidentBOption1CCCT = Option::insertGetId([
            "date" => $date,
            "resident" => 270,
            "schedule" => 143910,
            "attending" => 349746,
            "option" => 1,
            "isValid" => 1,
            "anesthesiologist_id" => 1
        ]);
        $ResidentCOption1CCCT = Option::insertGetId([
            "date" => $date,
            "resident" => 300,
            "schedule" => 143910,
            "attending" => 349746,
            "option" => 1,
            "isValid" => 1,
            "anesthesiologist_id" => 1
        ]);
        $ResidentCOption2CCCT = Option::insertGetId([
            "date" => $date,
            "resident" => 300,
            "schedule" => 143910,
            "attending" => 349746,
            "option" => 2,
            "isValid" => 1,
            "anesthesiologist_id" => 1
        ]);
        $ResidentDOption1CCCCT = Option::insertGetId([
            "date" => $date,
            "resident" => 881,
            "schedule" => 143910,
            "attending" => 349746,
            "option" => 1,
            "isValid" => 1,
            "anesthesiologist_id" => 1
        ]);
        $ResidentDOption2CCCT = Option::insertGetId([
            "date" => $date,
            "resident" => 881,
            "schedule" => 143910,
            "attending" => 349746,
            "option" => 2,
            "isValid" => 1,
            "anesthesiologist_id" => 1
        ]);
        $ResidentDOption3CCCT = Option::insertGetId([
            "date" => $date,
            "resident" => 881,
            "schedule" => 143910,
            "attending" => 349746,
            "option" => 3,
            "isValid" => 1,
            "anesthesiologist_id" => 1
        ]);
        // update total for residents needed for correct assignment
        Probability::where('resident', 113)->update(['total' => 1]);
        Probability::where('resident', 270)->update(['total' => 0]);
        Probability::where('resident', 300)->update(['total' => 0]);
        Probability::where('resident', 881)->update(['total' => 0]);
        // assignment method call
        $autoAssignment = new AutoAssignment();
        $autoAssignment->assignment($date);
        // bools and values for assignments existing and current total for residents
        $foundAssignmentForA = Assignment::where('date', $date)
                            ->where('resident', 113)
                            ->where('schedule', 143910)
                            ->where('anesthesiologist_id', 1)
                            ->exists();
        $ProbTotalResidentA = Probability::where('resident', 113)->value('total');
        $ProbTotalResidentB = Probability::where('resident', 270)->value('total');
        $ProbTotalResidentC = Probability::where('resident', 300)->value('total');
        $ProbTotalResidentD = Probability::where('resident', 881)->value('total');
        // assertions for correct assignments                    
        $this->assertTrue($foundAssignmentForA);
        // assertions for correct total in probability table after assignment
        $this->assertEquals($ProbTotalResidentA, 1);
        $this->assertEquals($ProbTotalResidentB, 6);
        $this->assertEquals($ProbTotalResidentC, 6);
        $this->assertEquals($ProbTotalResidentD, 6);
        // delete dummy data for option table
        Option::find($ResidentAOption1CCCT)->delete();
        Option::find($ResidentBOption1CCCT)->delete();
        Option::find($ResidentCOption1CCCT)->delete();
        Option::find($ResidentCOption2CCCT)->delete();
        Option::find($ResidentDOption1CCCCT)->delete();
        Option::find($ResidentDOption2CCCT)->delete();
        Option::find($ResidentDOption3CCCT)->delete();
        // delete dummy assignments
        Assignment::where('date', $date)
            ->where('resident', 113)
            ->where('schedule', 143910)
            ->where('anesthesiologist_id', 1)
            ->delete();  
    }

    // Tests for Anesthesiologist Preference Assignment

    public function testAnestAssignedOnce() {
        $date = "2021-03-03"; 
        // create options for residents
        $ResidentAOption1EP = Option::insertGetId([
            "date" => $date,
            "resident" => 113,
            "schedule" => 143908,
            "attending" => 349746,
            "option" => 1,
            "isValid" => 1,
            "anesthesiologist_id" => 1
        ]);
        $ResidentBOption1CCCT = Option::insertGetId([
            "date" => $date,
            "resident" => 270,
            "schedule" => 143910,
            "attending" => 349746,
            "option" => 1,
            "isValid" => 1,
            "anesthesiologist_id" => 1
        ]);
        // update total for residents needed for correct assignment
        Probability::where('resident', 113)->update(['total' => 0]);
        Probability::where('resident', 270)->update(['total' => 0]);
        // assignment method call
        $autoAssignment = new AutoAssignment();
        $autoAssignment->assignment($date);
        // bools and values for assignments existing and current total for residents
        $foundAssignmentForA = Assignment::where('date', $date)
                            ->where('resident', 113)
                            ->where('schedule', 143908)
                            ->where('anesthesiologist_id', 1)
                            ->exists();
        $ProbTotalResidentA = Probability::where('resident', 113)->value('total');
        $foundAssignmentForB =Assignment::where('date', $date)
                            ->where('resident', 270)
                            ->where('schedule', 143910)
                            ->where('anesthesiologist_id', NULL)
                            ->exists();
        $ProbTotalResidentB = Probability::where('resident', 270)->value('total');
        // assertions for correct assignments                    
        $this->assertTrue($foundAssignmentForA);
        $this->assertTrue($foundAssignmentForB);
        // assertions for correct total in probability table after assignment
        $this->assertEquals($ProbTotalResidentA, 0);
        $this->assertEquals($ProbTotalResidentB, 1);
        // delete dummy data for option table
        Option::find($ResidentAOption1EP)->delete();
        Option::find($ResidentBOption1CCCT)->delete();
        // delete dummy assignments
        Assignment::where('date', $date)
            ->where('resident', 113)
            ->where('schedule', 143908)
            ->where('anesthesiologist_id', 1)
            ->delete();
        Assignment::where('date', $date)
            ->where('resident', 270)
            ->where('schedule', 143910)
            ->where('anesthesiologist_id', NULL)
            ->delete();   
    }

    public function testAnestDoubleAssignedCCCT() {
        $date = "2021-03-03"; 
        // create options for residents
        $ResidentAOption1CCCT = Option::insertGetId([
            "date" => $date,
            "resident" => 113,
            "schedule" => 143907,
            "attending" => 349746,
            "option" => 1,
            "isValid" => 1,
            "anesthesiologist_id" => 1
        ]);
        $ResidentBOption1CCCT = Option::insertGetId([
            "date" => $date,
            "resident" => 270,
            "schedule" => 143910,
            "attending" => 349746,
            "option" => 1,
            "isValid" => 1,
            "anesthesiologist_id" => 1
        ]);
        $ResidentCOption1CCCCT = Option::insertGetId([
            "date" => $date,
            "resident" => 300,
            "schedule" => 143914,
            "attending" => 349746,
            "option" => 1,
            "isValid" => 1,
            "anesthesiologist_id" => 1
        ]);
        // update total for residents needed for correct assignment
        Probability::where('resident', 113)->update(['total' => 0]);
        Probability::where('resident', 270)->update(['total' => 0]);
        Probability::where('resident', 300)->update(['total' => 0]);
        // assignment method call
        $autoAssignment = new AutoAssignment();
        $autoAssignment->assignment($date);
        // bools and values for assignments existing and current total for residents
        $foundAssignmentForA = Assignment::where('date', $date)
                            ->where('resident', 113)
                            ->where('schedule', 143907)
                            ->where('anesthesiologist_id', 1)
                            ->exists();
        $ProbTotalResidentA = Probability::where('resident', 113)->value('total');
        $foundAssignmentForBWithAnest =Assignment::where('date', $date)
                            ->where('resident', 270)
                            ->where('schedule', 143910)
                            ->where('anesthesiologist_id', 1)
                            ->exists();
        $foundAssignmentForBWithoutAnest =Assignment::where('date', $date)
                            ->where('resident', 270)
                            ->where('schedule', 143910)
                            ->where('anesthesiologist_id', NULL)
                            ->exists();
        $ProbTotalResidentB = Probability::where('resident', 270)->value('total');
        $foundAssignmentForCWithAnest =Assignment::where('date', $date)
                            ->where('resident',300)
                            ->where('schedule', 143914)
                            ->where('anesthesiologist_id', 1)
                            ->exists();
        $foundAssignmentForCWithoutAnest =Assignment::where('date', $date)
                            ->where('resident',300)
                            ->where('schedule', 143914)
                            ->where('anesthesiologist_id', NULL)
                            ->exists();
        $ProbTotalResidentC = Probability::where('resident', 300)->value('total');
        // assertions for correct assignments                    
        $this->assertTrue($foundAssignmentForA);
        $bGotAnest = $foundAssignmentForBWithAnest && $foundAssignmentForCWithoutAnest;
        $cGotAnest = $foundAssignmentForCWithAnest && $foundAssignmentForBWithoutAnest;
        $this->assertTrue( $bGotAnest || $cGotAnest);
        // assertions for correct total in probability table after assignment
        $this->assertEquals($ProbTotalResidentA, 0);
        if($bGotAnest){
            $this->assertEquals($ProbTotalResidentB, 0);
            $this->assertEquals($ProbTotalResidentC, 1);
        } else{
            $this->assertEquals($ProbTotalResidentB, 1);
            $this->assertEquals($ProbTotalResidentC, 0);
        }
        // delete dummy data for option table
        Option::find($ResidentAOption1CCCT)->delete();
        Option::find($ResidentBOption1CCCT)->delete();
        Option::find($ResidentCOption1CCCCT)->delete();
        // delete dummy assignments
        Assignment::where('date', $date)
            ->where('resident', 113)
            ->where('schedule', 143907)
            ->where('anesthesiologist_id', 1)
            ->delete();
        if($bGotAnest){
            Assignment::where('date', $date)
                ->where('resident', 270)
                ->where('schedule', 143910)
                ->where('anesthesiologist_id', 1)
                ->delete();
            Assignment::where('date', $date)
                ->where('resident', 300)
                ->where('schedule', 143914)
                ->where('anesthesiologist_id', NULL)
                ->delete();
        } else{
             Assignment::where('date', $date)
                ->where('resident', 270)
                ->where('schedule', 143910)
                ->where('anesthesiologist_id', NULL)
                ->delete();
            Assignment::where('date', $date)
                ->where('resident', 300)
                ->where('schedule', 143914)
                ->where('anesthesiologist_id', 1)
                ->delete();
        }
    }

    public function testAnestDoubleAssignedUH() {
        $date = "2021-03-03"; 
        // create options for residents
        $ResidentAOption1CCCT = Option::insertGetId([
            "date" => $date,
            "resident" => 113,
            "schedule" => 143905,
            "attending" => 349746,
            "option" => 1,
            "isValid" => 1,
            "anesthesiologist_id" => 1
        ]);
        $ResidentBOption1CCCT = Option::insertGetId([
            "date" => $date,
            "resident" => 270,
            "schedule" => 143909,
            "attending" => 349746,
            "option" => 1,
            "isValid" => 1,
            "anesthesiologist_id" => 1
        ]);
        $ResidentCOption1CCCCT = Option::insertGetId([
            "date" => $date,
            "resident" => 300,
            "schedule" => 143912,
            "attending" => 349746,
            "option" => 1,
            "isValid" => 1,
            "anesthesiologist_id" => 1
        ]);
        // update total for residents needed for correct assignment
        Probability::where('resident', 113)->update(['total' => 0]);
        Probability::where('resident', 270)->update(['total' => 0]);
        Probability::where('resident', 300)->update(['total' => 0]);
        // assignment method call
        $autoAssignment = new AutoAssignment();
        $autoAssignment->assignment($date);
        // bools and values for assignments existing and current total for residents
        $foundAssignmentForA = Assignment::where('date', $date)
                            ->where('resident', 113)
                            ->where('schedule', 143905)
                            ->where('anesthesiologist_id', 1)
                            ->exists();
        $ProbTotalResidentA = Probability::where('resident', 113)->value('total');
        $foundAssignmentForBWithAnest =Assignment::where('date', $date)
                            ->where('resident', 270)
                            ->where('schedule', 143909)
                            ->where('anesthesiologist_id', 1)
                            ->exists();
        $foundAssignmentForBWithoutAnest =Assignment::where('date', $date)
                            ->where('resident', 270)
                            ->where('schedule', 143909)
                            ->where('anesthesiologist_id', NULL)
                            ->exists();
        $ProbTotalResidentB = Probability::where('resident', 270)->value('total');
        $foundAssignmentForCWithAnest =Assignment::where('date', $date)
                            ->where('resident',300)
                            ->where('schedule', 143912)
                            ->where('anesthesiologist_id', 1)
                            ->exists();
        $foundAssignmentForCWithoutAnest =Assignment::where('date', $date)
                            ->where('resident',300)
                            ->where('schedule', 143912)
                            ->where('anesthesiologist_id', NULL)
                            ->exists();
        $ProbTotalResidentC = Probability::where('resident', 300)->value('total');
        // assertions for correct assignments                    
        $this->assertTrue($foundAssignmentForA);
        $bGotAnest = $foundAssignmentForBWithAnest && $foundAssignmentForCWithoutAnest;
        $cGotAnest = $foundAssignmentForCWithAnest && $foundAssignmentForBWithoutAnest;
        $this->assertTrue( $bGotAnest || $cGotAnest);
        // assertions for correct total in probability table after assignment
        $this->assertEquals($ProbTotalResidentA, 0);
        if($bGotAnest){
            $this->assertEquals($ProbTotalResidentB, 0);
            $this->assertEquals($ProbTotalResidentC, 1);
        } else{
            $this->assertEquals($ProbTotalResidentB, 1);
            $this->assertEquals($ProbTotalResidentC, 0);
        }
        // delete dummy data for option table
        Option::find($ResidentAOption1CCCT)->delete();
        Option::find($ResidentBOption1CCCT)->delete();
        Option::find($ResidentCOption1CCCCT)->delete();
        // delete dummy assignments
        Assignment::where('date', $date)
            ->where('resident', 113)
            ->where('schedule', 143905)
            ->where('anesthesiologist_id', 1)
            ->delete();
        if($bGotAnest){
            Assignment::where('date', $date)
                ->where('resident', 270)
                ->where('schedule', 143909)
                ->where('anesthesiologist_id', 1)
                ->delete();
            Assignment::where('date', $date)
                ->where('resident', 300)
                ->where('schedule', 143912)
                ->where('anesthesiologist_id', NULL)
                ->delete();
        } else{
             Assignment::where('date', $date)
                ->where('resident', 270)
                ->where('schedule', 143909)
                ->where('anesthesiologist_id', NULL)
                ->delete();
            Assignment::where('date', $date)
                ->where('resident', 300)
                ->where('schedule', 143912)
                ->where('anesthesiologist_id', 1)
                ->delete();
        }
    }

    public function testAnestDoubleAssignedCCCTLeasingUHGivenCCCTAssignment() {
        $date = "2021-03-03";
        // create options for residents 
        $ResidentAOption1CCCT = Option::insertGetId([
            "date" => $date,
            "resident" => 113,
            "schedule" => 143910,
            "attending" => 349746,
            "option" => 1,
            "isValid" => 1,
            "anesthesiologist_id" => 1
        ]);
        $ResidentBOption1CCCTLeasingUH = Option::insertGetId([
            "date" => $date,
            "resident" => 270,
            "schedule" => 143924,
            "attending" => 349746,
            "option" => 1,
            "isValid" => 1,
            "anesthesiologist_id" => 1
        ]);
        $ResidentCOption1CCCCT = Option::insertGetId([
            "date" => $date,
            "resident" => 300,
            "schedule" => 143910,
            "attending" => 349746,
            "option" => 1,
            "isValid" => 1,
            "anesthesiologist_id" => 1
        ]);
        $ResidentCOption2CCCCT = Option::insertGetId([
            "date" => $date,
            "resident" => 300,
            "schedule" => 143914,
            "attending" => 349746,
            "option" => 2,
            "isValid" => 1,
            "anesthesiologist_id" => 1
        ]);
        // update total for residents needed for correct assignment
        Probability::where('resident', 113)->update(['total' => 1]);
        Probability::where('resident', 270)->update(['total' => 0]);
        Probability::where('resident', 300)->update(['total' => 0]);
        // assignment method call
        $autoAssignment = new AutoAssignment();
        $autoAssignment->assignment($date);
        // bools and values for assignments existing and current total for residents
        $foundAssignmentForA = Assignment::where('date', $date)
                            ->where('resident', 113)
                            ->where('schedule', 143910)
                            ->where('anesthesiologist_id', 1)
                            ->exists();
        $ProbTotalResidentA = Probability::where('resident', 113)->value('total');
        $foundAssignmentForB =Assignment::where('date', $date)
                            ->where('resident', 270)
                            ->where('schedule', 143924)
                            ->where('anesthesiologist_id', 1)
                            ->exists();
        $ProbTotalResidentB = Probability::where('resident', 270)->value('total');
        $foundAssignmentForC =Assignment::where('date', $date)
                            ->where('resident',300)
                            ->where('schedule', 143914)
                            ->where('anesthesiologist_id', NULL)
                            ->exists();
        $ProbTotalResidentC = Probability::where('resident', 300)->value('total');
        // assertions for correct assignments                    
        $this->assertTrue($foundAssignmentForA);
        $this->assertTrue($foundAssignmentForB);
        $this->assertTrue($foundAssignmentForC);
        // assertions for correct total in probability table after assignment
        $this->assertEquals($ProbTotalResidentA, 1);
        $this->assertEquals($ProbTotalResidentB, 0);
        $this->assertEquals($ProbTotalResidentC, 3);
        // delete dummy data for option table
        Option::find($ResidentAOption1CCCT)->delete();
        Option::find($ResidentBOption1CCCTLeasingUH)->delete();
        Option::find($ResidentCOption1CCCCT)->delete();
        Option::find($ResidentCOption2CCCCT)->delete();
        // delete dummy assignments
        Assignment::where('date', $date)
            ->where('resident', 113)
            ->where('schedule', 143910)
            ->where('anesthesiologist_id', 1)
            ->delete();
        Assignment::where('date', $date)
            ->where('resident', 270)
            ->where('schedule', 143924)
            ->where('anesthesiologist_id', 1)
            ->delete();
        Assignment::where('date', $date)
            ->where('resident', 300)
            ->where('schedule', 143914)
            ->where('anesthesiologist_id', NULL)
            ->delete();
    }
    
    public function testAnestDoubleAssignedCCCTGivenCCCTLeasingUHAssignment() {
        $date = "2021-03-03"; 
        // create options for residents
        $ResidentAOption1CCCTLeasingUH = Option::insertGetId([
            "date" => $date,
            "resident" => 113,
            "schedule" => 143924,
            "attending" => 349746,
            "option" => 1,
            "isValid" => 1,
            "anesthesiologist_id" => 1
        ]);
        $ResidentBOption1CCCT = Option::insertGetId([
            "date" => $date,
            "resident" => 270,
            "schedule" => 143910,
            "attending" => 349746,
            "option" => 1,
            "isValid" => 1,
            "anesthesiologist_id" => 1
        ]);
        $ResidentCOption1CCCCT = Option::insertGetId([
            "date" => $date,
            "resident" => 300,
            "schedule" => 143910,
            "attending" => 349746,
            "option" => 1,
            "isValid" => 1,
            "anesthesiologist_id" => 1
        ]);
        $ResidentCOption2CCCCT = Option::insertGetId([
            "date" => $date,
            "resident" => 300,
            "schedule" => 143914,
            "attending" => 349746,
            "option" => 2,
            "isValid" => 1,
            "anesthesiologist_id" => 1
        ]);
        // update total for residents needed for correct assignment
        Probability::where('resident', 113)->update(['total' => 0]);
        Probability::where('resident', 270)->update(['total' => 1]);
        Probability::where('resident', 300)->update(['total' => 0]);
        // assignment method call
        $autoAssignment = new AutoAssignment();
        $autoAssignment->assignment($date);
        // bools and values for assignments existing and current total for residents
        $foundAssignmentForA = Assignment::where('date', $date)
                            ->where('resident', 113)
                            ->where('schedule', 143924)
                            ->where('anesthesiologist_id', 1)
                            ->exists();
        $ProbTotalResidentA = Probability::where('resident', 113)->value('total');
        $foundAssignmentForB =Assignment::where('date', $date)
                            ->where('resident', 270)
                            ->where('schedule', 143910)
                            ->where('anesthesiologist_id', 1)
                            ->exists();
        $ProbTotalResidentB = Probability::where('resident', 270)->value('total');
        $foundAssignmentForC =Assignment::where('date', $date)
                            ->where('resident',300)
                            ->where('schedule', 143914)
                            ->where('anesthesiologist_id', NULL)
                            ->exists();
        $ProbTotalResidentC = Probability::where('resident', 300)->value('total');
        // assertions for correct assignments                    
        $this->assertTrue($foundAssignmentForA);
        $this->assertTrue($foundAssignmentForB);
        $this->assertTrue($foundAssignmentForC);
        // assertions for correct total in probability table after assignment
        $this->assertEquals($ProbTotalResidentA, 0);
        $this->assertEquals($ProbTotalResidentB, 1);
        $this->assertEquals($ProbTotalResidentC, 3);
        // delete dummy data for option table
        Option::find($ResidentAOption1CCCTLeasingUH)->delete();
        Option::find($ResidentBOption1CCCT)->delete();
        Option::find($ResidentCOption1CCCCT)->delete();
        Option::find($ResidentCOption2CCCCT)->delete();
        // delete dummy assignments
        Assignment::where('date', $date)
            ->where('resident', 113)
            ->where('schedule', 143924)
            ->where('anesthesiologist_id', 1)
            ->delete();
        Assignment::where('date', $date)
            ->where('resident', 270)
            ->where('schedule', 143910)
            ->where('anesthesiologist_id', 1)
            ->delete();
        Assignment::where('date', $date)
            ->where('resident', 300)
            ->where('schedule', 143914)
            ->where('anesthesiologist_id', NULL)
            ->delete();
    }

    public function testAnestDoubleAssignedCCCTLeasingUHGivenUHAssignment() {
        $date = "2021-03-03"; 
        // create options for residents
        $ResidentAOption1UH = Option::insertGetId([
            "date" => $date,
            "resident" => 113,
            "schedule" => 143905,
            "attending" => 349746,
            "option" => 1,
            "isValid" => 1,
            "anesthesiologist_id" => 1
        ]);
        $ResidentBOption1UH = Option::insertGetId([
            "date" => $date,
            "resident" => 270,
            "schedule" => 143905,
            "attending" => 349746,
            "option" => 1,
            "isValid" => 1,
            "anesthesiologist_id" => 1
        ]);
        $ResidentBOption2CCCTLeasingUH = Option::insertGetId([
            "date" => $date,
            "resident" => 270,
            "schedule" => 143924,
            "attending" => 349746,
            "option" => 2,
            "isValid" => 1,
            "anesthesiologist_id" => 1
        ]);
        $ResidentCOption1UH = Option::insertGetId([
            "date" => $date,
            "resident" => 300,
            "schedule" => 143905,
            "attending" => 349746,
            "option" => 1,
            "isValid" => 1,
            "anesthesiologist_id" => 1
        ]);
        $ResidentCOption2CCCTLeasingUH = Option::insertGetId([
            "date" => $date,
            "resident" => 300,
            "schedule" => 143924,
            "attending" => 349746,
            "option" => 2,
            "isValid" => 1,
            "anesthesiologist_id" => 1
        ]);
        $ResidentCOption3UH = Option::insertGetId([
            "date" => $date,
            "resident" => 300,
            "schedule" => 143909,
            "attending" => 349746,
            "option" => 3,
            "isValid" => 1,
            "anesthesiologist_id" => 1
        ]);
        // update total for residents needed for correct assignment
        Probability::where('resident', 113)->update(['total' => 2]);
        Probability::where('resident', 270)->update(['total' => 1]);
        Probability::where('resident', 300)->update(['total' => 0]);
        // assignment method call
        $autoAssignment = new AutoAssignment();
        $autoAssignment->assignment($date);
        // bools and values for assignments existing and current total for residents
        $foundAssignmentForA = Assignment::where('date', $date)
                            ->where('resident', 113)
                            ->where('schedule', 143905)
                            ->where('anesthesiologist_id', 1)
                            ->exists();
        $ProbTotalResidentA = Probability::where('resident', 113)->value('total');
        $foundAssignmentForB =Assignment::where('date', $date)
                            ->where('resident', 270)
                            ->where('schedule', 143924)
                            ->where('anesthesiologist_id', NULL)
                            ->exists();
        $ProbTotalResidentB = Probability::where('resident', 270)->value('total');
        $foundAssignmentForC =Assignment::where('date', $date)
                            ->where('resident',300)
                            ->where('schedule', 143909)
                            ->where('anesthesiologist_id', 1)
                            ->exists();
        $ProbTotalResidentC = Probability::where('resident', 300)->value('total');
        // assertions for correct assignments                    
        $this->assertTrue($foundAssignmentForA);
        $this->assertTrue($foundAssignmentForB);
        $this->assertTrue($foundAssignmentForC); 
        // assertions for correct total in probability table after assignment
        $this->assertEquals($ProbTotalResidentA, 2);
        $this->assertEquals($ProbTotalResidentB, 4);
        $this->assertEquals($ProbTotalResidentC, 4);
        // delete dummy data for option table
        Option::find($ResidentAOption1UH)->delete();
        Option::find($ResidentBOption1UH)->delete();
        Option::find($ResidentBOption2CCCTLeasingUH)->delete();
        Option::find($ResidentCOption1UH)->delete();
        Option::find($ResidentCOption2CCCTLeasingUH)->delete();
        Option::find($ResidentCOption3UH)->delete();
        // delete dummy assignments
        Assignment::where('date', $date)
            ->where('resident', 113)
            ->where('schedule', 143905)
            ->where('anesthesiologist_id', 1)
            ->delete();
        Assignment::where('date', $date)
            ->where('resident', 270)
            ->where('schedule', 143924)
            ->where('anesthesiologist_id', NULL)
            ->delete();
        Assignment::where('date', $date)
            ->where('resident', 300)
            ->where('schedule', 143909)
            ->where('anesthesiologist_id', 1)
            ->delete();
    }
  
    public function testAnestDoubleAssignedUHGivenCCCTLeasingUHAssignment() {
        $date = "2021-03-03"; 
        // create options for residents
        $ResidentAOption1CCCTLeasingUH = Option::insertGetId([
            "date" => $date,
            "resident" => 113,
            "schedule" => 143924,
            "attending" => 349746,
            "option" => 1,
            "isValid" => 1,
            "anesthesiologist_id" => 1
        ]);
        $ResidentBOption1CCCTLeasingUH = Option::insertGetId([
            "date" => $date,
            "resident" => 270,
            "schedule" => 143924,
            "attending" => 349746,
            "option" => 1,
            "isValid" => 1,
            "anesthesiologist_id" => 1
        ]);
        $ResidentBOption2UH = Option::insertGetId([
            "date" => $date,
            "resident" => 270,
            "schedule" => 143905,
            "attending" => 349746,
            "option" => 2,
            "isValid" => 1,
            "anesthesiologist_id" => 1
        ]);
        $ResidentCOption1CCCTLeasingUH = Option::insertGetId([
            "date" => $date,
            "resident" => 300,
            "schedule" => 143924,
            "attending" => 349746,
            "option" => 1,
            "isValid" => 1,
            "anesthesiologist_id" => 1
        ]);
        $ResidentCOption2CCCCT = Option::insertGetId([
            "date" => $date,
            "resident" => 300,
            "schedule" => 143914,
            "attending" => 349746,
            "option" => 2,
            "isValid" => 1,
            "anesthesiologist_id" => 1
        ]);
        // update total for residents needed for correct assignment
        Probability::where('resident', 113)->update(['total' => 1]);
        Probability::where('resident', 270)->update(['total' => 0]);
        Probability::where('resident', 300)->update(['total' => 0]);
        // assignment method call
        $autoAssignment = new AutoAssignment();
        $autoAssignment->assignment($date);
        // bools and values for assignments existing and current total for residents
        $foundAssignmentForA = Assignment::where('date', $date)
                            ->where('resident', 113)
                            ->where('schedule', 143924)
                            ->where('anesthesiologist_id', 1)
                            ->exists();
        $ProbTotalResidentA = Probability::where('resident', 113)->value('total');
        $foundAssignmentForB =Assignment::where('date', $date)
                            ->where('resident', 270)
                            ->where('schedule', 143905)
                            ->where('anesthesiologist_id', NULL)
                            ->exists();
        $ProbTotalResidentB = Probability::where('resident', 270)->value('total');
        $foundAssignmentForC =Assignment::where('date', $date)
                            ->where('resident',300)
                            ->where('schedule', 143914)
                            ->where('anesthesiologist_id', 1)
                            ->exists();
        $ProbTotalResidentC = Probability::where('resident', 300)->value('total');
        // assertions for correct assignments                    
        $this->assertTrue($foundAssignmentForA);
        $this->assertTrue($foundAssignmentForB);
        $this->assertTrue($foundAssignmentForC);
        // assertions for correct total in probability table after assignment
        $this->assertEquals($ProbTotalResidentA, 1);
        $this->assertEquals($ProbTotalResidentB, 3);
        $this->assertEquals($ProbTotalResidentC, 2);
        // delete dummy data for option table
        Option::find($ResidentAOption1CCCTLeasingUH)->delete();
        Option::find($ResidentBOption1CCCTLeasingUH)->delete();
        Option::find($ResidentBOption2UH)->delete();
        Option::find($ResidentCOption1CCCTLeasingUH)->delete();
        Option::find($ResidentCOption2CCCCT)->delete();
        // delete dummy assignments
        Assignment::where('date', $date)
            ->where('resident', 113)
            ->where('schedule', 143924)
            ->where('anesthesiologist_id', 1)
            ->delete();
        Assignment::where('date', $date)
            ->where('resident', 270)
            ->where('schedule', 143905)
            ->where('anesthesiologist_id', NULL)
            ->delete();
        Assignment::where('date', $date)
            ->where('resident', 300)
            ->where('schedule', 143914)
            ->where('anesthesiologist_id', 1)
            ->delete();
    }
    
}
