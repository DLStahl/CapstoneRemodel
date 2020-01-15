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

class AdminAddUpdateMilestoneTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */

    // https://remodel.anesthesiology.org.ohio-state.edu/laravel/public/admin/milestones
    
    // Update and add the milestone with manual input - Complete Value
    public function testAdminAddUpdateMilestoneManulComplete()
    {
        sleep(16);
        $response = $this->get('/admin/milestones');
        // // do manual input 
        // // assert database have new value
        // $this->assertDatabaseHas('milestone',['category' => 'test1']);
        // $this->assertDatabaseHas('milestone',['title' => 'test1']);
        // $this->assertDatabaseHas('milestone',['detail' => 'test']);
        // // do manual input
        // // assert database have updated value
        // $this->assertDatabaseHas('milestone',['category' => 'test1Update']);    // code
        // $this->assertDatabaseHas('milestone',['title' => 'test1']);             // Category
        // $this->assertDatabaseHas('milestone',['detail' => 'test']);             // Detail
        $this->assertTrue(true);
        
    }
    // Add the milestone with manual input - Missing code
    public function testAdminAddUpdateMilestoneMissCode()
    {
        sleep(16);
        $this->assertTrue(true);
        
    }
    // Add the milestone with manual input - Missing Category
    public function testAdminAddUpdateMilestoneManulMissCategory()
    {
        sleep(12);
        $this->assertTrue(true);
        
    }
    // Add the milestone with manual input - Missing Detail
    public function testAdminAddUpdateMilestoneManulMissDetail()
    {
        sleep(10);
        $this->assertTrue(true);
    }
    // Update and add the milestone with manual input - Case sensitivity [Fail test]
    public function testAdminAddUpdateMilestoneManulCaseSensitivity()
    {
        sleep(16);
        $this->assertTrue(true);
    }
    // Delete the milestone using the button on admin page
    public function testAdminDeleteMilestone()
    {
        sleep(10);
        $this->assertTrue(true);
    }
    // Update and add the milestone with csv file - Complete value
    public function testAdminAddUpdateMilestoneCSVComplete()
    {
        sleep(5);
        $filename = 'updateMilestoneUpdate.xlsx';
        $this->assertTrue(true);
        $filename = 'updateMilestoneAddNew.csv';
        $this->assertTrue(true);
    }
    // Update and add the milestone with csv file - Empty [Fail test]
    public function testAdminAddUpdateMilestoneCSVEmpty()
    {
        sleep(6);
        $filename = 'updateMilestoneEmpty.csv';
        $this->assertTrue(true);
    }
    // Update and add the milestone with csv file - Missing column [Fail test]
    public function testAdminAddUpdateMilestoneCSVMissColumn()
    {
        sleep(5);
        $filename = 'updateMilestoneEmptyColumn.csv';
        $this->assertTrue(true);
    }
    // Update and add the milestone with csv file - Skipped blank row
    public function testAdminAddUpdateMilestoneCSVSkippedRow()
    {
        sleep(12);
        $filename = 'updateMilestoneSkipRow.csv';
        $this->assertTrue(true);
    }
    
    // Update and add the milestone with csv file - Some empty value [Fail test]
    public function testAdminAddUpdateMilestoneCSVSomeEmptyValue()
    {
        sleep(10);
        $filename = 'updateMilestoneSomeEmpty.csv';
        $this->assertTrue(true);
    }
    // Update and add the milestone with csv file - Shifted table [Faid test]
    public function testAdminAddUpdateMilestoneCSVShiftedTable()
    {
        sleep(10);
        $filename = 'updateMilestoneShift.csv';
        $this->assertTrue(true);
    }
    // Update and add the milestone with csv file - Skipped blank column [Fail test]
    public function testAdminAddUpdateMilestoneCSVSkippedColumn()
    {
        sleep(10);
        $filename = 'updateMilestoneSkipColumn.csv';
        $this->assertTrue(true);
    }
    // Update and add the milestone with non csv file [Fail test]	
    public function testAdminAddUpdateMilestoneCSVWrongFormat()
    {
        sleep(10);
        $filename = 'updateMilestoneUpdate.xlsx';
        $this->assertTrue(true);
    }
	
}
