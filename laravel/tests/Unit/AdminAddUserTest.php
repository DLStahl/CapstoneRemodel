<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

use App\Http\Controllers\MedhubController;
use App\Http\Controllers\AdminController;
use App\Resident;

class AdminAddUserTest extends TestCase {
    /**
     * A test of add user on admin adds a user to the resident table
     *
     * @return void
     */
    public function testAddUserResidentTableHasData() {
        $AC = new AdminController();
        $AC->getUpdateUsers('addUser', 'Resident', 'fakeRes@fak.com', 'true', 'FakeName');
        $this->assertDatabaseHas('resident',['email' => 'fakeRes@fak.com']);
        $fakeData = Resident::where('email', 'fakeRes@fak.com')->first();
        $fakeData->delete();
    }

    /**
     * A test of the medhub api connection.
     *
     * @return void
     */
    public function testAddUserMedHubAPIConnection() {
        $MHC = new MedhubController();
        $testPOST = json_decode($MHC->testPOST()->getBody(), true);
        $response = $testPOST['response'];
        $this->assertTrue($response == 'success');
    }
}
