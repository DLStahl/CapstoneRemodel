<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Http\Controllers\MedhubController;

class RetrieveResidentIDTest extends TestCase
{
    // TODO: Move tests to Medhub Controller Testing file
    /**
     * A basic test to check the connection to medhub api with TestPOST call
     *
     * @return void
     */
    public function testMedHubAPIConnection()
    {
        $MHC = new MedhubController();
        $testPOST = json_decode($MHC->testPOST()->getBody(), true);
        $response = $testPOST["response"];
        $this->assertTrue($response == "success");
    }

    /**
     * A basic test to check the active residents call returns values
     *
     * @return void
     */
    public function testMedHubActiveResidentsPOST()
    {
        $MHC = new MedhubController();
        $usersArr = json_decode($MHC->activeResidentsPOST()->getBody(), true);
        $this->assertNotNull($usersArr);
    }
}
