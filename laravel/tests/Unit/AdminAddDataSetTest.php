<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

use App\Http\Controllers\AdminController;
use App\Milestone;
use App\Announcements;
use Carbon\Carbon;

class AdminAddDataSetTest extends TestCase
{
    /**
     * A test of an admin controller method which adds a data set to a data table
     * and checks to ensure the data is there. Data is then deleted after the check.
     *
     * @return void
     */
    public function testAdminAddDataSetDataTableHasData()
    {
        $AC = new AdminController();
        $AC->getUpdateMilestone("add", "true", null, "FakeAbbreviation", "FakeFullName", "FakeDetail");
        $this->assertDatabaseHas("milestone", ["detail" => "FakeDetail"]);
        $fakeData = Milestone::where("detail", "FakeDetail")->first();
        $fakeData->delete();
    }

    public function testPostAnnouncement()
    {
        Announcements::insert([
            "message" => "testcaseMessage",
            "user_type" => 1,
            "user_id" => 1,
            "parent_message_id" => -1,
            "created_at" => Carbon::now(),
        ]);
        $this->assertDatabaseHas("announcements", [
            "message" => "testcaseMessage",
        ]);
        $fakeData = Announcements::where("message", "testcaseMessage")->first();
        $fakeData->delete();
    }

    public function testDeleteAnnouncement()
    {
        Announcements::insert([
            "message" => "testcaseMessage",
            "user_type" => 1,
            "user_id" => 1,
            "parent_message_id" => -1,
            "created_at" => Carbon::now(),
        ]);
        $this->assertDatabaseHas("announcements", [
            "message" => "testcaseMessage",
        ]);
        $fakeData = Announcements::where("message", "testcaseMessage")->first();
        $fakeData->delete();
    }

    public function testPostReply()
    {
        Announcements::insert([
            "message" => "testcaseMessage",
            "user_type" => 1,
            "user_id" => 1,
            "parent_message_id" => -1,
            "created_at" => Carbon::now(),
        ]);
        $this->assertDatabaseHas("announcements", [
            "message" => "testcaseMessage",
        ]);
        $fakeData = Announcements::where("message", "testcaseMessage")->first();
        $fakeData->delete();
    }

    public function testDeleteReply()
    {
        Announcements::insert([
            "message" => "testcaseMessage",
            "user_type" => 1,
            "user_id" => 1,
            "parent_message_id" => -1,
            "created_at" => Carbon::now(),
        ]);
        $this->assertDatabaseHas("announcements", [
            "message" => "testcaseMessage",
        ]);
        $fakeData = Announcements::where("message", "testcaseMessage")->first();
        $fakeData->delete();
    }
}
