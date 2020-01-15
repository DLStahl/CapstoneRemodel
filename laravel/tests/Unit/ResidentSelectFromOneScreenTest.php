<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

use App\Http\Controllers\ScheduleDataController;

class ResidentSelectFromOneScreen extends TestCase
{
	/**
	* Test to check a certain user exists
	*
	*/
	public function testResidentSelectHasScheduleData1()
	{
		$this->assertDatabaseHas('schedule_data', ['case_procedure' => 'Test 1']);
	}
	public function testResidentSelectHasScheduleData2()
	{
		$this->assertDatabaseHas('schedule_data', ['case_procedure' => 'Test 2']);
	}
	public function testResidentSelectHasScheduleData3()
	{
		$this->assertDatabaseHas('schedule_data', ['case_procedure' => 'Test 3']);
	}
}
