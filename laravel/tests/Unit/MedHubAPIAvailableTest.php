<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

use App\Http\Controllers\MedhubController;

class MedHubAPIAvailableTest extends TestCase
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
		$response = $testPOST['response'];
		$this->assertTrue($response == 'success');
    }

    public function testMedHubAPIEvalForms()
    {
        $MHC = new MedhubController(); 
        $evalsFormsPOST = json_decode($MHC->evaluationFormsPOST()->getBody(), true);
        $this->assertTrue(sizeof($evalsFormsPOST)>0);
    }
}
