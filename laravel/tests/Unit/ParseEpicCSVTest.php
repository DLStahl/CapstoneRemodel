<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

use App\ScheduleParser;
use App\EvaluationParser;

class ParseEpicCSVTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testParseAssignmentTableHasData()
    {
        $this->assertDatabaseHas("assignment", ["id" => "1"]);
    }

    public function testParseEvaluationDataTableHasData()
    {
        $this->assertDatabaseHas("evaluation_data", ["id" => "113"]);
    }

    public function testParseResidentTableHasData()
    {
        $this->assertDatabaseHas("resident", ["id" => "1"]);
    }

    public function testEpicCSVReadSheet()
    {
        $file = fopen(
            "/usr/local/webs/remodel.anesthesiology_dev/evaluation/Resident_Evaluation_Report.20190228.csv",
            "r"
        );
        $csv = [];
        while (($line = fgetcsv($file)) !== false) {
            //$line is an array of the csv elements
            $csv[] = $line;
        }
        fclose($file);
        $this->assertNotNull($csv);
    }
}
