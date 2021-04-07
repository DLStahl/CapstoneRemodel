<?php

namespace Tests\Unit;

use App\AutoAssignment;
use Tests\TestCase;
use App\Models\Option;
use App\Models\Assignment;
use App\Models\Probability;
 // TODO: update mocking data and use refresh db
class AutoAssignmentTest extends TestCase
{
    public static $date = "2021-03-03";
    public static $residentA = 113;
    public static $residentB = 270;
    public static $residentC = 300;
    public static $residentD = 881;
    public static $CCCT10 = 143907;
    public static $CCCT10Attending = "051011";
    public static $CCCT14 = 143910;
    public static $CCCT14Attending = "033548";
    public static $CCCT11 = 143914;
    public static $CCCT11Attending = "368563";
    public static $EP05 = 143908;
    public static $EP05Attending = "745117";
    public static $UH19 = 143905;
    public static $UH19Attending = "363283";
    public static $UH2 = 143909;
    public static $UH2Attending = "420596";
    public static $UH16 = 143912;
    public static $UH16Attending = "328666";
    public static $CCCTLeasingUH = 143924;
    public static $CCCTLeasingUHAttending = "035089";

    public function addOptionsToDatabase($optionsDataArrays)
    {
        $optionIds = [];
        foreach ($optionsDataArrays as $optionDataArray) {
            $optionId = Option::insertGetId([
                "date" => self::$date,
                "resident" => $optionDataArray[0],
                "schedule" => $optionDataArray[1],
                "attending" => 349746,
                "option" => $optionDataArray[2],
                "isValid" => 1,
                "anesthesiologist_id" => $optionDataArray[3],
            ]);
            array_push($optionIds, $optionId);
        }
        return $optionIds;
    }

    public function deleteOptionsInDatabase($options)
    {
        foreach ($options as $option) {
            Option::find($option)->delete();
        }
    }

    public function deleteExpectedAssignments($expectedAssignments)
    {
        foreach ($expectedAssignments as $expectedAssignment) {
            Assignment::where("date", self::$date)
                ->where("resident", $expectedAssignment[0])
                ->where("schedule", $expectedAssignment[1])
                ->where("anesthesiologist_id", $expectedAssignment[2])
                ->where("attending", $expectedAssignment[3])
                ->delete();
        }
    }

    public function correctAssignments($expectedAssignments)
    {
        $allCorrect = true;
        foreach ($expectedAssignments as $expectedAssignment) {
            $assignmentExists = Assignment::where("date", self::$date)
                ->where("resident", $expectedAssignment[0])
                ->where("schedule", $expectedAssignment[1])
                ->where("anesthesiologist_id", $expectedAssignment[2])
                ->where("attending", $expectedAssignment[3])
                ->exists();
            $allCorrect = $allCorrect && $assignmentExists;
        }
        return $allCorrect;
    }

    public function correctProbTotals($expectedTotals)
    {
        $allCorrect = true;
        foreach ($expectedTotals as $expectedTotal) {
            $total = Probability::where("resident", $expectedTotal[0])->value("total");
            $correctTotal = $total == $expectedTotal[1];
            $allCorrect = $allCorrect && $correctTotal;
        }
        return $allCorrect;
    }

    public function callAssignmentMethod()
    {
        $autoAssignment = new AutoAssignment();
        $autoAssignment->assignment(self::$date);
    }

    // Tests for ticketing given types assignments

    public function testPreferenceTicketingWithAnestsGranted()
    {
        $options = [
            [self::$residentA, self::$CCCT10, 1, 1],
            [self::$residentB, self::$CCCT10, 1, 1],
            [self::$residentB, self::$CCCT14, 2, 2],
            [self::$residentC, self::$CCCT10, 1, 1],
            [self::$residentC, self::$CCCT14, 2, 2],
            [self::$residentC, self::$CCCT11, 3, 3],
        ];
        $expectedAssignments = [
            [self::$residentA, self::$CCCT10, 1, self::$CCCT10Attending],
            [self::$residentB, self::$CCCT14, 2, self::$CCCT14Attending],
            [self::$residentC, self::$CCCT11, 3, self::$CCCT11Attending],
        ];
        $expectedProbTotals = [[self::$residentA, 2], [self::$residentB, 3], [self::$residentC, 4]];

        $optionIds = self::addOptionsToDatabase($options);
        Probability::where("resident", self::$residentA)->update(["total" => 2]);
        Probability::where("resident", self::$residentB)->update(["total" => 1]);
        Probability::where("resident", self::$residentC)->update(["total" => 0]);

        self::callAssignmentMethod();

        $correctAssignments = self::correctAssignments($expectedAssignments);
        $correctProbabilityTotals = self::correctProbTotals($expectedProbTotals);

        self::deleteOptionsInDatabase($optionIds);
        self::deleteExpectedAssignments($expectedAssignments);

        $this->assertTrue($correctAssignments);
        $this->assertTrue($correctProbabilityTotals);
    }

    public function testPreferenceTicketingWithAnestsNotGranted()
    {
        $options = [
            [self::$residentA, self::$EP05, 1, 1],
            [self::$residentB, self::$CCCT10, 1, 1],
            [self::$residentC, self::$CCCT10, 1, 1],
            [self::$residentC, self::$CCCT14, 2, 1],
            [self::$residentD, self::$CCCT10, 1, 1],
            [self::$residentD, self::$CCCT14, 2, 1],
            [self::$residentD, self::$CCCT11, 3, 1],
        ];
        $expectedAssignments = [
            [self::$residentA, self::$EP05, 1, self::$EP05Attending],
            [self::$residentB, self::$CCCT10, null, self::$CCCT10Attending],
            [self::$residentC, self::$CCCT14, null, self::$CCCT14Attending],
            [self::$residentD, self::$CCCT11, null, self::$CCCT11Attending],
        ];
        $expectedProbTotals = [
            [self::$residentA, 0],
            [self::$residentB, 3],
            [self::$residentC, 4],
            [self::$residentD, 5],
        ];

        $optionIds = self::addOptionsToDatabase($options);
        Probability::where("resident", self::$residentA)->update(["total" => 0]);
        Probability::where("resident", self::$residentB)->update(["total" => 2]);
        Probability::where("resident", self::$residentC)->update(["total" => 1]);
        Probability::where("resident", self::$residentD)->update(["total" => 0]);

        self::callAssignmentMethod();

        $correctAssignments = self::correctAssignments($expectedAssignments);
        $correctProbabilityTotals = self::correctProbTotals($expectedProbTotals);

        self::deleteOptionsInDatabase($optionIds);
        self::deleteExpectedAssignments($expectedAssignments);

        $this->assertTrue($correctAssignments);
        $this->assertTrue($correctProbabilityTotals);
    }

    public function testPreferenceTicketingNoAnestsPreferences()
    {
        $options = [
            [self::$residentA, self::$CCCT10, 1, null],
            [self::$residentB, self::$CCCT10, 1, null],
            [self::$residentB, self::$CCCT14, 2, null],
            [self::$residentC, self::$CCCT10, 1, null],
            [self::$residentC, self::$CCCT14, 2, null],
            [self::$residentC, self::$CCCT11, 3, null],
        ];
        $expectedAssignments = [
            [self::$residentA, self::$CCCT10, null, self::$CCCT10Attending],
            [self::$residentB, self::$CCCT14, null, self::$CCCT14Attending],
            [self::$residentC, self::$CCCT11, null, self::$CCCT11Attending],
        ];
        $expectedProbTotals = [[self::$residentA, 2], [self::$residentB, 3], [self::$residentC, 4]];

        $optionIds = self::addOptionsToDatabase($options);
        Probability::where("resident", self::$residentA)->update(["total" => 2]);
        Probability::where("resident", self::$residentB)->update(["total" => 1]);
        Probability::where("resident", self::$residentC)->update(["total" => 0]);

        self::callAssignmentMethod();

        $correctAssignments = self::correctAssignments($expectedAssignments);
        $correctProbabilityTotals = self::correctProbTotals($expectedProbTotals);

        self::deleteOptionsInDatabase($optionIds);
        self::deleteExpectedAssignments($expectedAssignments);

        $this->assertTrue($correctAssignments);
        $this->assertTrue($correctProbabilityTotals);
    }

    public function testPreferenceTicketingForUnassignedResidents()
    {
        $options = [
            [self::$residentA, self::$CCCT14, 1, 1],
            [self::$residentB, self::$CCCT14, 1, 1],
            [self::$residentC, self::$CCCT14, 1, 1],
            [self::$residentC, self::$CCCT14, 2, 1],
            [self::$residentD, self::$CCCT14, 1, 1],
            [self::$residentD, self::$CCCT14, 2, 1],
            [self::$residentD, self::$CCCT14, 3, 1],
        ];
        $expectedAssignments = [[self::$residentA, self::$CCCT14, 1, self::$CCCT14Attending]];
        $expectedProbTotals = [
            [self::$residentA, 1],
            [self::$residentB, 6],
            [self::$residentC, 6],
            [self::$residentD, 6],
        ];

        $optionIds = self::addOptionsToDatabase($options);
        Probability::where("resident", self::$residentA)->update(["total" => 1]);
        Probability::where("resident", self::$residentB)->update(["total" => 0]);
        Probability::where("resident", self::$residentC)->update(["total" => 0]);
        Probability::where("resident", self::$residentD)->update(["total" => 0]);

        self::callAssignmentMethod();

        $correctAssignments = self::correctAssignments($expectedAssignments);
        $correctProbabilityTotals = self::correctProbTotals($expectedProbTotals);

        self::deleteOptionsInDatabase($optionIds);
        self::deleteExpectedAssignments($expectedAssignments);

        $this->assertTrue($correctAssignments);
        $this->assertTrue($correctProbabilityTotals);
    }

    // Tests for Anesthesiologist Preference Assignment

    public function testAnestAssignedOnce()
    {
        $options = [[self::$residentA, self::$EP05, 1, 1], [self::$residentB, self::$CCCT14, 1, 1]];
        $expectedAssignments = [
            [self::$residentA, self::$EP05, 1, self::$EP05Attending],
            [self::$residentB, self::$CCCT14, null, self::$CCCT14Attending],
        ];
        $expectedProbTotals = [[self::$residentA, 0], [self::$residentB, 1]];

        $optionIds = self::addOptionsToDatabase($options);
        Probability::where("resident", self::$residentA)->update(["total" => 0]);
        Probability::where("resident", self::$residentB)->update(["total" => 0]);

        self::callAssignmentMethod();

        $correctAssignments = self::correctAssignments($expectedAssignments);
        $correctProbabilityTotals = self::correctProbTotals($expectedProbTotals);

        self::deleteOptionsInDatabase($optionIds);
        self::deleteExpectedAssignments($expectedAssignments);

        $this->assertTrue($correctAssignments);
        $this->assertTrue($correctProbabilityTotals);
    }

    public function testAnestDoubleAssignedCCCT()
    {
        $options = [
            [self::$residentA, self::$CCCT10, 1, 1],
            [self::$residentB, self::$CCCT14, 1, 1],
            [self::$residentC, self::$CCCT11, 1, 1],
        ];
        $expectedAssignments1 = [
            [self::$residentA, self::$CCCT10, 1, self::$CCCT10Attending],
            [self::$residentB, self::$CCCT14, 1, self::$CCCT14Attending],
            [self::$residentC, self::$CCCT11, null, self::$CCCT11Attending],
        ];
        $expectedProbTotals1 = [[self::$residentA, 0], [self::$residentB, 0], [self::$residentC, 1]];
        $expectedAssignments2 = [
            [self::$residentA, self::$CCCT10, 1, self::$CCCT10Attending],
            [self::$residentB, self::$CCCT14, null, self::$CCCT14Attending],
            [self::$residentC, self::$CCCT11, 1, self::$CCCT11Attending],
        ];
        $expectedProbTotals2 = [[self::$residentA, 0], [self::$residentB, 1], [self::$residentC, 0]];

        $optionIds = self::addOptionsToDatabase($options);
        Probability::where("resident", self::$residentA)->update(["total" => 0]);
        Probability::where("resident", self::$residentB)->update(["total" => 0]);
        Probability::where("resident", self::$residentC)->update(["total" => 0]);

        self::callAssignmentMethod();

        $bGotAnest = self::correctAssignments($expectedAssignments1);
        $cGotAnest = self::correctAssignments($expectedAssignments2);

        self::deleteOptionsInDatabase($optionIds);
        if ($bGotAnest) {
            self::deleteExpectedAssignments($expectedAssignments1);
            $this->assertTrue(self::correctProbTotals($expectedProbTotals1));
        } else {
            self::deleteExpectedAssignments($expectedAssignments2);
            $this->assertTrue(self::correctProbTotals($expectedProbTotals2));
        }
        $this->assertTrue($bGotAnest || $cGotAnest);
    }

    public function testAnestDoubleAssignedUH()
    {
        $options = [
            [self::$residentA, self::$UH19, 1, 1],
            [self::$residentB, self::$UH2, 1, 1],
            [self::$residentC, self::$UH16, 1, 1],
        ];
        $expectedAssignments1 = [
            [self::$residentA, self::$UH19, 1, self::$UH19Attending],
            [self::$residentB, self::$UH2, 1, self::$UH2Attending],
            [self::$residentC, self::$UH16, null, self::$UH16Attending],
        ];
        $expectedProbTotals1 = [[self::$residentA, 0], [self::$residentB, 0], [self::$residentC, 1]];
        $expectedAssignments2 = [
            [self::$residentA, self::$UH19, 1, self::$UH19Attending],
            [self::$residentB, self::$UH2, null, self::$UH2Attending],
            [self::$residentC, self::$UH16, 1, self::$UH16Attending],
        ];
        $expectedProbTotals2 = [[self::$residentA, 0], [self::$residentB, 1], [self::$residentC, 0]];

        $optionIds = self::addOptionsToDatabase($options);
        Probability::where("resident", self::$residentA)->update(["total" => 0]);
        Probability::where("resident", self::$residentB)->update(["total" => 0]);
        Probability::where("resident", self::$residentC)->update(["total" => 0]);

        self::callAssignmentMethod();

        $bGotAnest = self::correctAssignments($expectedAssignments1);
        $cGotAnest = self::correctAssignments($expectedAssignments2);

        self::deleteOptionsInDatabase($optionIds);
        if ($bGotAnest) {
            self::deleteExpectedAssignments($expectedAssignments1);
            $this->assertTrue(self::correctProbTotals($expectedProbTotals1));
        } else {
            self::deleteExpectedAssignments($expectedAssignments2);
            $this->assertTrue(self::correctProbTotals($expectedProbTotals2));
        }

        $this->assertTrue($bGotAnest || $cGotAnest);
    }

    public function testAnestDoubleAssignedCCCTLeasingUHGivenCCCTAssignment()
    {
        $options = [
            [self::$residentA, self::$CCCT14, 1, 1],
            [self::$residentB, self::$CCCTLeasingUH, 1, 1],
            [self::$residentC, self::$CCCT14, 1, 1],
            [self::$residentC, self::$CCCT11, 2, 1],
        ];
        $expectedAssignments = [
            [self::$residentA, self::$CCCT14, 1, self::$CCCT14Attending],
            [self::$residentB, self::$CCCTLeasingUH, 1, self::$CCCTLeasingUHAttending],
            [self::$residentC, self::$CCCT11, null, self::$CCCT11Attending],
        ];
        $expectedProbTotals = [[self::$residentA, 1], [self::$residentB, 0], [self::$residentC, 3]];

        $optionIds = self::addOptionsToDatabase($options);
        Probability::where("resident", self::$residentA)->update(["total" => 1]);
        Probability::where("resident", self::$residentB)->update(["total" => 0]);
        Probability::where("resident", self::$residentC)->update(["total" => 0]);

        self::callAssignmentMethod();

        $correctAssignments = self::correctAssignments($expectedAssignments);
        $correctProbabilityTotals = self::correctProbTotals($expectedProbTotals);

        self::deleteOptionsInDatabase($optionIds);
        self::deleteExpectedAssignments($expectedAssignments);

        $this->assertTrue($correctAssignments);
        $this->assertTrue($correctProbabilityTotals);
    }

    public function testAnestDoubleAssignedCCCTGivenCCCTLeasingUHAssignment()
    {
        $options = [
            [self::$residentA, self::$CCCTLeasingUH, 1, 1],
            [self::$residentB, self::$CCCT14, 1, 1],
            [self::$residentC, self::$CCCT14, 1, 1],
            [self::$residentC, self::$CCCT11, 2, 1],
        ];
        $expectedAssignments = [
            [self::$residentA, self::$CCCTLeasingUH, 1, self::$CCCTLeasingUHAttending],
            [self::$residentB, self::$CCCT14, 1, self::$CCCT14Attending],
            [self::$residentC, self::$CCCT11, null, self::$CCCT11Attending],
        ];
        $expectedProbTotals = [[self::$residentA, 0], [self::$residentB, 1], [self::$residentC, 3]];

        $optionIds = self::addOptionsToDatabase($options);
        Probability::where("resident", self::$residentA)->update(["total" => 0]);
        Probability::where("resident", self::$residentB)->update(["total" => 1]);
        Probability::where("resident", self::$residentC)->update(["total" => 0]);
        self::callAssignmentMethod();

        $correctAssignments = self::correctAssignments($expectedAssignments);
        $correctProbabilityTotals = self::correctProbTotals($expectedProbTotals);

        self::deleteOptionsInDatabase($optionIds);
        self::deleteExpectedAssignments($expectedAssignments);

        $this->assertTrue($correctAssignments);
        $this->assertTrue($correctProbabilityTotals);
    }

    public function testAnestDoubleAssignedCCCTLeasingUHGivenUHAssignment()
    {
        $options = [
            [self::$residentA, self::$UH19, 1, 1],
            [self::$residentB, self::$UH19, 1, 1],
            [self::$residentB, self::$CCCTLeasingUH, 2, 1],
            [self::$residentC, self::$UH19, 1, 1],
            [self::$residentC, self::$CCCTLeasingUH, 2, 1],
            [self::$residentC, self::$UH2, 3, 1],
        ];
        $expectedAssignments = [
            [self::$residentA, self::$UH19, 1, self::$UH19Attending],
            [self::$residentB, self::$CCCTLeasingUH, null, self::$CCCTLeasingUHAttending],
            [self::$residentC, self::$UH2, 1, self::$UH2Attending],
        ];
        $expectedProbTotals = [[self::$residentA, 2], [self::$residentB, 4], [self::$residentC, 4]];

        $optionIds = self::addOptionsToDatabase($options);
        Probability::where("resident", self::$residentA)->update(["total" => 2]);
        Probability::where("resident", self::$residentB)->update(["total" => 1]);
        Probability::where("resident", self::$residentC)->update(["total" => 0]);

        self::callAssignmentMethod();

        $correctAssignments = self::correctAssignments($expectedAssignments);
        $correctProbabilityTotals = self::correctProbTotals($expectedProbTotals);

        self::deleteOptionsInDatabase($optionIds);
        self::deleteExpectedAssignments($expectedAssignments);

        $this->assertTrue($correctAssignments);
        $this->assertTrue($correctProbabilityTotals);
    }

    public function testAnestDoubleAssignedUHGivenCCCTLeasingUHAssignment()
    {
        $options = [
            [self::$residentA, self::$CCCTLeasingUH, 1, 1],
            [self::$residentB, self::$CCCTLeasingUH, 1, 1],
            [self::$residentB, self::$UH19, 2, 1],
            [self::$residentC, self::$CCCTLeasingUH, 1, 1],
            [self::$residentC, self::$CCCT11, 2, 1],
        ];
        $expectedAssignments = [
            [self::$residentA, self::$CCCTLeasingUH, 1, self::$CCCTLeasingUHAttending],
            [self::$residentB, self::$UH19, null, self::$UH19Attending],
            [self::$residentC, self::$CCCT11, 1, self::$CCCT11Attending],
        ];
        $expectedProbTotals = [[self::$residentA, 1], [self::$residentB, 3], [self::$residentC, 2]];

        $optionIds = self::addOptionsToDatabase($options);
        Probability::where("resident", self::$residentA)->update(["total" => 1]);
        Probability::where("resident", self::$residentB)->update(["total" => 0]);
        Probability::where("resident", self::$residentC)->update(["total" => 0]);

        self::callAssignmentMethod();

        $correctAssignments = self::correctAssignments($expectedAssignments);
        $correctProbabilityTotals = self::correctProbTotals($expectedProbTotals);

        self::deleteOptionsInDatabase($optionIds);
        self::deleteExpectedAssignments($expectedAssignments);

        $this->assertTrue($correctAssignments);
        $this->assertTrue($correctProbabilityTotals);
    }
}
