<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

use App\Http\Controllers\MedhubController;

class RetrieveScheduleRotation extends TestCase
{
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
     * A basic test to check the academicYearPOST call returns values
     *
     * @return void
     */
    public function testMedHubAcademicYearPOST()
    {
        $MHC = new MedhubController();
        $usersArr = json_decode($MHC->academicYearPOST()->getBody(), true);
        $this->assertNotNull($usersArr);
    }

    /**
     * A basic test to check the schedulePOST call returns values
     *
     * @return void
     */
    public function testMedHubSchedulePOST()
    {
        $MHC = new MedhubController();
        $usersArr = json_decode($MHC->schedulePOST()->getBody(), true);
        $this->assertNotNull($usersArr);
    }

    /**
     * A basic test to check the schedulePOST call returns values
     *
     * @return void
     */
    public function testMedHubRotationPOST()
    {
        $MHC = new MedhubController();
        $usersArr = json_decode($MHC->rotationsPOST()->getBody(), true);
        $this->assertNotNull($usersArr);
    }
}
