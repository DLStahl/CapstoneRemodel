<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

use App\Http\Controllers\ScheduleDataController;

class ResidentSelectMilestone extends TestCase
{
	/**
	* Test to check a certain user exists
	*
	*/
	public function testOptionTableHasMilestone1()
	{
		$this->assertDatabaseHas('option', ['milestones' => 'PBLI1']);
	}

	public function testOptionTableHasMilestone2()
        {
		$this->assertDatabaseHas('option',['milestones'=>'PBLI2']);
        }

	public function testOptionTableHasMilestone3()
	{
		$this->assertDatabaseHas('option', ['milestones'=>'PBLI3']);
	} 
	
	public function testOptionTableHasEntryFromTestResidentBragalone()
	{
		$this->assertDatabaseHas('option', ['resident'=>'115']);
	} 
	public function testOptionTableHasEntryFromTestResidentKader()
	{
		$this->assertDatabaseHas('option', ['resident'=>'113']);
	} 
	

	
	
}
