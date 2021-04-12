<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Console\Commands\InitiateEval;
use App\Models\EvaluateData;
use App\Models\Rotations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class InitiateEvalTest extends TestCase
{
    use DatabaseTransactions;

    public function insertEvalData($data){
        EvaluateData::insert([
            "date"=> $data[0],
            "location" => $data[1],
            "diagnosis" => "Test",
            "procedure" => "Test",
            "ASA" => "Test",
            "resident_id" => $data[2],
            "resident" => $data[3],
            "attending_id" => $data[4],
            "attending" => $data[5],
            "time_with_attending" => $data[6],
        ]);
    }
    
    public function insertAllEvalData($allData){
        foreach($allData as $data){
            self::insertEvalData($data);
        }
    }

    public function insertRotationData($data){
        Rotations::insert([
            "name" => $data[0],
            "level" => "1",
            "service" => $data[1],
            "site" => "test",
            "start" => $data[2],
            "end" => $data[3],
        ]);
    }

    public function insertAllRotationData($allData){
        foreach($allData as $data){
            self::insertRotationData($data);
        }
    }
    
    
    public function testMedHubAPIConnection()
    {
        $initiateEval = new InitiateEval();
        $testPOST = json_decode(
            $initiateEval->medhubPOST("info/test", json_encode(["programID" => 73]))->getBody(),
            true
        );
        $response = $testPOST["response"];
        $this->assertTrue($response == "success");
    }

    public function testGetResidentAndAttendingEvalData()
    {
        // insert mock data
        $initiateEval = new InitiateEval();
        $evalData = [
            ["2021-03-03", "TestL1", "1", "Resident A", "10", "Attending A", "10"],
            ["2021-03-03", "TestL2", "1", "Resident A", "10", "Attending A", "20"],
            ["2021-03-03", "TestL1", "2", "Resident B", "10", "Attending A", "30"],
            ["2021-03-03", "TestL4", "2", "Resident B", "20", "Attending B", "40"],
        ];
        $rotationData = [
            ["Resident A", "1", "2021-02-01", "2021-02-28"],
            ["Resident A", "2", "2021-03-01", "2021-03-31"],
            ["Resident A", "3", "2021-04-01", "2021-04-30"],
            ["Resident B", "1", "2020-02-01", "2020-02-28"],
            ["Resident B", "2", "2020-03-01", "2020-03-31"],
            ["Resident B", "3", "2020-04-01", "2020-04-30"],
        ];
        self::insertAllEvalData($evalData);
        self::insertAllRotationData($rotationData);

        $expectedResults = [
            ["Resident A", "1", "2", "10", "Attending A", "30"],
            ["Resident B", "2", 0, "10", "Attending A", "30"],
            ["Resident B",  "2", 0, "20", "Attending B", "40"],
        ];

        $results = $initiateEval->getResidentAndAttendingEvalData("2021-03-03");
        $this->assertEqualsCanonicalizing($expectedResults, $results);
    }

    // public function testSendEvaluationsToMedHub()
    // {
    //     // get yesterday's date
    //     $date = date("Y-m-d");
    //     $yesterday = strtotime("-1 day", strtotime($date));
    //     $yesterday = date("Y-m-d", $yesterday);

    //     // insert the mock data into the database
    //     $evaluationId = DB::table("evaluation_data")->insertGetId([
    //         "date" => $yesterday,
    //         "location" => "test",
    //         "diagnosis" => "test",
    //         "procedure" => "test",
    //         "ASA" => 5,
    //         "rId" => 306,
    //         "resident" => "Test Resident1",
    //         "aId" => 115350,
    //         "attending" => "testfaculty20",
    //     ]);
    //     $optionId = DB::table("option")->insertGetId([
    //         "date" => $yesterday,
    //         "resident" => 306,
    //         "schedule" => 1,
    //         "attending" => 115350,
    //         "option" => 1,
    //         "milestones" => 3,
    //         "objectives" => "test",
    //     ]);
    //     $rotationId = DB::table("rotations")->insertGetId([
    //         "Name" => "Test Resident1",
    //         "Level" => 1,
    //         "Service" => 3,
    //         "Site" => "RD",
    //         "Start" => "2020-02-01",
    //         "End" => "2020-02-28",
    //     ]);

    //     // make the call to initiate the evaluations
    //     $this->artisan("initiateEvals");

    //     // delete the mock data from the database
    //     DB::table("evaluation_data")
    //         ->where("id", $evaluationId)
    //         ->delete();
    //     DB::table("option")
    //         ->where("id", $optionId)
    //         ->delete();
    //     DB::table("rotations")
    //         ->where("ID", $rotationId)
    //         ->delete();

    //     // if we got this far, nothing broke so it passes
    //     $this->assertTrue(true);
    // }
}