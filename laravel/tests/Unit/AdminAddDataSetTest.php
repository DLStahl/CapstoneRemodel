<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

use App\Http\Controllers\AdminController;
use App\Milestone;

class AdminAddDataSetTest extends TestCase {
    
    /**
     * A test of an admin controller method which adds a data set to a data table
     * and checks to ensure the data is there. Data is then deleted after the check.
     *
     * @return void
     */
    public function testAdminAddDataSetDataTableHasData() {
        $AC = new AdminController();
        $AC->getUpdateMilestone('add', 'true', null, 'FakeAbbreviation', 'FakeFullName', 'FakeDetail');
        $this->assertDatabaseHas('milestone', ['detail' => 'FakeDetail']);
        $fakeData = Milestone::where('detail', 'FakeDetail')->first();
        $fakeData->delete();
    }	
}
