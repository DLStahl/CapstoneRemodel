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
    // TODO: move into Admin Controller Test file
    public function testAddUserResidentTableHasData()
    {
        $AC = new AdminController();
        $AC->getUpdateUsers("addUser", "Resident", "fakeRes@fak.com", "true", "FakeName");
        $this->assertDatabaseHas("resident", ["email" => "fakeRes@fak.com"]);
        $fakeData = Resident::where("email", "fakeRes@fak.com")->first();
        $fakeData->delete();
    }

}
