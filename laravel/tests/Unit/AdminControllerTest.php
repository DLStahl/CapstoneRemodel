<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Http\Controllers\AdminController;
use App\Models\Milestone;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class AdminControllerTest extends TestCase
{
    use DatabaseTransactions;

    public function testAdminAddMilestone()
    {
        $AC = new AdminController();
        $AC->getUpdateMilestone("add", "true", null, "FakeAbbreviation", "FakeFullName", "FakeDetail");
        $this->assertDatabaseHas("milestone", [
            "category" => "FakeAbbreviation",
            "title" => "FakeFullName",
            "detail" => "FakeDetail",
            "exists" => "1",
        ]);
    }

    public function testAdminDeleteMilestone()
    {
        $milestoneId = Milestone::insertGetId([
            "category" => "test",
        ]);
        $AC = new AdminController();
        $AC->getUpdateMilestone("delete", "true", $milestoneId, "FakeAbbreviation", "FakeFullName", "FakeDetail");
        $this->assertDatabaseHas("milestone", ["exists" => "0"]);
    }

    public function testAddUserResidentTableHasData()
    {
        $AC = new AdminController();
        $AC->getUpdateUsers("addUser", "Resident", "fakeRes@fak.com", "true", "FakeName");
        $this->assertDatabaseHas("resident", [
            "name" => "FakeName",
            "email" => "fakeRes@fak.com"
        ]);
    }
}
