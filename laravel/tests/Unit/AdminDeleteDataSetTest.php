<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

use App\Milestone;
use App\Http\Controllers\AdminController;

class AdminDeleteDataSetTest extends TestCase
{
    /**
     * A test of delete milestone using the button on admin page
     *
     * @return void
     */
    public function testAdminDeleteDataSetDataTableHasData()
    {
        $AC = new AdminController();
        $AC->getUpdateMilestone(
            "delete",
            "true",
            198,
            "FakeAbbreviation",
            "FakeFullName",
            "FakeDetail"
        );
        $this->assertDatabaseHas("milestone", ["exists" => "0"]);
        Milestone::where("id", 198)->update(["exists" => 1]);
    }

    public function testAdminDeleteDataSetPost()
    {
        $ac = new AdminController();
        $response = $ac->postEditDB();
        $this->assertNotNull($response);
    }
}
