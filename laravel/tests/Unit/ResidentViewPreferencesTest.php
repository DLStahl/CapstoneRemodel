<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

use App\Http\Controllers\ScheduleDataController;

class ResidentViewPreferences extends TestCase
{
	/**
	* Test to check a certain user exists
	*
	*/
	public function testOptionTableHasEntryFromTestResidentBragaloneWithPref()
	{
		$this->assertDatabaseHas('option', ['resident'=>'115']);
	} 
	public function testOptionTableHasEntryFromTestResidentKaderWithPref()
	{
		$this->assertDatabaseHas('option', ['resident'=>'113']);
	} 
	public function testAssignTableHasEntryFromTestResidentBragalone()
	{
		$this->assertDatabaseHas('assignment', ['resident'=>'107']);
	} 
	public function testAssignTableHasEntryFromTestResidentKader()
	{
		$this->assertDatabaseHas('assignment', ['resident'=>'43']);
	} 

	
}
