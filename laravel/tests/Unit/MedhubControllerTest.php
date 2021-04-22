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
        $response = 'failure';
        try {
            $testPOST = json_decode($MHC->testPOST()->getBody(), true);
            $response = $testPOST['response'];
        } catch (\Exception $e) {
            $this->fail('Medhub Controller test post: Exception caught:' . $e->getCode() . ' ' . $e->getMessage());
        }
        $this->assertEquals('success', $response);
    }

    public function testMedHubActiveResidentsPOST()
    {
        $expectedKeys = [
            'userID',
            'name_last',
            'name_first',
            'email',
            'username',
            'employeeID',
            'typeID',
            'level',
            'programID',
        ];
        $MHC = new MedhubController();
        try {
            $usersArr = json_decode($MHC->activeResidentsPOST()->getBody(), true);
            $this->assertEqualsCanonicalizing(array_keys($usersArr[0]), $expectedKeys);
        } catch (\Exception $e) {
            $this->fail(
                'MedHub Controller active residents post: Exception caught:' . $e->getCode() . ' ' . $e->getMessage()
            );
        }
    }

    public function testMedHubActiveFacultyPOST()
    {
        $expectedKeys = ['userID', 'name_last', 'name_first', 'email', 'username', 'employeeID'];
        $MHC = new MedhubController();
        try {
            $usersArr = json_decode($MHC->activeFacultyPOST()->getBody(), true);
            $this->assertEqualsCanonicalizing(array_keys($usersArr[0]), $expectedKeys);
        } catch (\Exception $e) {
            $this->fail(
                'MedHub Controller active faculty post: Exception caught:' . $e->getCode() . ' ' . $e->getMessage()
            );
        }
    }

    public function testMedHubAcademicYearPOST()
    {
        $expectedKeys = ['rotationsetID', 'start_date', 'end_date', 'rotationset_title'];
        $MHC = new MedhubController();
        try {
            $response = json_decode($MHC->academicYearPOST()->getBody(), true);
            $this->assertEqualsCanonicalizing(array_keys($response[0]), $expectedKeys);
        } catch (\Exception $e) {
            $this->fail(
                'MedHub Controller academic year post: Exception caught:' . $e->getCode() . ' ' . $e->getMessage()
            );
        }
    }

    public function testMedHubAPIEvalForms()
    {
        $expectedKeys = [
            'evaluationID',
            'evaluation_title',
            'introduction',
            'questions_count',
            'types',
            'can_remove',
            'remove_req_comment',
        ];
        $MHC = new MedhubController();
        try {
            $evalsForms = json_decode($MHC->evaluationFormsPOST()->getBody(), true);
            $this->assertEqualsCanonicalizing(array_keys($evalsForms[0]), $expectedKeys);
        } catch (\Exception $e) {
            $this->fail(
                'MedHub Controller evaluation forms post: Exception caught:' . $e->getCode() . ' ' . $e->getMessage()
            );
        }
    }

    public function testMedHubSchedulePOST()
    {
        $expectedKeys = ['scheduleID', 'schedule_name', 'levels'];
        $MHC = new MedhubController();
        try {
            $response = json_decode($MHC->schedulePOST()->getBody(), true);
            $this->assertEqualsCanonicalizing(array_keys($response[0]), $expectedKeys);
        } catch (\Exception $e) {
            $this->fail('MedHub Controller schedule post: Exception caught:' . $e->getCode() . ' ' . $e->getMessage());
        }
    }

    public function testMedHubRotationPOST()
    {
        $MHC = new MedhubController();
        $expectedKeys = ['rotationID', 'rotation_name', 'start_date', 'end_date'];
        try {
            $response = json_decode($MHC->rotationsPOST()->getBody(), true);
            $this->assertEqualsCanonicalizing(array_keys($response[0]), $expectedKeys);
        } catch (\Exception $e) {
            $this->fail('MedHub Controller rotations post: Exception caught:' . $e->getCode() . ' ' . $e->getMessage());
        }
    }
}
