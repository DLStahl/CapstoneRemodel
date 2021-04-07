<?php

namespace Tests\Unit;

use Tests\TestCase;
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

}
