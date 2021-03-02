<?php

namespace Tests\Unit;

use App\AutoAssignment;
use Tests\TestCase;
use Illuminate\Support\Facades\DB;

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
        $ResidentAOption1CCCT = DB::table("option")->insertGetId([
            "date" => $date,
            "resident" => 113,
            "schedule" => 143907,
            "attending" => 349746,
            "option" => 1,
            "isValid" => 1,
            "anesthesiologist_id" => 1
        ]);
        $ResidentBOption1CCCT = DB::table("option")->insertGetId([
            "date" => $date,
            "resident" => 270,
            "schedule" => 143907,
            "attending" => 349746,
            "option" => 1,
            "isValid" => 1,
            "anesthesiologist_id" => 1
        ]);
        $ResidentBOption2CCCT = DB::table("option")->insertGetId([
            "date" => $date,
            "resident" => 270,
            "schedule" => 143910,
            "attending" => 349746,
            "option" => 2,
            "isValid" => 1,
            "anesthesiologist_id" => 2
        ]);
        $ResidentCOption1CCCCT = DB::table("option")->insertGetId([
            "date" => $date,
            "resident" => 300,
            "schedule" => 143907,
            "attending" => 349746,
            "option" => 1,
            "isValid" => 1,
            "anesthesiologist_id" => 1
        ]);
        $ResidentCOption2CCCT = DB::table("option")->insertGetId([
            "date" => $date,
            "resident" => 300,
            "schedule" => 143910,
            "attending" => 349746,
            "option" => 2,
            "isValid" => 1,
            "anesthesiologist_id" => 2
        ]);
        $ResidentCOption3CCCT = DB::table("option")->insertGetId([
            "date" => $date,
            "resident" => 300,
            "schedule" => 143914,
            "attending" => 349746,
            "option" => 3,
            "isValid" => 1,
            "anesthesiologist_id" => 3
        ]);
        // update total for residents needed for correct assignment
        DB::table('probability')->where('resident', 113)->update(['total' => 2]);
        DB::table('probability')->where('resident', 270)->update(['total' => 1]);
        DB::table('probability')->where('resident', 300)->update(['total' => 0]);
        // assignment method call
        $autoAssignment = new AutoAssignment();
        $autoAssignment->assignment($date);
        // bools and values for assignments existing and current total for residents
        $foundAssignmentForA = DB::table('assignment')
                            ->where('date', $date)
                            ->where('resident', 113)
                            ->where('schedule', 143907)
                            ->where('anesthesiologist_id', 1)
                            ->exists();
        $ProbTotalResidentA = DB::table('probability')
                            ->where('resident', 113)
                            ->value('total');
        $foundAssignmentForB =DB::table('assignment')
                            ->where('date', $date)
                            ->where('resident', 270)
                            ->where('schedule', 143910)
                            ->where('anesthesiologist_id', 2)
                            ->exists();
        $ProbTotalResidentB = DB::table('probability')
                            ->where('resident', 270)
                            ->value('total');
        $foundAssignmentForC =DB::table('assignment')
                            ->where('date', $date)
                            ->where('resident',300)
                            ->where('schedule', 143914)
                            ->where('anesthesiologist_id', 3)
                            ->exists();
        $ProbTotalResidentC = DB::table('probability')
                            ->where('resident', 300)
                            ->value('total');
        // assertions for correct assignments                    
        $this->assertTrue($foundAssignmentForA);
        $this->assertTrue($foundAssignmentForB);
        $this->assertTrue($foundAssignmentForC);
        // assertions for correct total in probability table after assignment
        $this->assertEquals($ProbTotalResidentA, 2);
        $this->assertEquals($ProbTotalResidentB, 3);
        $this->assertEquals($ProbTotalResidentC, 4);
        // delete dummy data for option table
        DB::table('option')
            ->where('id', $ResidentAOption1CCCT)
            ->delete();
        DB::table('option')
            ->where('id', $ResidentBOption1CCCT)
            ->delete();
        DB::table('option')
            ->where('id', $ResidentBOption2CCCT)
            ->delete();
        DB::table('option')
            ->where('id', $ResidentCOption1CCCCT)
            ->delete();
        DB::table('option')
            ->where('id', $ResidentCOption2CCCT)
            ->delete();
        DB::table('option')
            ->where('id', $ResidentCOption3CCCT)
            ->delete();
        // delete dummy assignments
        DB::table('assignment')
            ->where('date', $date)
            ->where('resident', 113)
            ->where('schedule', 143907)
            ->where('anesthesiologist_id', 1)
            ->delete();
        DB::table('assignment')
            ->where('date', $date)
            ->where('resident', 270)
            ->where('schedule', 143910)
            ->where('anesthesiologist_id', 2)
            ->delete();
        DB::table('assignment')
            ->where('date', $date)
            ->where('resident', 300)
            ->where('schedule', 143914)
            ->where('anesthesiologist_id', 3)
            ->delete();
    }

    public function testPreferenceTicketingWithAnestsNotGranted() {
        $date = "2021-03-03";
        // create options for residents 
        $ResidentAOption1EP = DB::table("option")->insertGetId([
            "date" => $date,
            "resident" => 113,
            "schedule" => 143908,
            "attending" => 349746,
            "option" => 1,
            "isValid" => 1,
            "anesthesiologist_id" => 1
        ]);
        $ResidentBOption1CCCT = DB::table("option")->insertGetId([
            "date" => $date,
            "resident" => 270,
            "schedule" => 143907,
            "attending" => 349746,
            "option" => 1,
            "isValid" => 1,
            "anesthesiologist_id" => 1
        ]);
        $ResidentCOption1CCCT = DB::table("option")->insertGetId([
            "date" => $date,
            "resident" => 300,
            "schedule" => 143907,
            "attending" => 349746,
            "option" => 1,
            "isValid" => 1,
            "anesthesiologist_id" => 1
        ]);
        $ResidentCOption2CCCT = DB::table("option")->insertGetId([
            "date" => $date,
            "resident" => 300,
            "schedule" => 143910,
            "attending" => 349746,
            "option" => 2,
            "isValid" => 1,
            "anesthesiologist_id" => 1
        ]);
        $ResidentDOption1CCCCT = DB::table("option")->insertGetId([
            "date" => $date,
            "resident" => 881,
            "schedule" => 143907,
            "attending" => 349746,
            "option" => 1,
            "isValid" => 1,
            "anesthesiologist_id" => 1
        ]);
        $ResidentDOption2CCCT = DB::table("option")->insertGetId([
            "date" => $date,
            "resident" => 881,
            "schedule" => 143910,
            "attending" => 349746,
            "option" => 2,
            "isValid" => 1,
            "anesthesiologist_id" => 1
        ]);
        $ResidentDOption3CCCT = DB::table("option")->insertGetId([
            "date" => $date,
            "resident" => 881,
            "schedule" => 143914,
            "attending" => 349746,
            "option" => 3,
            "isValid" => 1,
            "anesthesiologist_id" => 1
        ]);
        // update total for residents needed for correct assignment
        DB::table('probability')->where('resident', 113)->update(['total' => 0]);
        DB::table('probability')->where('resident', 270)->update(['total' => 2]);
        DB::table('probability')->where('resident', 300)->update(['total' => 1]);
        DB::table('probability')->where('resident', 881)->update(['total' => 0]);
        // assignment method call
        $autoAssignment = new AutoAssignment();
        $autoAssignment->assignment($date);
        // bools and values for assignments existing and current total for residents
        $foundAssignmentForA = DB::table('assignment')
                            ->where('date', $date)
                            ->where('resident', 113)
                            ->where('schedule', 143908)
                            ->where('anesthesiologist_id', 1)
                            ->exists();
        $ProbTotalResidentA = DB::table('probability')
                            ->where('resident', 113)
                            ->value('total');
        $foundAssignmentForB =DB::table('assignment')
                            ->where('date', $date)
                            ->where('resident', 270)
                            ->where('schedule', 143907)
                            ->where('anesthesiologist_id', NULL)
                            ->exists();
        $ProbTotalResidentB = DB::table('probability')
                            ->where('resident', 270)
                            ->value('total');
        $foundAssignmentForC =DB::table('assignment')
                            ->where('date', $date)
                            ->where('resident', 300)
                            ->where('schedule', 143910)
                            ->where('anesthesiologist_id', NULL)
                            ->exists();
        $ProbTotalResidentC = DB::table('probability')
                            ->where('resident', 300)
                            ->value('total');
        $foundAssignmentForD =DB::table('assignment')
                            ->where('date', $date)
                            ->where('resident', 881)
                            ->where('schedule', 143914)
                            ->where('anesthesiologist_id', NULL)
                            ->exists();
        $ProbTotalResidentD = DB::table('probability')
                            ->where('resident', 881)
                            ->value('total');
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
        DB::table('option')
            ->where('id', $ResidentAOption1EP)
            ->delete();
        DB::table('option')
            ->where('id', $ResidentBOption1CCCT)
            ->delete();
        DB::table('option')
            ->where('id', $ResidentCOption1CCCT)
            ->delete();
        DB::table('option')
            ->where('id', $ResidentCOption2CCCT)
            ->delete();
        DB::table('option')
            ->where('id', $ResidentDOption1CCCCT)
            ->delete();
        DB::table('option')
            ->where('id', $ResidentDOption2CCCT)
            ->delete();
        DB::table('option')
            ->where('id', $ResidentDOption3CCCT)
            ->delete();
        // delete dummy assignments
        DB::table('assignment')
            ->where('date', $date)
            ->where('resident', 113)
            ->where('schedule', 143908)
            ->where('anesthesiologist_id', 1)
            ->delete();
        DB::table('assignment')
            ->where('date', $date)
            ->where('resident', 270)
            ->where('schedule', 143907)
            ->where('anesthesiologist_id', NULL)
            ->delete();
        DB::table('assignment')
            ->where('date', $date)
            ->where('resident', 300)
            ->where('schedule', 143910)
            ->where('anesthesiologist_id', NULL)
            ->delete();
        DB::table('assignment')
            ->where('date', $date)
            ->where('resident', 881)
            ->where('schedule', 143914)
            ->where('anesthesiologist_id', NULL)
            ->delete();
    }
    
    public function testPreferenceTicketingNoAnestsPreferences() {
        $date = "2021-03-03";
        // create options for residents 
        $ResidentAOption1CCCT = DB::table("option")->insertGetId([
            "date" => $date,
            "resident" => 113,
            "schedule" => 143907,
            "attending" => 349746,
            "option" => 1,
            "isValid" => 1,
            "anesthesiologist_id" => NULL
        ]);
        $ResidentBOption1CCCT = DB::table("option")->insertGetId([
            "date" => $date,
            "resident" => 270,
            "schedule" => 143907,
            "attending" => 349746,
            "option" => 1,
            "isValid" => 1,
            "anesthesiologist_id" => NULL
        ]);
        $ResidentBOption2CCCT = DB::table("option")->insertGetId([
            "date" => $date,
            "resident" => 270,
            "schedule" => 143910,
            "attending" => 349746,
            "option" => 2,
            "isValid" => 1,
            "anesthesiologist_id" => NULL
        ]);
        $ResidentCOption1CCCCT = DB::table("option")->insertGetId([
            "date" => $date,
            "resident" => 300,
            "schedule" => 143907,
            "attending" => 349746,
            "option" => 1,
            "isValid" => 1,
            "anesthesiologist_id" => NULL
        ]);
        $ResidentCOption2CCCT = DB::table("option")->insertGetId([
            "date" => $date,
            "resident" => 300,
            "schedule" => 143910,
            "attending" => 349746,
            "option" => 2,
            "isValid" => 1,
            "anesthesiologist_id" => NULL
        ]);
        $ResidentCOption3CCCT = DB::table("option")->insertGetId([
            "date" => $date,
            "resident" => 300,
            "schedule" => 143914,
            "attending" => 349746,
            "option" => 3,
            "isValid" => 1,
            "anesthesiologist_id" => NULL
        ]);
        // update total for residents needed for correct assignment
        DB::table('probability')->where('resident', 113)->update(['total' => 2]);
        DB::table('probability')->where('resident', 270)->update(['total' => 1]);
        DB::table('probability')->where('resident', 300)->update(['total' => 0]);
        // assignment method call
        $autoAssignment = new AutoAssignment();
        $autoAssignment->assignment($date);
        // bools and values for assignments existing and current total for residents
        $foundAssignmentForA = DB::table('assignment')
                            ->where('date', $date)
                            ->where('resident', 113)
                            ->where('schedule', 143907)
                            ->where('anesthesiologist_id', NULL)
                            ->exists();
        $ProbTotalResidentA = DB::table('probability')
                            ->where('resident', 113)
                            ->value('total');
        $foundAssignmentForB =DB::table('assignment')
                            ->where('date', $date)
                            ->where('resident', 270)
                            ->where('schedule', 143910)
                            ->where('anesthesiologist_id', NULL)
                            ->exists();
        $ProbTotalResidentB = DB::table('probability')
                            ->where('resident', 270)
                            ->value('total');
        $foundAssignmentForC =DB::table('assignment')
                            ->where('date', $date)
                            ->where('resident',300)
                            ->where('schedule', 143914)
                            ->where('anesthesiologist_id', NULL)
                            ->exists();
        $ProbTotalResidentC = DB::table('probability')
                            ->where('resident', 300)
                            ->value('total');
        // assertions for correct assignments                    
        $this->assertTrue($foundAssignmentForA);
        $this->assertTrue($foundAssignmentForB);
        $this->assertTrue($foundAssignmentForC);
        // assertions for correct total in probability table after assignment
        $this->assertEquals($ProbTotalResidentA, 2);
        $this->assertEquals($ProbTotalResidentB, 3);
        $this->assertEquals($ProbTotalResidentC, 4);
        // delete dummy data for option table
        DB::table('option')
            ->where('id', $ResidentAOption1CCCT)
            ->delete();
        DB::table('option')
            ->where('id', $ResidentBOption1CCCT)
            ->delete();
        DB::table('option')
            ->where('id', $ResidentBOption2CCCT)
            ->delete();
        DB::table('option')
            ->where('id', $ResidentCOption1CCCCT)
            ->delete();
        DB::table('option')
            ->where('id', $ResidentCOption2CCCT)
            ->delete();
        DB::table('option')
            ->where('id', $ResidentCOption3CCCT)
            ->delete();
        // delete dummy assignments
        DB::table('assignment')
            ->where('date', $date)
            ->where('resident', 113)
            ->where('schedule', 143907)
            ->where('anesthesiologist_id', NULL)
            ->delete();
        DB::table('assignment')
            ->where('date', $date)
            ->where('resident', 270)
            ->where('schedule', 143910)
            ->where('anesthesiologist_id', NULL)
            ->delete();
        DB::table('assignment')
            ->where('date', $date)
            ->where('resident', 300)
            ->where('schedule', 143914)
            ->where('anesthesiologist_id', NULL)
            ->delete();
    }

    public function testPreferenceTicketingForUnassignedResidents() {
        $date = "2021-03-03"; 
        // create options for residents
        $ResidentAOption1CCCT = DB::table("option")->insertGetId([
            "date" => $date,
            "resident" => 113,
            "schedule" => 143910,
            "attending" => 349746,
            "option" => 1,
            "isValid" => 1,
            "anesthesiologist_id" => 1
        ]);
        $ResidentBOption1CCCT = DB::table("option")->insertGetId([
            "date" => $date,
            "resident" => 270,
            "schedule" => 143910,
            "attending" => 349746,
            "option" => 1,
            "isValid" => 1,
            "anesthesiologist_id" => 1
        ]);
        $ResidentCOption1CCCT = DB::table("option")->insertGetId([
            "date" => $date,
            "resident" => 300,
            "schedule" => 143910,
            "attending" => 349746,
            "option" => 1,
            "isValid" => 1,
            "anesthesiologist_id" => 1
        ]);
        $ResidentCOption2CCCT = DB::table("option")->insertGetId([
            "date" => $date,
            "resident" => 300,
            "schedule" => 143910,
            "attending" => 349746,
            "option" => 2,
            "isValid" => 1,
            "anesthesiologist_id" => 1
        ]);
        $ResidentDOption1CCCCT = DB::table("option")->insertGetId([
            "date" => $date,
            "resident" => 881,
            "schedule" => 143910,
            "attending" => 349746,
            "option" => 1,
            "isValid" => 1,
            "anesthesiologist_id" => 1
        ]);
        $ResidentDOption2CCCT = DB::table("option")->insertGetId([
            "date" => $date,
            "resident" => 881,
            "schedule" => 143910,
            "attending" => 349746,
            "option" => 2,
            "isValid" => 1,
            "anesthesiologist_id" => 1
        ]);
        $ResidentDOption3CCCT = DB::table("option")->insertGetId([
            "date" => $date,
            "resident" => 881,
            "schedule" => 143910,
            "attending" => 349746,
            "option" => 3,
            "isValid" => 1,
            "anesthesiologist_id" => 1
        ]);
        // update total for residents needed for correct assignment
        DB::table('probability')->where('resident', 113)->update(['total' => 1]);
        DB::table('probability')->where('resident', 270)->update(['total' => 0]);
        DB::table('probability')->where('resident', 300)->update(['total' => 0]);
        DB::table('probability')->where('resident', 881)->update(['total' => 0]);
        // assignment method call
        $autoAssignment = new AutoAssignment();
        $autoAssignment->assignment($date);
        // bools and values for assignments existing and current total for residents
        $foundAssignmentForA = DB::table('assignment')
                            ->where('date', $date)
                            ->where('resident', 113)
                            ->where('schedule', 143910)
                            ->where('anesthesiologist_id', 1)
                            ->exists();
        $ProbTotalResidentA = DB::table('probability')
                            ->where('resident', 113)
                            ->value('total');
        $ProbTotalResidentB = DB::table('probability')
                            ->where('resident', 270)
                            ->value('total');
        $ProbTotalResidentC = DB::table('probability')
                            ->where('resident', 300)
                            ->value('total');
        $ProbTotalResidentD = DB::table('probability')
                            ->where('resident', 881)
                            ->value('total');
        // assertions for correct assignments                    
        $this->assertTrue($foundAssignmentForA);
        // assertions for correct total in probability table after assignment
        $this->assertEquals($ProbTotalResidentA, 1);
        $this->assertEquals($ProbTotalResidentB, 6);
        $this->assertEquals($ProbTotalResidentC, 6);
        $this->assertEquals($ProbTotalResidentD, 6);
        // delete dummy data for option table
        DB::table('option')
            ->where('id', $ResidentAOption1CCCT)
            ->delete();
        DB::table('option')
            ->where('id', $ResidentBOption1CCCT)
            ->delete();
        DB::table('option')
            ->where('id', $ResidentCOption1CCCT)
            ->delete();
        DB::table('option')
            ->where('id', $ResidentCOption2CCCT)
            ->delete();
        DB::table('option')
            ->where('id', $ResidentDOption1CCCCT)
            ->delete();
        DB::table('option')
            ->where('id', $ResidentDOption2CCCT)
            ->delete();
        DB::table('option')
            ->where('id', $ResidentDOption3CCCT)
            ->delete();
        // delete dummy assignments
        DB::table('assignment')
            ->where('date', $date)
            ->where('resident', 113)
            ->where('schedule', 143910)
            ->where('anesthesiologist_id', 1)
            ->delete();  
    }

    // Tests for Anesthesiologist Preference Assignment

    public function testAnestAssignedOnce() {
        $date = "2021-03-03"; 
        // create options for residents
        $ResidentAOption1EP = DB::table("option")->insertGetId([
            "date" => $date,
            "resident" => 113,
            "schedule" => 143908,
            "attending" => 349746,
            "option" => 1,
            "isValid" => 1,
            "anesthesiologist_id" => 1
        ]);
        $ResidentBOption1CCCT = DB::table("option")->insertGetId([
            "date" => $date,
            "resident" => 270,
            "schedule" => 143910,
            "attending" => 349746,
            "option" => 1,
            "isValid" => 1,
            "anesthesiologist_id" => 1
        ]);
        // update total for residents needed for correct assignment
        DB::table('probability')->where('resident', 113)->update(['total' => 0]);
        DB::table('probability')->where('resident', 270)->update(['total' => 0]);
        // assignment method call
        $autoAssignment = new AutoAssignment();
        $autoAssignment->assignment($date);
        // bools and values for assignments existing and current total for residents
        $foundAssignmentForA = DB::table('assignment')
                            ->where('date', $date)
                            ->where('resident', 113)
                            ->where('schedule', 143908)
                            ->where('anesthesiologist_id', 1)
                            ->exists();
        $ProbTotalResidentA = DB::table('probability')
                            ->where('resident', 113)
                            ->value('total');
        $foundAssignmentForB =DB::table('assignment')
                            ->where('date', $date)
                            ->where('resident', 270)
                            ->where('schedule', 143910)
                            ->where('anesthesiologist_id', NULL)
                            ->exists();
        $ProbTotalResidentB = DB::table('probability')
                            ->where('resident', 270)
                            ->value('total');
        // assertions for correct assignments                    
        $this->assertTrue($foundAssignmentForA);
        $this->assertTrue($foundAssignmentForB);
        // assertions for correct total in probability table after assignment
        $this->assertEquals($ProbTotalResidentA, 0);
        $this->assertEquals($ProbTotalResidentB, 1);
        // delete dummy data for option table
        DB::table('option')
            ->where('id', $ResidentAOption1EP)
            ->delete();
        DB::table('option')
            ->where('id', $ResidentBOption1CCCT)
            ->delete();
        // delete dummy assignments
        DB::table('assignment')
            ->where('date', $date)
            ->where('resident', 113)
            ->where('schedule', 143908)
            ->where('anesthesiologist_id', 1)
            ->delete();
        DB::table('assignment')
            ->where('date', $date)
            ->where('resident', 270)
            ->where('schedule', 143910)
            ->where('anesthesiologist_id', NULL)
            ->delete();   
    }

    public function testAnestDoubleAssignedCCCT() {
        $date = "2021-03-03"; 
        // create options for residents
        $ResidentAOption1CCCT = DB::table("option")->insertGetId([
            "date" => $date,
            "resident" => 113,
            "schedule" => 143907,
            "attending" => 349746,
            "option" => 1,
            "isValid" => 1,
            "anesthesiologist_id" => 1
        ]);
        $ResidentBOption1CCCT = DB::table("option")->insertGetId([
            "date" => $date,
            "resident" => 270,
            "schedule" => 143910,
            "attending" => 349746,
            "option" => 1,
            "isValid" => 1,
            "anesthesiologist_id" => 1
        ]);
        $ResidentCOption1CCCCT = DB::table("option")->insertGetId([
            "date" => $date,
            "resident" => 300,
            "schedule" => 143914,
            "attending" => 349746,
            "option" => 1,
            "isValid" => 1,
            "anesthesiologist_id" => 1
        ]);
        // update total for residents needed for correct assignment
        DB::table('probability')->where('resident', 113)->update(['total' => 0]);
        DB::table('probability')->where('resident', 270)->update(['total' => 0]);
        DB::table('probability')->where('resident', 300)->update(['total' => 0]);
        // assignment method call
        $autoAssignment = new AutoAssignment();
        $autoAssignment->assignment($date);
        // bools and values for assignments existing and current total for residents
        $foundAssignmentForA = DB::table('assignment')
                            ->where('date', $date)
                            ->where('resident', 113)
                            ->where('schedule', 143907)
                            ->where('anesthesiologist_id', 1)
                            ->exists();
        $ProbTotalResidentA = DB::table('probability')
                            ->where('resident', 113)
                            ->value('total');
        $foundAssignmentForBWithAnest =DB::table('assignment')
                            ->where('date', $date)
                            ->where('resident', 270)
                            ->where('schedule', 143910)
                            ->where('anesthesiologist_id', 1)
                            ->exists();
        $foundAssignmentForBWithoutAnest =DB::table('assignment')
                            ->where('date', $date)
                            ->where('resident', 270)
                            ->where('schedule', 143910)
                            ->where('anesthesiologist_id', NULL)
                            ->exists();
        $ProbTotalResidentB = DB::table('probability')
                            ->where('resident', 270)
                            ->value('total');
        $foundAssignmentForCWithAnest =DB::table('assignment')
                            ->where('date', $date)
                            ->where('resident',300)
                            ->where('schedule', 143914)
                            ->where('anesthesiologist_id', 1)
                            ->exists();
        $foundAssignmentForCWithoutAnest =DB::table('assignment')
                            ->where('date', $date)
                            ->where('resident',300)
                            ->where('schedule', 143914)
                            ->where('anesthesiologist_id', NULL)
                            ->exists();
        $ProbTotalResidentC = DB::table('probability')
                            ->where('resident', 300)
                            ->value('total');
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
        DB::table('option')
            ->where('id', $ResidentAOption1CCCT)
            ->delete();
        DB::table('option')
            ->where('id', $ResidentBOption1CCCT)
            ->delete();
        DB::table('option')
            ->where('id', $ResidentCOption1CCCCT)
            ->delete();
        // delete dummy assignments
        DB::table('assignment')
            ->where('date', $date)
            ->where('resident', 113)
            ->where('schedule', 143907)
            ->where('anesthesiologist_id', 1)
            ->delete();
        if($bGotAnest){
            DB::table('assignment')
                ->where('date', $date)
                ->where('resident', 270)
                ->where('schedule', 143910)
                ->where('anesthesiologist_id', 1)
                ->delete();
            DB::table('assignment')
                ->where('date', $date)
                ->where('resident', 300)
                ->where('schedule', 143914)
                ->where('anesthesiologist_id', NULL)
                ->delete();
        } else{
             DB::table('assignment')
                ->where('date', $date)
                ->where('resident', 270)
                ->where('schedule', 143910)
                ->where('anesthesiologist_id', NULL)
                ->delete();
            DB::table('assignment')
                ->where('date', $date)
                ->where('resident', 300)
                ->where('schedule', 143914)
                ->where('anesthesiologist_id', 1)
                ->delete();
        }
    }

    public function testAnestDoubleAssignedUH() {
        $date = "2021-03-03"; 
        // create options for residents
        $ResidentAOption1CCCT = DB::table("option")->insertGetId([
            "date" => $date,
            "resident" => 113,
            "schedule" => 143905,
            "attending" => 349746,
            "option" => 1,
            "isValid" => 1,
            "anesthesiologist_id" => 1
        ]);
        $ResidentBOption1CCCT = DB::table("option")->insertGetId([
            "date" => $date,
            "resident" => 270,
            "schedule" => 143909,
            "attending" => 349746,
            "option" => 1,
            "isValid" => 1,
            "anesthesiologist_id" => 1
        ]);
        $ResidentCOption1CCCCT = DB::table("option")->insertGetId([
            "date" => $date,
            "resident" => 300,
            "schedule" => 143912,
            "attending" => 349746,
            "option" => 1,
            "isValid" => 1,
            "anesthesiologist_id" => 1
        ]);
        // update total for residents needed for correct assignment
        DB::table('probability')->where('resident', 113)->update(['total' => 0]);
        DB::table('probability')->where('resident', 270)->update(['total' => 0]);
        DB::table('probability')->where('resident', 300)->update(['total' => 0]);
        // assignment method call
        $autoAssignment = new AutoAssignment();
        $autoAssignment->assignment($date);
        // bools and values for assignments existing and current total for residents
        $foundAssignmentForA = DB::table('assignment')
                            ->where('date', $date)
                            ->where('resident', 113)
                            ->where('schedule', 143905)
                            ->where('anesthesiologist_id', 1)
                            ->exists();
        $ProbTotalResidentA = DB::table('probability')
                            ->where('resident', 113)
                            ->value('total');
        $foundAssignmentForBWithAnest =DB::table('assignment')
                            ->where('date', $date)
                            ->where('resident', 270)
                            ->where('schedule', 143909)
                            ->where('anesthesiologist_id', 1)
                            ->exists();
        $foundAssignmentForBWithoutAnest =DB::table('assignment')
                            ->where('date', $date)
                            ->where('resident', 270)
                            ->where('schedule', 143909)
                            ->where('anesthesiologist_id', NULL)
                            ->exists();
        $ProbTotalResidentB = DB::table('probability')
                            ->where('resident', 270)
                            ->value('total');
        $foundAssignmentForCWithAnest =DB::table('assignment')
                            ->where('date', $date)
                            ->where('resident',300)
                            ->where('schedule', 143912)
                            ->where('anesthesiologist_id', 1)
                            ->exists();
        $foundAssignmentForCWithoutAnest =DB::table('assignment')
                            ->where('date', $date)
                            ->where('resident',300)
                            ->where('schedule', 143912)
                            ->where('anesthesiologist_id', NULL)
                            ->exists();
        $ProbTotalResidentC = DB::table('probability')
                            ->where('resident', 300)
                            ->value('total');
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
        DB::table('option')
            ->where('id', $ResidentAOption1CCCT)
            ->delete();
        DB::table('option')
            ->where('id', $ResidentBOption1CCCT)
            ->delete();
        DB::table('option')
            ->where('id', $ResidentCOption1CCCCT)
            ->delete();
        // delete dummy assignments
        DB::table('assignment')
            ->where('date', $date)
            ->where('resident', 113)
            ->where('schedule', 143905)
            ->where('anesthesiologist_id', 1)
            ->delete();
        if($bGotAnest){
            DB::table('assignment')
                ->where('date', $date)
                ->where('resident', 270)
                ->where('schedule', 143909)
                ->where('anesthesiologist_id', 1)
                ->delete();
            DB::table('assignment')
                ->where('date', $date)
                ->where('resident', 300)
                ->where('schedule', 143912)
                ->where('anesthesiologist_id', NULL)
                ->delete();
        } else{
             DB::table('assignment')
                ->where('date', $date)
                ->where('resident', 270)
                ->where('schedule', 143909)
                ->where('anesthesiologist_id', NULL)
                ->delete();
            DB::table('assignment')
                ->where('date', $date)
                ->where('resident', 300)
                ->where('schedule', 143912)
                ->where('anesthesiologist_id', 1)
                ->delete();
        }
    }

    public function testAnestDoubleAssignedCCCTLeasingUHGivenCCCTAssignment() {
        $date = "2021-03-03";
        // create options for residents 
        $ResidentAOption1CCCT = DB::table("option")->insertGetId([
            "date" => $date,
            "resident" => 113,
            "schedule" => 143910,
            "attending" => 349746,
            "option" => 1,
            "isValid" => 1,
            "anesthesiologist_id" => 1
        ]);
        $ResidentBOption1CCCTLeasingUH = DB::table("option")->insertGetId([
            "date" => $date,
            "resident" => 270,
            "schedule" => 143924,
            "attending" => 349746,
            "option" => 1,
            "isValid" => 1,
            "anesthesiologist_id" => 1
        ]);
        $ResidentCOption1CCCCT = DB::table("option")->insertGetId([
            "date" => $date,
            "resident" => 300,
            "schedule" => 143910,
            "attending" => 349746,
            "option" => 1,
            "isValid" => 1,
            "anesthesiologist_id" => 1
        ]);
        $ResidentCOption2CCCCT = DB::table("option")->insertGetId([
            "date" => $date,
            "resident" => 300,
            "schedule" => 143914,
            "attending" => 349746,
            "option" => 2,
            "isValid" => 1,
            "anesthesiologist_id" => 1
        ]);
        // update total for residents needed for correct assignment
        DB::table('probability')->where('resident', 113)->update(['total' => 1]);
        DB::table('probability')->where('resident', 270)->update(['total' => 0]);
        DB::table('probability')->where('resident', 300)->update(['total' => 0]);
        // assignment method call
        $autoAssignment = new AutoAssignment();
        $autoAssignment->assignment($date);
        // bools and values for assignments existing and current total for residents
        $foundAssignmentForA = DB::table('assignment')
                            ->where('date', $date)
                            ->where('resident', 113)
                            ->where('schedule', 143910)
                            ->where('anesthesiologist_id', 1)
                            ->exists();
        $ProbTotalResidentA = DB::table('probability')
                            ->where('resident', 113)
                            ->value('total');
        $foundAssignmentForB =DB::table('assignment')
                            ->where('date', $date)
                            ->where('resident', 270)
                            ->where('schedule', 143924)
                            ->where('anesthesiologist_id', 1)
                            ->exists();
        $ProbTotalResidentB = DB::table('probability')
                            ->where('resident', 270)
                            ->value('total');
        $foundAssignmentForC =DB::table('assignment')
                            ->where('date', $date)
                            ->where('resident',300)
                            ->where('schedule', 143914)
                            ->where('anesthesiologist_id', NULL)
                            ->exists();
        $ProbTotalResidentC = DB::table('probability')
                            ->where('resident', 300)
                            ->value('total');
        // assertions for correct assignments                    
        $this->assertTrue($foundAssignmentForA);
        $this->assertTrue($foundAssignmentForB);
        $this->assertTrue($foundAssignmentForC);
        // assertions for correct total in probability table after assignment
        $this->assertEquals($ProbTotalResidentA, 1);
        $this->assertEquals($ProbTotalResidentB, 0);
        $this->assertEquals($ProbTotalResidentC, 3);
        // delete dummy data for option table
        DB::table('option')
            ->where('id', $ResidentAOption1CCCT)
            ->delete();
        DB::table('option')
            ->where('id', $ResidentBOption1CCCTLeasingUH)
            ->delete();
        DB::table('option')
            ->where('id', $ResidentCOption1CCCCT)
            ->delete();
        DB::table('option')
            ->where('id', $ResidentCOption2CCCCT)
            ->delete();
        // delete dummy assignments
        DB::table('assignment')
            ->where('date', $date)
            ->where('resident', 113)
            ->where('schedule', 143910)
            ->where('anesthesiologist_id', 1)
            ->delete();
        DB::table('assignment')
                ->where('date', $date)
                ->where('resident', 270)
                ->where('schedule', 143924)
                ->where('anesthesiologist_id', 1)
                ->delete();
        DB::table('assignment')
                ->where('date', $date)
                ->where('resident', 300)
                ->where('schedule', 143914)
                ->where('anesthesiologist_id', NULL)
                ->delete();
    }
    
    public function testAnestDoubleAssignedCCCTGivenCCCTLeasingUHAssignment() {
        $date = "2021-03-03"; 
        // create options for residents
        $ResidentAOption1CCCTLeasingUH = DB::table("option")->insertGetId([
            "date" => $date,
            "resident" => 113,
            "schedule" => 143924,
            "attending" => 349746,
            "option" => 1,
            "isValid" => 1,
            "anesthesiologist_id" => 1
        ]);
        $ResidentBOption1CCCT = DB::table("option")->insertGetId([
            "date" => $date,
            "resident" => 270,
            "schedule" => 143910,
            "attending" => 349746,
            "option" => 1,
            "isValid" => 1,
            "anesthesiologist_id" => 1
        ]);
        $ResidentCOption1CCCCT = DB::table("option")->insertGetId([
            "date" => $date,
            "resident" => 300,
            "schedule" => 143910,
            "attending" => 349746,
            "option" => 1,
            "isValid" => 1,
            "anesthesiologist_id" => 1
        ]);
        $ResidentCOption2CCCCT = DB::table("option")->insertGetId([
            "date" => $date,
            "resident" => 300,
            "schedule" => 143914,
            "attending" => 349746,
            "option" => 2,
            "isValid" => 1,
            "anesthesiologist_id" => 1
        ]);
        // update total for residents needed for correct assignment
        DB::table('probability')->where('resident', 113)->update(['total' => 0]);
        DB::table('probability')->where('resident', 270)->update(['total' => 1]);
        DB::table('probability')->where('resident', 300)->update(['total' => 0]);
        // assignment method call
        $autoAssignment = new AutoAssignment();
        $autoAssignment->assignment($date);
        // bools and values for assignments existing and current total for residents
        $foundAssignmentForA = DB::table('assignment')
                            ->where('date', $date)
                            ->where('resident', 113)
                            ->where('schedule', 143924)
                            ->where('anesthesiologist_id', 1)
                            ->exists();
        $ProbTotalResidentA = DB::table('probability')
                            ->where('resident', 113)
                            ->value('total');
        $foundAssignmentForB =DB::table('assignment')
                            ->where('date', $date)
                            ->where('resident', 270)
                            ->where('schedule', 143910)
                            ->where('anesthesiologist_id', 1)
                            ->exists();
        $ProbTotalResidentB = DB::table('probability')
                            ->where('resident', 270)
                            ->value('total');
        $foundAssignmentForC =DB::table('assignment')
                            ->where('date', $date)
                            ->where('resident',300)
                            ->where('schedule', 143914)
                            ->where('anesthesiologist_id', NULL)
                            ->exists();
        $ProbTotalResidentC = DB::table('probability')
                            ->where('resident', 300)
                            ->value('total');
        // assertions for correct assignments                    
        $this->assertTrue($foundAssignmentForA);
        $this->assertTrue($foundAssignmentForB);
        $this->assertTrue($foundAssignmentForC);
        // assertions for correct total in probability table after assignment
        $this->assertEquals($ProbTotalResidentA, 0);
        $this->assertEquals($ProbTotalResidentB, 1);
        $this->assertEquals($ProbTotalResidentC, 3);
        // delete dummy data for option table
        DB::table('option')
            ->where('id', $ResidentAOption1CCCTLeasingUH)
            ->delete();
        DB::table('option')
            ->where('id', $ResidentBOption1CCCT)
            ->delete();
        DB::table('option')
            ->where('id', $ResidentCOption1CCCCT)
            ->delete();
        DB::table('option')
            ->where('id', $ResidentCOption2CCCCT)
            ->delete();
        // delete dummy assignments
        DB::table('assignment')
            ->where('date', $date)
            ->where('resident', 113)
            ->where('schedule', 143924)
            ->where('anesthesiologist_id', 1)
            ->delete();
        DB::table('assignment')
                ->where('date', $date)
                ->where('resident', 270)
                ->where('schedule', 143910)
                ->where('anesthesiologist_id', 1)
                ->delete();
        DB::table('assignment')
                ->where('date', $date)
                ->where('resident', 300)
                ->where('schedule', 143914)
                ->where('anesthesiologist_id', NULL)
                ->delete();
    }

    public function testAnestDoubleAssignedCCCTLeasingUHGivenUHAssignment() {
        $date = "2021-03-03"; 
        // create options for residents
        $ResidentAOption1UH = DB::table("option")->insertGetId([
            "date" => $date,
            "resident" => 113,
            "schedule" => 143905,
            "attending" => 349746,
            "option" => 1,
            "isValid" => 1,
            "anesthesiologist_id" => 1
        ]);
        $ResidentBOption1UH = DB::table("option")->insertGetId([
            "date" => $date,
            "resident" => 270,
            "schedule" => 143905,
            "attending" => 349746,
            "option" => 1,
            "isValid" => 1,
            "anesthesiologist_id" => 1
        ]);
        $ResidentBOption2CCCTLeasingUH = DB::table("option")->insertGetId([
            "date" => $date,
            "resident" => 270,
            "schedule" => 143924,
            "attending" => 349746,
            "option" => 2,
            "isValid" => 1,
            "anesthesiologist_id" => 1
        ]);
        $ResidentCOption1UH = DB::table("option")->insertGetId([
            "date" => $date,
            "resident" => 300,
            "schedule" => 143905,
            "attending" => 349746,
            "option" => 1,
            "isValid" => 1,
            "anesthesiologist_id" => 1
        ]);
        $ResidentCOption2CCCTLeasingUH = DB::table("option")->insertGetId([
            "date" => $date,
            "resident" => 300,
            "schedule" => 143924,
            "attending" => 349746,
            "option" => 2,
            "isValid" => 1,
            "anesthesiologist_id" => 1
        ]);
        $ResidentCOption3UH = DB::table("option")->insertGetId([
            "date" => $date,
            "resident" => 300,
            "schedule" => 143909,
            "attending" => 349746,
            "option" => 3,
            "isValid" => 1,
            "anesthesiologist_id" => 1
        ]);
        // update total for residents needed for correct assignment
        DB::table('probability')->where('resident', 113)->update(['total' => 2]);
        DB::table('probability')->where('resident', 270)->update(['total' => 1]);
        DB::table('probability')->where('resident', 300)->update(['total' => 0]);
        // assignment method call
        $autoAssignment = new AutoAssignment();
        $autoAssignment->assignment($date);
        // bools and values for assignments existing and current total for residents
        $foundAssignmentForA = DB::table('assignment')
                            ->where('date', $date)
                            ->where('resident', 113)
                            ->where('schedule', 143905)
                            ->where('anesthesiologist_id', 1)
                            ->exists();
        $ProbTotalResidentA = DB::table('probability')
                            ->where('resident', 113)
                            ->value('total');
        $foundAssignmentForB =DB::table('assignment')
                            ->where('date', $date)
                            ->where('resident', 270)
                            ->where('schedule', 143924)
                            ->where('anesthesiologist_id', NULL)
                            ->exists();
        $ProbTotalResidentB = DB::table('probability')
                            ->where('resident', 270)
                            ->value('total');
        $foundAssignmentForC =DB::table('assignment')
                            ->where('date', $date)
                            ->where('resident',300)
                            ->where('schedule', 143909)
                            ->where('anesthesiologist_id', 1)
                            ->exists();
        $ProbTotalResidentC = DB::table('probability')
                            ->where('resident', 300)
                            ->value('total');
        // assertions for correct assignments                    
        $this->assertTrue($foundAssignmentForA);
        $this->assertTrue($foundAssignmentForB);
        $this->assertTrue($foundAssignmentForC); 
        // assertions for correct total in probability table after assignment
        $this->assertEquals($ProbTotalResidentA, 2);
        $this->assertEquals($ProbTotalResidentB, 4);
        $this->assertEquals($ProbTotalResidentC, 4);
        // delete dummy data for option table
        DB::table('option')
            ->where('id', $ResidentAOption1UH)
            ->delete();
        DB::table('option')
            ->where('id', $ResidentBOption1UH)
            ->delete();
        DB::table('option')
            ->where('id', $ResidentBOption2CCCTLeasingUH)
            ->delete();
        DB::table('option')
            ->where('id', $ResidentCOption1UH)
            ->delete();
        DB::table('option')
            ->where('id', $ResidentCOption2CCCTLeasingUH)
            ->delete();
        DB::table('option')
            ->where('id', $ResidentCOption3UH)
            ->delete();
        // delete dummy assignments
        DB::table('assignment')
            ->where('date', $date)
            ->where('resident', 113)
            ->where('schedule', 143905)
            ->where('anesthesiologist_id', 1)
            ->delete();
        DB::table('assignment')
                ->where('date', $date)
                ->where('resident', 270)
                ->where('schedule', 143924)
                ->where('anesthesiologist_id', NULL)
                ->delete();
        DB::table('assignment')
                ->where('date', $date)
                ->where('resident', 300)
                ->where('schedule', 143909)
                ->where('anesthesiologist_id', 1)
                ->delete();
    }
  
    public function testAnestDoubleAssignedUHGivenCCCTLeasingUHAssignment() {
        $date = "2021-03-03"; 
        // create options for residents
        $ResidentAOption1CCCTLeasingUH = DB::table("option")->insertGetId([
            "date" => $date,
            "resident" => 113,
            "schedule" => 143924,
            "attending" => 349746,
            "option" => 1,
            "isValid" => 1,
            "anesthesiologist_id" => 1
        ]);
        $ResidentBOption1CCCTLeasingUH = DB::table("option")->insertGetId([
            "date" => $date,
            "resident" => 270,
            "schedule" => 143924,
            "attending" => 349746,
            "option" => 1,
            "isValid" => 1,
            "anesthesiologist_id" => 1
        ]);
        $ResidentBOption2UH = DB::table("option")->insertGetId([
            "date" => $date,
            "resident" => 270,
            "schedule" => 143905,
            "attending" => 349746,
            "option" => 2,
            "isValid" => 1,
            "anesthesiologist_id" => 1
        ]);
        $ResidentCOption1CCCTLeasingUH = DB::table("option")->insertGetId([
            "date" => $date,
            "resident" => 300,
            "schedule" => 143924,
            "attending" => 349746,
            "option" => 1,
            "isValid" => 1,
            "anesthesiologist_id" => 1
        ]);
        $ResidentCOption2CCCCT = DB::table("option")->insertGetId([
            "date" => $date,
            "resident" => 300,
            "schedule" => 143914,
            "attending" => 349746,
            "option" => 2,
            "isValid" => 1,
            "anesthesiologist_id" => 1
        ]);
        // update total for residents needed for correct assignment
        DB::table('probability')->where('resident', 113)->update(['total' => 1]);
        DB::table('probability')->where('resident', 270)->update(['total' => 0]);
        DB::table('probability')->where('resident', 300)->update(['total' => 0]);
        // assignment method call
        $autoAssignment = new AutoAssignment();
        $autoAssignment->assignment($date);
        // bools and values for assignments existing and current total for residents
        $foundAssignmentForA = DB::table('assignment')
                            ->where('date', $date)
                            ->where('resident', 113)
                            ->where('schedule', 143924)
                            ->where('anesthesiologist_id', 1)
                            ->exists();
        $ProbTotalResidentA = DB::table('probability')
                            ->where('resident', 113)
                            ->value('total');
        $foundAssignmentForB =DB::table('assignment')
                            ->where('date', $date)
                            ->where('resident', 270)
                            ->where('schedule', 143905)
                            ->where('anesthesiologist_id', NULL)
                            ->exists();
        $ProbTotalResidentB = DB::table('probability')
                            ->where('resident', 270)
                            ->value('total');
        $foundAssignmentForC =DB::table('assignment')
                            ->where('date', $date)
                            ->where('resident',300)
                            ->where('schedule', 143914)
                            ->where('anesthesiologist_id', 1)
                            ->exists();
        $ProbTotalResidentC = DB::table('probability')
                            ->where('resident', 300)
                            ->value('total');
        // assertions for correct assignments                    
        $this->assertTrue($foundAssignmentForA);
        $this->assertTrue($foundAssignmentForB);
        $this->assertTrue($foundAssignmentForC);
        // assertions for correct total in probability table after assignment
        $this->assertEquals($ProbTotalResidentA, 1);
        $this->assertEquals($ProbTotalResidentB, 3);
        $this->assertEquals($ProbTotalResidentC, 2);
        // delete dummy data for option table
        DB::table('option')
            ->where('id', $ResidentAOption1CCCTLeasingUH)
            ->delete();
        DB::table('option')
            ->where('id', $ResidentBOption1CCCTLeasingUH)
            ->delete();
        DB::table('option')
            ->where('id', $ResidentBOption2UH)
            ->delete();
        DB::table('option')
            ->where('id', $ResidentCOption1CCCTLeasingUH)
            ->delete();
        DB::table('option')
            ->where('id', $ResidentCOption2CCCCT)
            ->delete();
        // delete dummy assignments
        DB::table('assignment')
            ->where('date', $date)
            ->where('resident', 113)
            ->where('schedule', 143924)
            ->where('anesthesiologist_id', 1)
            ->delete();
        DB::table('assignment')
                ->where('date', $date)
                ->where('resident', 270)
                ->where('schedule', 143905)
                ->where('anesthesiologist_id', NULL)
                ->delete();
        DB::table('assignment')
                ->where('date', $date)
                ->where('resident', 300)
                ->where('schedule', 143914)
                ->where('anesthesiologist_id', 1)
                ->delete();
    }
    
}
