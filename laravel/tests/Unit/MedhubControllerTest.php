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
        $response = "failure";
        try {
            $testPOST = json_decode($MHC->testPOST()->getBody(), true);
        $response = $testPOST["response"];
        } catch (\Exception $e) {
            echo "\nTESTING MedHub Controller test post: Exception caught" . $e->getCode() . " " . $e->getMessage();
        }
        
        $this->assertTrue($response == "success");
    }

    public function testMedHubActiveResidentsPOST()
    {
        $MHC = new MedhubController();
        try {
            $usersArr = json_decode($MHC->activeResidentsPOST()->getBody(), true);
        } catch(\Exception $e) {
            echo "\nTESTING MedHub Controller active residents post: Exception caught" . $e->getCode() . " " . $e->getMessage();
        }

        $this->assertNotNull($usersArr);
    }

    public function testMedHubActiveFacultyPOST()
    {
        $MHC = new MedhubController();
        try {
            $usersArr = json_decode($MHC->activeFacultyPOST()->getBody(), true);
        } catch(\Exception $e) {
            echo "\nTESTING MedHub Controller active faculty post: Exception caught" . $e->getCode() . " " . $e->getMessage();
        }
        
        $this->assertNotNull($usersArr);
    }

    public function testMedHubAcademicYearPOST()
    {
        $MHC = new MedhubController();
        try {
            $usersArr = json_decode($MHC->academicYearPOST()->getBody(), true);
        } catch (\Exception $e) {
            echo "\nTESTING MedHub Controller academic year post: Exception caught" . $e->getCode() . " " . $e->getMessage();
        }
        
        $this->assertNotNull($usersArr);
    }

    public function testMedHubAPIEvalForms()
    {
        $MHC = new MedhubController();
        try {
            $evalsForms = json_decode($MHC->evaluationFormsPOST()->getBody(), true);
        } catch (\Exception $e){
            echo "\nTESTING MedHub Controller evaluation forms post: Exception caught" . $e->getCode() . " " . $e->getMessage();
        }
        
        $this->assertTrue(sizeof($evalsForms) > 0);
    }

    public function testMedHubSchedulePOST()
    {
        $MHC = new MedhubController();
        try {
            $usersArr = json_decode($MHC->schedulePOST()->getBody(), true);
        } catch (\Exception $e) {
            echo "\nTESTING MedHub Controller schedule post: Exception caught" . $e->getCode() . " " . $e->getMessage();
        }
        
        $this->assertNotNull($usersArr);
    }

    public function testMedHubRotationPOST()
    {
        $MHC = new MedhubController();
        try {
            $usersArr = json_decode($MHC->rotationsPOST()->getBody(), true);
        } catch (\Exception $e) {
            echo "\nTESTING MedHub Controller rotations post: Exception caught" . $e->getCode() . " " . $e->getMessage();
        }
        
        $this->assertNotNull($usersArr);
    }
}
