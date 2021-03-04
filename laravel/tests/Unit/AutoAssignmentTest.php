<?php

namespace Tests\Unit;

use App\AutoAssignment;
use Tests\TestCase;
use App\Option;
use App\Assignment;
use App\Probability;

class AutoAssignmentTest extends TestCase
{
    public static $date = "2021-03-03";
    public static $residentA = 113;
    public static $residentB = 270;
    public static $residentC = 300;
    public static $residentD = 881;
    public static $CCCT10 = 143907;
    public static $CCCT14 = 143910;
    public static $CCCT11 = 143914;
    public static $EP05 =143908;
    public static $UH19 = 143905;
    public static $UH2 = 143909;
    public static $UH16 = 143912;
    public static $CCCTLeasingUH = 143924;

    public function addOptionsToDatabase($optionsDataArrays) {
        $optionIds = array();
        foreach($optionsDataArrays as $optionDataArray){
            $optionId = Option::insertGetId([
                "date" =>  self::$date,
                "resident" => $optionDataArray[0],
                "schedule" => $optionDataArray[1],
                "attending" => 349746,
                "option" => $optionDataArray[2],
                "isValid" => 1,
                "anesthesiologist_id" => $optionDataArray[3]
            ]);
            array_push($optionIds, $optionId);
        }
        return $optionIds;
    }

    public function deleteOptionsInDatabase($options) {
        foreach($options as $option){
            Option::find($option)->delete();
        }  
    }

    public function deleteExpectedAssignments($expectedAssignments) {
        foreach($expectedAssignments as $expectedAssignment){
            Assignment::where('date', self::$date)
                ->where('resident', $expectedAssignment[0])
                ->where('schedule', $expectedAssignment[1])
                ->where('anesthesiologist_id', $expectedAssignment[2])
                ->delete();
        }
    }
    
    public function correctAssignments($expectedAssignments) {
        $allCorrect = true;
        foreach($expectedAssignments as $expectedAssignment){
            $assignmentExists = Assignment::where('date', self::$date)
                ->where('resident', $expectedAssignment[0])
                ->where('schedule', $expectedAssignment[1])
                ->where('anesthesiologist_id', $expectedAssignment[2])
                ->exists();
            $allCorrect = $allCorrect && $assignmentExists;
        }
        return $allCorrect;
    }

    public function correctProbTotals($expectedTotals){
        $allCorrect = true;
        foreach($expectedTotals as $expectedTotal){
            $total = Probability::where('resident', $expectedTotal[0])->value('total');
            $correctTotal = $total == $expectedTotal[1];
            $allCorrect = $allCorrect && $correctTotal;
        }
        return $allCorrect;
    }

    public function callAssignmentMethod() {
        $autoAssignment = new AutoAssignment();
        $autoAssignment->assignment(self::$date);
    }

    
    // Tests for ticketing given types assignments 
    
    public function testPreferenceTicketingWithAnestsGranted() {
        $options = [
            [self::$residentA, self::$CCCT10, 1, 1],
            [self::$residentB, self::$CCCT10, 1, 1],
            [self::$residentB, self::$CCCT14, 2, 2],
            [self::$residentC, self::$CCCT10, 1, 1],
            [self::$residentC, self::$CCCT14, 2, 2],
            [self::$residentC, self::$CCCT11, 3, 3]
        ];
        $expectedAssignments = [
            [self::$residentA, self::$CCCT10, 1],
            [self::$residentB, self::$CCCT14, 2],
            [self::$residentC, self::$CCCT11, 3]
        ];
        $expectedProbTotals = [
            [self::$residentA, 2],
            [self::$residentB, 3],
            [self::$residentC, 4]
        ];
        
        $optionIds = self::addOptionsToDatabase($options);
        Probability::where('resident', self::$residentA)->update(['total' => 2]);
        Probability::where('resident', self::$residentB)->update(['total' => 1]);
        Probability::where('resident', self::$residentC)->update(['total' => 0]);

        self::callAssignmentMethod();
       
        $this->assertTrue(self::correctAssignments($expectedAssignments));
        $this->assertTrue(self::correctProbTotals($expectedProbTotals));
     
        self::deleteOptionsInDatabase($optionIds);
        self::deleteExpectedAssignments($expectedAssignments);

    }

    public function testPreferenceTicketingWithAnestsNotGranted() {
        $options = [
            [self::$residentA, self::$EP05, 1, 1],
            [self::$residentB, self::$CCCT10, 1, 1],
            [self::$residentC, self::$CCCT10, 1, 1],
            [self::$residentC, self::$CCCT14, 2, 1],
            [self::$residentD, self::$CCCT10, 1, 1],
            [self::$residentD, self::$CCCT14, 2, 1],
            [self::$residentD, self::$CCCT11, 3, 1]
        ];
        $expectedAssignments = [
            [self::$residentA, self::$EP05, 1],
            [self::$residentB, self::$CCCT10, NULL],
            [self::$residentC, self::$CCCT14, NULL],
            [self::$residentD, self::$CCCT11, NULL]
        ];
        $expectedProbTotals = [
            [self::$residentA, 0],
            [self::$residentB, 3],
            [self::$residentC, 4],
            [self::$residentD, 5]
        ];

        $optionIds = self::addOptionsToDatabase($options);
        Probability::where('resident', self::$residentA)->update(['total' => 0]);
        Probability::where('resident', self::$residentB)->update(['total' => 2]);
        Probability::where('resident', self::$residentC)->update(['total' => 1]);
        Probability::where('resident', self::$residentD)->update(['total' => 0]);

        self::callAssignmentMethod();
                    
        $this->assertTrue(self::correctAssignments($expectedAssignments)); 
        $this->assertTrue(self::correctProbTotals($expectedProbTotals));

        self::deleteOptionsInDatabase($optionIds);
        self::deleteExpectedAssignments($expectedAssignments);
    }
    
    public function testPreferenceTicketingNoAnestsPreferences() {
        $options = [
            [self::$residentA, self::$CCCT10, 1, NULL],
            [self::$residentB, self::$CCCT10, 1, NULL],
            [self::$residentB, self::$CCCT14, 2, NULL],
            [self::$residentC, self::$CCCT10, 1, NULL],
            [self::$residentC, self::$CCCT14, 2, NULL],
            [self::$residentC, self::$CCCT11, 3, NULL]
        ];
        $expectedAssignments = [
            [self::$residentA, self::$CCCT10, NULL],
            [self::$residentB, self::$CCCT14, NULL],
            [self::$residentC, self::$CCCT11, NULL]
        ];
        $expectedProbTotals = [
            [self::$residentA, 2],
            [self::$residentB, 3],
            [self::$residentC, 4]
        ];

        $optionIds = self::addOptionsToDatabase($options);
        Probability::where('resident', self::$residentA)->update(['total' => 2]);
        Probability::where('resident', self::$residentB)->update(['total' => 1]);
        Probability::where('resident', self::$residentC)->update(['total' => 0]);

        self::callAssignmentMethod();     

        $this->assertTrue(self::correctAssignments($expectedAssignments)); 
        $this->assertTrue(self::correctProbTotals($expectedProbTotals));

        self::deleteOptionsInDatabase($optionIds);
        self::deleteExpectedAssignments($expectedAssignments);
    }

    public function testPreferenceTicketingForUnassignedResidents() {
        $options = [
            [self::$residentA, self::$CCCT14, 1, 1],
            [self::$residentB, self::$CCCT14, 1, 1],
            [self::$residentC, self::$CCCT14, 1, 1],
            [self::$residentC, self::$CCCT14, 2, 1],
            [self::$residentD, self::$CCCT14, 1, 1],
            [self::$residentD, self::$CCCT14, 2, 1],
            [self::$residentD, self::$CCCT14, 3, 1]
        ];
        $expectedAssignments = [
            [self::$residentA, self::$CCCT14, 1]
        ];
        $expectedProbTotals = [
            [self::$residentA, 1],
            [self::$residentB, 6],
            [self::$residentC, 6],
            [self::$residentD, 6]
        ];

        $optionIds = self::addOptionsToDatabase($options);
        Probability::where('resident', self::$residentA)->update(['total' => 1]);
        Probability::where('resident', self::$residentB)->update(['total' => 0]);
        Probability::where('resident', self::$residentC)->update(['total' => 0]);
        Probability::where('resident', self::$residentD)->update(['total' => 0]);

        self::callAssignmentMethod();
                          
        $this->assertTrue(self::correctAssignments($expectedAssignments)); 
        $this->assertTrue(self::correctProbTotals($expectedProbTotals));

        self::deleteOptionsInDatabase($optionIds);
        self::deleteExpectedAssignments($expectedAssignments); 
    }

    // Tests for Anesthesiologist Preference Assignment

    public function testAnestAssignedOnce() {
        $options = [
            [self::$residentA, self::$EP05, 1, 1],
            [self::$residentB, self::$CCCT14, 1, 1]
        ];
        $expectedAssignments = [
            [self::$residentA, self::$EP05, 1],
            [self::$residentB, self::$CCCT14, NULL]
        ];
        $expectedProbTotals = [
            [self::$residentA, 0],
            [self::$residentB, 1]
        ];

        $optionIds = self::addOptionsToDatabase($options);
        Probability::where('resident', self::$residentA)->update(['total' => 0]);
        Probability::where('resident', self::$residentB)->update(['total' => 0]);
 
        self::callAssignmentMethod();
                   
        $this->assertTrue(self::correctAssignments($expectedAssignments)); 
        $this->assertTrue(self::correctProbTotals($expectedProbTotals));
  
        self::deleteOptionsInDatabase($optionIds);
        self::deleteExpectedAssignments($expectedAssignments); 
    }

    public function testAnestDoubleAssignedCCCT() {
        $options = [
            [self::$residentA, self::$CCCT10, 1, 1],
            [self::$residentB, self::$CCCT14, 1, 1],
            [self::$residentC, self::$CCCT11, 1, 1]
        ];
        $expectedAssignments1 = [
            [self::$residentA, self::$CCCT10, 1],
            [self::$residentB, self::$CCCT14, 1],
            [self::$residentC, self::$CCCT11, NULL]
        ];
        $expectedProbTotals1 = [
            [self::$residentA, 0],
            [self::$residentB, 0],
            [self::$residentC, 1]
        ];
        $expectedAssignments2 = [
            [self::$residentA, self::$CCCT10, 1],
            [self::$residentB, self::$CCCT14, NULL],
            [self::$residentC, self::$CCCT11, 1]
        ];
        $expectedProbTotals2 = [
            [self::$residentA, 0],
            [self::$residentB, 1],
            [self::$residentC, 0]
        ];

        $optionIds = self::addOptionsToDatabase($options);
        Probability::where('resident', self::$residentA)->update(['total' => 0]);
        Probability::where('resident', self::$residentB)->update(['total' => 0]);
        Probability::where('resident', self::$residentC)->update(['total' => 0]);

        self::callAssignmentMethod();

        $bGotAnest = self::correctAssignments($expectedAssignments1);
        $cGotAnest = self::correctAssignments($expectedAssignments2);
        $this->assertTrue( $bGotAnest || $cGotAnest);

        if($bGotAnest){
            $this->assertTrue(self::correctProbTotals($expectedProbTotals1));
            self::deleteExpectedAssignments($expectedAssignments1);
        } else{
            $this->assertTrue(self::correctProbTotals($expectedProbTotals2));
            self::deleteExpectedAssignments($expectedAssignments2);
        }
        self::deleteOptionsInDatabase($optionIds);
    }

    public function testAnestDoubleAssignedUH() {
        $options = [
            [self::$residentA, self::$UH19, 1, 1],
            [self::$residentB, self::$UH2, 1, 1],
            [self::$residentC, self::$UH16, 1, 1]
        ];
        $expectedAssignments1 = [
            [self::$residentA, self::$UH19, 1],
            [self::$residentB, self::$UH2, 1],
            [self::$residentC, self::$UH16, NULL]
        ];
        $expectedProbTotals1 = [
            [self::$residentA, 0],
            [self::$residentB, 0],
            [self::$residentC, 1]
        ];
        $expectedAssignments2 = [
            [self::$residentA, self::$UH19, 1],
            [self::$residentB, self::$UH2, NULL],
            [self::$residentC, self::$UH16, 1]
        ];
        $expectedProbTotals2 = [
            [self::$residentA, 0],
            [self::$residentB, 1],
            [self::$residentC, 0]
        ];

        $optionIds = self::addOptionsToDatabase($options);
        Probability::where('resident', self::$residentA)->update(['total' => 0]);
        Probability::where('resident', self::$residentB)->update(['total' => 0]);
        Probability::where('resident', self::$residentC)->update(['total' => 0]);

        self::callAssignmentMethod();
                   
        $bGotAnest = self::correctAssignments($expectedAssignments1);
        $cGotAnest = self::correctAssignments($expectedAssignments2);
        $this->assertTrue( $bGotAnest || $cGotAnest);

        if($bGotAnest){
            $this->assertTrue(self::correctProbTotals($expectedProbTotals1));
            self::deleteExpectedAssignments($expectedAssignments1);
        } else{
            $this->assertTrue(self::correctProbTotals($expectedProbTotals2));
            self::deleteExpectedAssignments($expectedAssignments2);
        }
        self::deleteOptionsInDatabase($optionIds);
    }

    public function testAnestDoubleAssignedCCCTLeasingUHGivenCCCTAssignment() {
        $options = [
            [self::$residentA, self::$CCCT14, 1, 1],
            [self::$residentB, self::$CCCTLeasingUH, 1, 1],
            [self::$residentC, self::$CCCT14, 1, 1],
            [self::$residentC, self::$CCCT11, 2, 1]
        ];
        $expectedAssignments = [
            [self::$residentA, self::$CCCT14, 1],
            [self::$residentB, self::$CCCTLeasingUH, 1],
            [self::$residentC, self::$CCCT11, NULL]
        ];
        $expectedProbTotals = [
            [self::$residentA, 1],
            [self::$residentB, 0],
            [self::$residentC, 3]
        ];

        $optionIds = self::addOptionsToDatabase($options); 
        Probability::where('resident', self::$residentA)->update(['total' => 1]);
        Probability::where('resident', self::$residentB)->update(['total' => 0]);
        Probability::where('resident', self::$residentC)->update(['total' => 0]);

        self::callAssignmentMethod();
                  
        $this->assertTrue(self::correctAssignments($expectedAssignments)); 
        $this->assertTrue(self::correctProbTotals($expectedProbTotals));
        
        self::deleteOptionsInDatabase($optionIds);
        self::deleteExpectedAssignments($expectedAssignments);
    }
    
    public function testAnestDoubleAssignedCCCTGivenCCCTLeasingUHAssignment() {
        $options = [
            [self::$residentA, self::$CCCTLeasingUH, 1, 1],
            [self::$residentB, self::$CCCT14, 1, 1],
            [self::$residentC, self::$CCCT14, 1, 1],
            [self::$residentC, self::$CCCT11, 2, 1]
        ];
        $expectedAssignments = [
            [self::$residentA, self::$CCCTLeasingUH, 1],
            [self::$residentB, self::$CCCT14, 1],
            [self::$residentC, self::$CCCT11, NULL]
        ];
        $expectedProbTotals = [
            [self::$residentA, 0],
            [self::$residentB, 1],
            [self::$residentC, 3]
        ];

        $optionIds = self::addOptionsToDatabase($options);
        Probability::where('resident', self::$residentA)->update(['total' => 0]);
        Probability::where('resident', self::$residentB)->update(['total' => 1]);
        Probability::where('resident', self::$residentC)->update(['total' => 0]);
        self::callAssignmentMethod();
                 
        $this->assertTrue(self::correctAssignments($expectedAssignments)); 
        $this->assertTrue(self::correctProbTotals($expectedProbTotals));

        self::deleteOptionsInDatabase($optionIds);
        self::deleteExpectedAssignments($expectedAssignments);
    }

    public function testAnestDoubleAssignedCCCTLeasingUHGivenUHAssignment() {
        $options = [
            [self::$residentA, self::$UH19, 1, 1],
            [self::$residentB, self::$UH19, 1, 1],
            [self::$residentB, self::$CCCTLeasingUH, 2, 1],
            [self::$residentC, self::$UH19, 1, 1],
            [self::$residentC, self::$CCCTLeasingUH, 2, 1],
            [self::$residentC, self::$UH2, 3, 1]
        ];
        $expectedAssignments = [
            [self::$residentA, self::$UH19, 1],
            [self::$residentB, self::$CCCTLeasingUH, NULL],
            [self::$residentC, self::$UH2, 1]
        ];
        $expectedProbTotals = [
            [self::$residentA, 2],
            [self::$residentB, 4],
            [self::$residentC, 4]
        ];

        $optionIds = self::addOptionsToDatabase($options); 
        Probability::where('resident', self::$residentA)->update(['total' => 2]);
        Probability::where('resident', self::$residentB)->update(['total' => 1]);
        Probability::where('resident', self::$residentC)->update(['total' => 0]);
        
        self::callAssignmentMethod();
                    
        $this->assertTrue(self::correctAssignments($expectedAssignments));  
        $this->assertTrue(self::correctProbTotals($expectedProbTotals));
        
        self::deleteOptionsInDatabase($optionIds);
        self::deleteExpectedAssignments($expectedAssignments);
    }
  
    public function testAnestDoubleAssignedUHGivenCCCTLeasingUHAssignment() {
        $options = [
            [self::$residentA, self::$CCCTLeasingUH, 1, 1],
            [self::$residentB, self::$CCCTLeasingUH, 1, 1],
            [self::$residentB, self::$UH19, 2, 1],
            [self::$residentC, self::$CCCTLeasingUH, 1, 1],
            [self::$residentC, self::$CCCT11, 2, 1]
        ];
        $expectedAssignments = [
            [self::$residentA, self::$CCCTLeasingUH, 1],
            [self::$residentB, self::$UH19, NULL],
            [self::$residentC, self::$CCCT11, 1]
        ];
        $expectedProbTotals = [
            [self::$residentA, 1],
            [self::$residentB, 3],
            [self::$residentC, 2]
        ];

        $optionIds = self::addOptionsToDatabase($options); 
        Probability::where('resident', self::$residentA)->update(['total' => 1]);
        Probability::where('resident', self::$residentB)->update(['total' => 0]);
        Probability::where('resident', self::$residentC)->update(['total' => 0]);

        self::callAssignmentMethod();
                
        $this->assertTrue(self::correctAssignments($expectedAssignments)); 
        $this->assertTrue(self::correctProbTotals($expectedProbTotals));
        
        self::deleteOptionsInDatabase($optionIds);
        self::deleteExpectedAssignments($expectedAssignments);
    }
    
}
