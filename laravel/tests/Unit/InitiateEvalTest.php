<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Console\Commands\InitiateEval;
use App\Models\EvaluateData;
use App\Models\Rotations;
use App\Models\Attending;
use App\Models\Resident;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class InitiateEvalTest extends TestCase
{
    use DatabaseTransactions;

    public function insertEvalData($data)
    {
        EvaluateData::insert([
            "date" => $data[0],
            "location" => $data[1],
            "diagnosis" => "Test",
            "procedure" => "Test",
            "ASA" => "Test",
            "rId" => $data[2],
            "resident" => $data[3],
            "aId" => $data[4],
            "attending" => $data[5],
            "diff" => $data[6],
        ]);
    }

    public function insertAllEvalData($allData)
    {
        foreach ($allData as $data) {
            self::insertEvalData($data);
        }
    }
    public function addResident($resident)
    {
        return Resident::insertGetId([
            "name" => $resident[0],
            "email" => $resident[1],
        ]);
    }

    public function addAllResidents($residents)
    {
        $ids = [];
        foreach ($residents as $resident) {
            $id = self::addResident($resident);
            array_push($ids, $id);
        }
        return $ids;
    }

    public function addAllAttendings($attendings)
    {
        foreach ($attendings as $attending) {
            Attending::insert([
                "id" => $attending[0],
                "name" => $attending[1],
            ]);
        }
    }

    public function insertRotationData($data)
    {
        Rotations::insert([
            "name" => $data[0],
            "level" => "1",
            "service" => $data[1],
            "site" => "test",
            "start" => $data[2],
            "end" => $data[3],
        ]);
    }

    public function insertAllRotationData($allData)
    {
        foreach ($allData as $data) {
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
        $residents = [["Resident A", "test1@email"], ["Resident B", "test2@email"]];
        $residentIds = $this->addAllResidents($residents);
        $attendings = [["1", "Attending A"], ["2", "Attending B"]];
        self::addAllAttendings($attendings);
        $initiateEval = new InitiateEval();
        $evalData = [
            ["2021-03-03", "TestL1", $residentIds[0], "Resident A", "1", "Attending A", "10"],
            ["2021-03-03", "TestL2", $residentIds[0], "Resident A", "1", "Attending A", "20"],
            ["2021-03-03", "TestL1", $residentIds[1], "Resident B", "1", "Attending A", "30"],
            ["2021-03-03", "TestL4", $residentIds[1], "Resident B", "2", "Attending B", "40"],
        ];
        $rotationData = [
            ["Resident A", 1, "2021-02-01", "2021-02-28"],
            ["Resident A", 2, "2021-03-01", "2021-03-31"],
            ["Resident A", 3, "2021-04-01", "2021-04-30"],
            ["Resident B", 1, "2020-02-01", "2020-02-28"],
            ["Resident B", 2, "2020-03-01", "2020-03-31"],
            ["Resident B", 3, "2020-04-01", "2020-04-30"],
        ];
        self::insertAllEvalData($evalData);
        self::insertAllRotationData($rotationData);

        $expectedResults = [
            ["Resident A", strval($residentIds[0]), "2", "1", "Attending A", "30"],
            ["Resident B", strval($residentIds[1]), 0, "1", "Attending A", "30"],
            ["Resident B", strval($residentIds[1]), 0, "2", "Attending B", "40"],
        ];

        $results = $initiateEval->getResidentAndAttendingEvalData("2021-03-03");
        $this->assertEqualsCanonicalizing($expectedResults, $results);
    }
}
