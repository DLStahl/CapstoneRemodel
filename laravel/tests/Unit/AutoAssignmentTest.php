<?php

namespace Tests\Unit;

use App\AutoAssignment;
use Tests\TestCase;
use App\Models\Option;
use App\Models\Assignment;
use App\Models\Probability;
use App\Models\ScheduleData;
use App\Models\Milestone;
use App\Models\Resident;

use Illuminate\Foundation\Testing\DatabaseTransactions;

class AutoAssignmentTest extends TestCase
{
    use DatabaseTransactions;

    public static $date = "2021-03-03";
    private $autoAssignment;
    private $CCCT10;
    private $CCCT14;
    private $CCCT11;
    private $EP05;
    private $UH19;
    private $UH2;
    private $UH16;
    private $CCCT19LeasingUH7;
    private $residentA;
    private $residentB;
    private $residentC;
    private $residentD;
    private $milestone_id;

    public function setUp(): void
    {
        parent::setUp();
        $rooms = ["CCCT 10", "CCCT 14", "CCCT 11", "EP 05", "UH 19", "UH 2", "UH 16", "CCCT 19 Leasing UH7"];
        foreach ($rooms as $room) {
            $this->{str_replace(" ", "", $room)} = ScheduleData::insertGetId([
                "date" => self::$date,
                "room" => $room,
            ]);
        }
        $residents = ["resident A", "resident B", "resident C", "resident D"];
        foreach ($residents as $resident) {
            $this->{str_replace(" ", "", $resident)} = Resident::insertGetId([
                "name" => $resident,
                "email" => $resident . "@email",
            ]);
            Probability::insert([
                "resident" => $this->{str_replace(" ", "", $resident)},
                "total" => 0,
                "selected" => 0,
                "probability" => 0,
            ]);
        }
        $this->autoAssignment = new AutoAssignment();
        $this->milestone_id = Milestone::first()->value("id");
    }

    public function addOptionsToDatabase($optionsDataArrays)
    {
        foreach ($optionsDataArrays as $optionDataArray) {
            Option::insert([
                "date" => self::$date,
                "resident" => $optionDataArray[0],
                "schedule" => $optionDataArray[1],
                "option" => $optionDataArray[2],
                "isValid" => 1,
                "anesthesiologist_id" => $optionDataArray[3],
                "milestones" => $optionDataArray[4],
                "attending" => 1,
            ]);
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
                ->exists();
            $allCorrect = $allCorrect && $assignmentExists;
        }
        return $allCorrect;
    }

    public function correctProbTotals($expectedTotals)
    {
        $allCorrect = true;
        foreach ($expectedTotals as $expectedTotal) {
            $total = Probability::where('resident', $expectedTotal[0])->value('total');
            $correctTotal = $total == $expectedTotal[1];
            $allCorrect = $allCorrect && $correctTotal;
        }
        return $allCorrect;
    }

    // Tests for ticketing given types assignments
    public function testPreferenceTicketingWithAnestsGranted()
    {
        $options = [
            [$this->residentA, $this->CCCT10, 1, 1, $this->milestone_id],
            [$this->residentB, $this->CCCT10, 1, 1, $this->milestone_id],
            [$this->residentB, $this->CCCT14, 2, 2, $this->milestone_id],
            [$this->residentC, $this->CCCT10, 1, 1, $this->milestone_id],
            [$this->residentC, $this->CCCT14, 2, 2, $this->milestone_id],
            [$this->residentC, $this->CCCT11, 3, 3, $this->milestone_id],
        ];
        $expectedAssignments = [
            [$this->residentA, $this->CCCT10, 1],
            [$this->residentB, $this->CCCT14, 2],
            [$this->residentC, $this->CCCT11, 3],
        ];
        $expectedProbTotals = [[$this->residentA, 2], [$this->residentB, 3], [$this->residentC, 4]];

        self::addOptionsToDatabase($options);
        Probability::where("resident", $this->residentA)->update(["total" => 2]);
        Probability::where("resident", $this->residentB)->update(["total" => 1]);
        Probability::where("resident", $this->residentC)->update(["total" => 0]);

        $this->autoAssignment->assignment(self::$date);

        $this->assertTrue(self::correctAssignments($expectedAssignments));
        $this->assertTrue(self::correctProbTotals($expectedProbTotals));
    }

    public function testPreferenceTicketingWithAnestsNotGranted()
    {
        $options = [
            [$this->residentA, $this->EP05, 1, 1, $this->milestone_id],
            [$this->residentB, $this->CCCT10, 1, 1, $this->milestone_id],
            [$this->residentC, $this->CCCT10, 1, 1, $this->milestone_id],
            [$this->residentC, $this->CCCT14, 2, 1, $this->milestone_id],
            [$this->residentD, $this->CCCT10, 1, 1, $this->milestone_id],
            [$this->residentD, $this->CCCT14, 2, 1, $this->milestone_id],
            [$this->residentD, $this->CCCT11, 3, 1, $this->milestone_id],
        ];
        $expectedAssignments = [
            [$this->residentA, $this->EP05, 1],
            [$this->residentB, $this->CCCT10, null],
            [$this->residentC, $this->CCCT14, null],
            [$this->residentD, $this->CCCT11, null],
        ];
        $expectedProbTotals = [
            [$this->residentA, 0],
            [$this->residentB, 3],
            [$this->residentC, 4],
            [$this->residentD, 5],
        ];

        self::addOptionsToDatabase($options);
        Probability::where("resident", $this->residentA)->update(["total" => 0]);
        Probability::where("resident", $this->residentB)->update(["total" => 2]);
        Probability::where("resident", $this->residentC)->update(["total" => 1]);
        Probability::where("resident", $this->residentD)->update(["total" => 0]);

        $this->autoAssignment->assignment(self::$date);

        $this->assertTrue(self::correctAssignments($expectedAssignments));
        $this->assertTrue(self::correctProbTotals($expectedProbTotals));
    }

    public function testPreferenceTicketingNoAnestsPreferences()
    {
        $options = [
            [$this->residentA, $this->CCCT10, 1, null, $this->milestone_id],
            [$this->residentB, $this->CCCT10, 1, null, $this->milestone_id],
            [$this->residentB, $this->CCCT14, 2, null, $this->milestone_id],
            [$this->residentC, $this->CCCT10, 1, null, $this->milestone_id],
            [$this->residentC, $this->CCCT14, 2, null, $this->milestone_id],
            [$this->residentC, $this->CCCT11, 3, null, $this->milestone_id],
        ];
        $expectedAssignments = [
            [$this->residentA, $this->CCCT10, null],
            [$this->residentB, $this->CCCT14, null],
            [$this->residentC, $this->CCCT11, null],
        ];
        $expectedProbTotals = [[$this->residentA, 2], [$this->residentB, 3], [$this->residentC, 4]];

        self::addOptionsToDatabase($options);
        Probability::where("resident", $this->residentA)->update(["total" => 2]);
        Probability::where("resident", $this->residentB)->update(["total" => 1]);
        Probability::where("resident", $this->residentC)->update(["total" => 0]);

        $this->autoAssignment->assignment(self::$date);

        $this->assertTrue(self::correctAssignments($expectedAssignments));
        $this->assertTrue(self::correctProbTotals($expectedProbTotals));
    }

    public function testPreferenceTicketingForUnassignedResidents()
    {
        $options = [
            [$this->residentA, $this->CCCT14, 1, 1, $this->milestone_id],
            [$this->residentB, $this->CCCT14, 1, 1, $this->milestone_id],
            [$this->residentC, $this->CCCT14, 1, 1, $this->milestone_id],
            [$this->residentC, $this->CCCT14, 2, 1, $this->milestone_id],
            [$this->residentD, $this->CCCT14, 1, 1, $this->milestone_id],
            [$this->residentD, $this->CCCT14, 2, 1, $this->milestone_id],
            [$this->residentD, $this->CCCT14, 3, 1, $this->milestone_id],
        ];
        $expectedAssignments = [[$this->residentA, $this->CCCT14, 1]];
        $expectedProbTotals = [
            [$this->residentA, 1],
            [$this->residentB, 6],
            [$this->residentC, 6],
            [$this->residentD, 6],
        ];

        self::addOptionsToDatabase($options);
        Probability::where("resident", $this->residentA)->update(["total" => 1]);
        Probability::where("resident", $this->residentB)->update(["total" => 0]);
        Probability::where("resident", $this->residentC)->update(["total" => 0]);
        Probability::where("resident", $this->residentD)->update(["total" => 0]);

        $this->autoAssignment->assignment(self::$date);

        $this->assertTrue(self::correctAssignments($expectedAssignments));
        $this->assertTrue(self::correctProbTotals($expectedProbTotals));
    }

    // Tests for Anesthesiologist Preference Assignment
    public function testAnestAssignedOnce()
    {
        $options = [
            [$this->residentA, $this->EP05, 1, 1, $this->milestone_id],
            [$this->residentB, $this->CCCT14, 1, 1, $this->milestone_id],
        ];
        $expectedAssignments = [[$this->residentA, $this->EP05, 1], [$this->residentB, $this->CCCT14, null]];
        $expectedProbTotals = [[$this->residentA, 0], [$this->residentB, 1]];

        self::addOptionsToDatabase($options);
        Probability::where("resident", $this->residentA)->update(["total" => 0]);
        Probability::where("resident", $this->residentB)->update(["total" => 0]);

        $this->autoAssignment->assignment(self::$date);

        $this->assertTrue(self::correctAssignments($expectedAssignments));
        $this->assertTrue(self::correctProbTotals($expectedProbTotals));
    }

    public function testAnestDoubleAssignedCCCT()
    {
        $options = [
            [$this->residentA, $this->CCCT10, 1, 1, $this->milestone_id],
            [$this->residentB, $this->CCCT14, 1, 1, $this->milestone_id],
            [$this->residentC, $this->CCCT11, 1, 1, $this->milestone_id],
        ];
        $expectedAssignments1 = [
            [$this->residentA, $this->CCCT10, 1],
            [$this->residentB, $this->CCCT14, 1],
            [$this->residentC, $this->CCCT11, null],
        ];
        $expectedProbTotals1 = [[$this->residentA, 0], [$this->residentB, 0], [$this->residentC, 1]];
        $expectedAssignments2 = [
            [$this->residentA, $this->CCCT10, 1],
            [$this->residentB, $this->CCCT14, null],
            [$this->residentC, $this->CCCT11, 1],
        ];
        $expectedProbTotals2 = [[$this->residentA, 0], [$this->residentB, 1], [$this->residentC, 0]];

        self::addOptionsToDatabase($options);
        Probability::where("resident", $this->residentA)->update(["total" => 0]);
        Probability::where("resident", $this->residentB)->update(["total" => 0]);
        Probability::where("resident", $this->residentC)->update(["total" => 0]);

        $this->autoAssignment->assignment(self::$date);

        $this->assertTrue(
            self::correctAssignments($expectedAssignments1) || self::correctAssignments($expectedAssignments2)
        );
        $this->assertTrue(
            self::correctProbTotals($expectedProbTotals1) || self::correctProbTotals($expectedProbTotals2)
        );
    }

    public function testAnestDoubleAssignedUH()
    {
        $options = [
            [$this->residentA, $this->UH19, 1, 1, $this->milestone_id],
            [$this->residentB, $this->UH2, 1, 1, $this->milestone_id],
            [$this->residentC, $this->UH16, 1, 1, $this->milestone_id],
        ];
        $expectedAssignments1 = [
            [$this->residentA, $this->UH19, 1],
            [$this->residentB, $this->UH2, 1],
            [$this->residentC, $this->UH16, null],
        ];
        $expectedProbTotals1 = [[$this->residentA, 0], [$this->residentB, 0], [$this->residentC, 1]];
        $expectedAssignments2 = [
            [$this->residentA, $this->UH19, 1],
            [$this->residentB, $this->UH2, null],
            [$this->residentC, $this->UH16, 1],
        ];
        $expectedProbTotals2 = [[$this->residentA, 0], [$this->residentB, 1], [$this->residentC, 0]];

        self::addOptionsToDatabase($options);
        Probability::where("resident", $this->residentA)->update(["total" => 0]);
        Probability::where("resident", $this->residentB)->update(["total" => 0]);
        Probability::where("resident", $this->residentC)->update(["total" => 0]);

        $this->autoAssignment->assignment(self::$date);

        $this->assertTrue(
            self::correctAssignments($expectedAssignments1) || self::correctAssignments($expectedAssignments2)
        );
        $this->assertTrue(
            self::correctProbTotals($expectedProbTotals1) || self::correctProbTotals($expectedProbTotals2)
        );
    }

    public function testAnestDoubleAssignedCCCTLeasingUHGivenCCCTAssignment()
    {
        $options = [
            [$this->residentA, $this->CCCT14, 1, 1, $this->milestone_id],
            [$this->residentB, $this->CCCT19LeasingUH7, 1, 1, $this->milestone_id],
            [$this->residentC, $this->CCCT14, 1, 1, $this->milestone_id],
            [$this->residentC, $this->CCCT11, 2, 1, $this->milestone_id],
        ];
        $expectedAssignments = [
            [$this->residentA, $this->CCCT14, 1],
            [$this->residentB, $this->CCCT19LeasingUH7, 1],
            [$this->residentC, $this->CCCT11, null],
        ];
        $expectedProbTotals = [[$this->residentA, 1], [$this->residentB, 0], [$this->residentC, 3]];

        self::addOptionsToDatabase($options);
        Probability::where("resident", $this->residentA)->update(["total" => 1]);
        Probability::where("resident", $this->residentB)->update(["total" => 0]);
        Probability::where("resident", $this->residentC)->update(["total" => 0]);

        $this->autoAssignment->assignment(self::$date);

        $this->assertTrue(self::correctAssignments($expectedAssignments));
        $this->assertTrue(self::correctProbTotals($expectedProbTotals));
    }

    public function testAnestDoubleAssignedCCCTGivenCCCTLeasingUHAssignment()
    {
        $options = [
            [$this->residentA, $this->CCCT19LeasingUH7, 1, 1, $this->milestone_id],
            [$this->residentB, $this->CCCT14, 1, 1, $this->milestone_id],
            [$this->residentC, $this->CCCT14, 1, 1, $this->milestone_id],
            [$this->residentC, $this->CCCT11, 2, 1, $this->milestone_id],
        ];
        $expectedAssignments = [
            [$this->residentA, $this->CCCT19LeasingUH7, 1],
            [$this->residentB, $this->CCCT14, 1],
            [$this->residentC, $this->CCCT11, null],
        ];
        $expectedProbTotals = [[$this->residentA, 0], [$this->residentB, 1], [$this->residentC, 3]];

        self::addOptionsToDatabase($options);
        Probability::where("resident", $this->residentA)->update(["total" => 0]);
        Probability::where("resident", $this->residentB)->update(["total" => 1]);
        Probability::where("resident", $this->residentC)->update(["total" => 0]);

        $this->autoAssignment->assignment(self::$date);

        $this->assertTrue(self::correctAssignments($expectedAssignments));
        $this->assertTrue(self::correctProbTotals($expectedProbTotals));
    }

    public function testAnestDoubleAssignedCCCTLeasingUHGivenUHAssignment()
    {
        $options = [
            [$this->residentA, $this->UH19, 1, 1, $this->milestone_id],
            [$this->residentB, $this->UH19, 1, 1, $this->milestone_id],
            [$this->residentB, $this->CCCT19LeasingUH7, 2, 1, $this->milestone_id],
            [$this->residentC, $this->UH19, 1, 1, $this->milestone_id],
            [$this->residentC, $this->CCCT19LeasingUH7, 2, 1, $this->milestone_id],
            [$this->residentC, $this->UH2, 3, 1, $this->milestone_id],
        ];
        $expectedAssignments = [
            [$this->residentA, $this->UH19, 1],
            [$this->residentB, $this->CCCT19LeasingUH7, null],
            [$this->residentC, $this->UH2, 1],
        ];
        $expectedProbTotals = [[$this->residentA, 2], [$this->residentB, 4], [$this->residentC, 4]];

        self::addOptionsToDatabase($options);
        Probability::where("resident", $this->residentA)->update(["total" => 2]);
        Probability::where("resident", $this->residentB)->update(["total" => 1]);
        Probability::where("resident", $this->residentC)->update(["total" => 0]);

        $this->autoAssignment->assignment(self::$date);

        $this->assertTrue(self::correctAssignments($expectedAssignments));
        $this->assertTrue(self::correctProbTotals($expectedProbTotals));
    }

    public function testAnestDoubleAssignedUHGivenCCCTLeasingUHAssignment()
    {
        $options = [
            [$this->residentA, $this->CCCT19LeasingUH7, 1, 1, $this->milestone_id],
            [$this->residentB, $this->CCCT19LeasingUH7, 1, 1, $this->milestone_id],
            [$this->residentB, $this->UH19, 2, 1, $this->milestone_id],
            [$this->residentC, $this->CCCT19LeasingUH7, 1, 1, $this->milestone_id],
            [$this->residentC, $this->CCCT11, 2, 1, $this->milestone_id],
        ];
        $expectedAssignments = [
            [$this->residentA, $this->CCCT19LeasingUH7, 1],
            [$this->residentB, $this->UH19, null],
            [$this->residentC, $this->CCCT11, 1],
        ];
        $expectedProbTotals = [[$this->residentA, 1], [$this->residentB, 3], [$this->residentC, 2]];

        self::addOptionsToDatabase($options);
        Probability::where("resident", $this->residentA)->update(["total" => 1]);
        Probability::where("resident", $this->residentB)->update(["total" => 0]);
        Probability::where("resident", $this->residentC)->update(["total" => 0]);

        $this->autoAssignment->assignment(self::$date);

        $this->assertTrue(self::correctAssignments($expectedAssignments));
        $this->assertTrue(self::correctProbTotals($expectedProbTotals));
    }
}
