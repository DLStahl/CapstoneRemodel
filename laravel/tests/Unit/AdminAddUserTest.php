<?php

namespace Tests\Unit;

use Tests\TestCase;

use App\Http\Controllers\AdminController;
use App\Http\Controllers\MedhubController;
use App\Models\Resident;

class AdminAddUserTest extends TestCase
{
    /**
     * A test of add user on admin adds a user to the resident table
     *
     * @return void
     */
    public function testAddUserResidentTableHasData()
    {
        $AC = new AdminController();
        $AC->getUpdateUsers("addUser", "Resident", "fakeRes@fak.com", "true", "FakeName");
        $this->assertDatabaseHas("resident", ["email" => "fakeRes@fak.com"]);
        $fakeData = Resident::where("email", "fakeRes@fak.com")->first();
        $fakeData->delete();
    }

    public function testAddUserResidentTableHasCorrectNameData()
    {
        $this->assertDatabaseHas("resident", ["name" => "Amy Baumann"]);
    }

    public function testAddUserResidentTableHasCorrectIDData()
    {
        $this->assertDatabaseHas("resident", ["medhubId" => "113643"]);
    }

    /**
     * A test of the medhub api connection.
     *
     * @return void
     */
    public function testAddUserMedHubAPIConnection()
    {
        $MHC = new MedhubController();
        $testPOST = json_decode($MHC->testPOST()->getBody(), true);
        $response = $testPOST["response"];
        $this->assertTrue($response == "success");
    }

}
