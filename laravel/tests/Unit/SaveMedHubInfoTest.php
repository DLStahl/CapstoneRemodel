<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Http\Controllers\MedhubController;

class SaveMedHubInfo extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testMedHubResidentSavedData()
    {
        $this->assertDatabaseHas("resident", ["medhubId" => "113643"]);
    }

    public function testMedHubAttendingSavedData()
    {
        $this->assertDatabaseHas("attending", ["id" => "109589"]);
    }

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

    /**
     * A basic test to check the active residents call returns values
     *
     * @return void
     */
    public function testMedHubActiveFacultyPOST()
    {
        $MHC = new MedhubController();
        $usersArr = json_decode($MHC->activeFacultyPOST()->getBody(), true);
        $this->assertNotNull($usersArr);
    }
}
