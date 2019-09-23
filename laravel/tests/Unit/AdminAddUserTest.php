<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

use App\ScheduleParser;
use App\EvaluationParser;
use App\Http\Controllers\MedhubController;

class AdminAddUserTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */

	public function testAddUserResidentTableHasData()
    {
        $this->assertDatabaseHas('resident',['id' => '1']);
    }

	public function testAddUserResidentTableHasCorrectNameData()
    {
        $this->assertDatabaseHas('resident',['name' => 'Amy Baumann']);
    }

	public function testAddUserResidentTableHasCorrectIDData()
    {
        $this->assertDatabaseHas('resident',['medhubId' => '113643']);
    }

	public function testAddUserMedHubAPIConnection()
    {
		$MHC = new MedhubController();
		$testPOST = json_decode($MHC->testPOST()->getBody(), true);
		$response = $testPOST['response'];
		$this->assertTrue($response == 'success');
    }

	// public function testAddUserFindPeople()
  //   {
	// 	$ep = new EvaluationParser(date("o", strtotime('today')).date("m", strtotime('today')).date("d", strtotime('today')), true);
	// 	$result = $ep->findPeopleOSU("Michael", "Bragalone");
	// 	$this->assertNotNull($result);
  //   }



}
