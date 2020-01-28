<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

use App\ScheduleParser;
use App\EvaluationParser;
use App\Http\Controllers\MedhubController;
use App\Http\Controllers\AdminController;
use App\Resident;

class AdminAddUserTest extends TestCase {
    public function testAddUserResidentTableHasData() {
        $AC = new AdminController();
        $AC->getUpdateUsers('addUser', 'Admin', 'fake@fak.com', 'true', 'FakeName');
        $this->assertDatabaseHas('resident',['email' => 'fake@fak.com']);
        $fakeData = Resident::where('email', 'fake@fak.com')->first();
		$fakeData->delete();
    }

	public function testAddUserMedHubAPIConnection() {
		$MHC = new MedhubController();
		$testPOST = json_decode($MHC->testPOST()->getBody(), true);
		$response = $testPOST['response'];
		$this->assertTrue($response == 'success');
    }
}
