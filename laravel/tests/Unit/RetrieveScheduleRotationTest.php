<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Http\Controllers\MedhubController;

class RetrieveScheduleRotationTest extends TestCase
{
    //TODO: Move methods into Medhub Controller Testing file

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
