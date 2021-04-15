<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Http\Controllers\MedhubController;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class MedhubControllerTest extends TestCase
{
    use DatabaseTransactions;

    public function testMedHubAPIConnection()
    {
        $MHC = new MedhubController();
        $testPOST = json_decode($MHC->testPOST()->getBody(), true);
        $response = $testPOST["response"];
        $this->assertTrue($response == "success");
    }

    public function testMedHubActiveResidentsPOST()
    {
        $MHC = new MedhubController();
        $usersArr = json_decode($MHC->activeResidentsPOST()->getBody(), true);
        $this->assertNotNull($usersArr);
    }

    public function testMedHubActiveFacultyPOST()
    {
        $MHC = new MedhubController();
        $usersArr = json_decode($MHC->activeFacultyPOST()->getBody(), true);
        $this->assertNotNull($usersArr);
    }

    public function testMedHubAcademicYearPOST()
    {
        $MHC = new MedhubController();
        $usersArr = json_decode($MHC->academicYearPOST()->getBody(), true);
        $this->assertNotNull($usersArr);
    }

    public function testMedHubAPIEvalForms()
    {
        $MHC = new MedhubController();
        $evalsFormsPOST = json_decode($MHC->evaluationFormsPOST()->getBody(), true);
        $this->assertTrue(sizeof($evalsFormsPOST) > 0);
    }

    public function testMedHubSchedulePOST()
    {
        $MHC = new MedhubController();
        $usersArr = json_decode($MHC->schedulePOST()->getBody(), true);
        $this->assertNotNull($usersArr);
    }

    public function testMedHubRotationPOST()
    {
        $MHC = new MedhubController();
        $usersArr = json_decode($MHC->rotationsPOST()->getBody(), true);
        $this->assertNotNull($usersArr);
    }
}
