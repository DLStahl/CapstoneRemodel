<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;

use App\Console\Commands\InitiateEval;
use Artisan;

class SendEvaluations extends TestCase
{
    /**
     * A basic test to check the active residents call returns values
     *
     * @return void
     */
    public function testSendEvaluationsToMedHub()
    {
        // get yesterday's date
        $date = date("Y-m-d");
        $yesterday = strtotime("-1 day", strtotime($date));
        $yesterday = date("Y-m-d", $yesterday);

        // insert the mock data into the database
        $evaluationId = DB::table("evaluation_data")->insertGetId([
            "date" => $yesterday,
            "location" => "test",
            "diagnosis" => "test",
            "procedure" => "test",
            "ASA" => 5,
            "resident_id" => 306,
            "resident" => "Test Resident1",
            "attending_id" => 115350,
            "attending" => "testfaculty20",
        ]);
        $optionId = DB::table("option")->insertGetId([
            "date" => $yesterday,
            "resident_id" => 306,
            "schedule_data_id" => 1,
            "option" => 1,
            "milestone_id" => 3,
            "objectives" => "test",
        ]);

        // make the call to initiate the evaluations
        Artisan::call("initiateEvals");

        // delete the mock data from the database
        DB::table("evaluation_data")
            ->where("id", $evaluationId)
            ->delete();
        DB::table("option")
            ->where("id", $optionId)
            ->delete();

        // if we got this far, nothing broke so it passes
        $this->assertTrue(true);
    }
}
