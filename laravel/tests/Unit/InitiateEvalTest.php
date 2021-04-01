<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

use GuzzleHttp\Client;
use App\Console\Commands\InitiateEval;

class InitiateEvalTest extends TestCase
{
    public function testMedHubAPIConnection()
    {
        $initiateEval = new InitiateEval();
        $testPOST = json_decode(
            $initiateEval->medhubPOST("info/test", json_encode(["programID" => 73]))->getBody(),
            true
        );
        // echo json_encode($testPOST);
        $response = $testPOST["response"];
        $this->assertTrue($response == "success");
    }

    // public function testMedHubInitResidentEvalAttendingPOST()
    //    {
    //        $initiateEval = new InitiateEval();

    //        // test with medhubPOSt
    //        // $initEvalPOST = json_decode($initiateEval->medhubPOST('evals/initiate',json_encode(array('evaluationID' => 1353, 'evaluation_type' => 2, 'evaluator_userID' => 114141, 'programID' => 73, 'evaluatee_userID' => 114709)))->getBody(), true);

    //        // $initEvalPOST = json_decode($response->getBody(), true);
    //        // echo json_encode($initEvalPOST)."\n";
    //        // $this->assertTrue($initEvalPOST['responseID'] != 0);

    //        // test with initResidentEvalAttendingPOST
    //        $initEvalPOST = json_decode($initiateEval->initResidentEvalAttendingPOST(114708,114709)->getBody(), true);
    //        echo json_encode($initEvalPOST);
    //        $this->assertTrue($initEvalPOST['responseID'] != 0);

    //    }

    // public function testMedHubInitAttendingEvalResidentPOST()
    // {
    //     $initiateEval = new InitiateEval();

    //     // test with initAttendingEvalResidentPOST
    //     $initEvalPOST = json_decode($initiateEval->initAttendingEvalResidentPOST(114708,114709, 580)->getBody(), true);
    //     echo json_encode($initEvalPOST);
    //     $this->assertTrue($initEvalPOST['responseID'] != 0);

    // }
}
