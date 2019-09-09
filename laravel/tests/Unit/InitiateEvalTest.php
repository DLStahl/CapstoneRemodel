<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

use App\Http\Controllers\MedhubController;

class InitiateEval extends TestCase
{
    public function testMedHubAPIConnection()
    {
		$MHC = new MedhubController(); 
		$testPOST = json_decode($MHC->testPOST()->getBody(), true);
		$response = $testPOST['response'];
		$this->assertTrue($response == 'success');
    }
	
	// /**
     // * A basic test to check the active residents call returns values 
     // *
     // * @return void
     // */
    // public function testMedHubInitiateEvalResidentAttendingPOST()
    // {
		// $MHC = new MedhubController(); 
		// $usersArr = json_decode($MHC->initEvalResidentAttendingPOST(109589,114706)->getBody(), true);
		// $this->assertNotNull($usersArr);
    // }
	
	// /**
     // * A basic test to check the active residents call returns values 
     // *
     // * @return void
     // */
    // public function testMedHubInitiateEvalAttendingResidentPOST()
    // {
		// $MHC = new MedhubController(); 
		// $usersArr = json_decode($MHC->initEvalAttendingResidentPOST(114706,109589)->getBody(), true);
		// $this->assertNotNull($usersArr);
    // }
}
