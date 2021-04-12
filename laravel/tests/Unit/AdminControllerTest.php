<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Http\Controllers\AdminController;
use App\Models\Milestone;
use App\Models\Resident;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class AdminControllerTest extends TestCase
{
    use DatabaseTransactions;

    public function testAdminAddMilestone()
    {
        $AC = new AdminController();
        $AC->getUpdateMilestone("add", "true", null, "FakeAbbreviation", "FakeFullName", "FakeDetail");
        $this->assertDatabaseHas("milestone", ["detail" => "FakeDetail"]);
    }

    public function testAdminDeleteMilestone()
    {
        $AC = new AdminController();
        $AC->getUpdateMilestone("delete", "true", 198, "FakeAbbreviation", "FakeFullName", "FakeDetail");
        $this->assertDatabaseHas("milestone", ["exists" => "0"]);
    }

    public function testAddUserResidentTableHasData()
    {
        $AC = new AdminController();
        $AC->getUpdateUsers("addUser", "Resident", "fakeRes@fak.com", "true", "FakeName");
        $this->assertDatabaseHas("resident", ["email" => "fakeRes@fak.com"]);
    }

}