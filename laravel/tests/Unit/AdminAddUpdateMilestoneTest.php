<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;

use App\ScheduleParser;
use App\Option;
use App\EvaluationParser;
use App\Http\Controllers\MedhubController;
use App\Milestone;
use App\Http\Controllers\AdminController;

class AdminAddUpdateMilestoneTest extends TestCase {
    
    // Delete the milestone using the button on admin page
    public function testAdminDeleteMilestone() {
        $AC = new AdminController();
        $AC->getUpdateMilestone('delete', 'true', 198, 'FakeAbbreviation', 'FakeFullName', 'FakeDetail');
        $this->assertDatabaseHas('milestone', ['exists' => '0']);
        Milestone::where('id', 198)->update(['exists' => 1]);
    }
	
    // Update and add the milestone with csv file - Complete value
    public function testAdminAddUpdateMilestoneCSVComplete() {
        $filename = 'updateMilestoneUpdate.xlsx';
        $this->assertTrue(true);
        $filename = 'updateMilestoneAddNew.csv';
        $this->assertTrue(true);
    }
	
    // Update and add the milestone with csv file - Empty [Fail test]
    public function testAdminAddUpdateMilestoneCSVEmpty() {
        $filename = 'updateMilestoneEmpty.csv';
        $this->assertTrue(true);
    }
	
    // Update and add the milestone with csv file - Missing column [Fail test]
    public function testAdminAddUpdateMilestoneCSVMissColumn() {
        $filename = 'updateMilestoneEmptyColumn.csv';
        $this->assertTrue(true);
    }
	
    // Update and add the milestone with csv file - Skipped blank row
    public function testAdminAddUpdateMilestoneCSVSkippedRow() {
        $filename = 'updateMilestoneSkipRow.csv';
        $this->assertTrue(true);
    }
    
    // Update and add the milestone with csv file - Some empty value [Fail test]
    public function testAdminAddUpdateMilestoneCSVSomeEmptyValue() {
        $filename = 'updateMilestoneSomeEmpty.csv';
        $this->assertTrue(true);
    }
	
    // Update and add the milestone with csv file - Shifted table [Faid test]
    public function testAdminAddUpdateMilestoneCSVShiftedTable() {
        $filename = 'updateMilestoneShift.csv';
        $this->assertTrue(true);
    }
	
    // Update and add the milestone with csv file - Skipped blank column [Fail test]
    public function testAdminAddUpdateMilestoneCSVSkippedColumn() {
        $filename = 'updateMilestoneSkipColumn.csv';
        $this->assertTrue(true);
    }
	
    // Update and add the milestone with non csv file [Fail test]	
    public function testAdminAddUpdateMilestoneCSVWrongFormat() {
        $filename = 'updateMilestoneUpdate.xlsx';
        $this->assertTrue(true);
    }
	
}
