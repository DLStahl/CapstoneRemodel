<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

use App\Http\Controllers\ScheduleDataController;

class ResidentSelectDay extends TestCase
{
	public function testEvaluationDataHasTestID()
	{
		$this->assertDatabaseHas('evaluation_data', ['id' => '9999']);
	}

	public function testEvaluationDataHasTestDate()
        {
		$this->assertDatabaseHas('evaluation_data',['date'=>'2019-04-08']);
        }

	public function testEvaluationDataHasTestLocation()
	{
		$this->assertDatabaseHas('evaluation_data', ['location'=>'test']);
	} 
	public function testEvaluationDataHasTestCreated_At()
	{
		$this->assertDatabaseHas('evaluation_data', ['created_at'=>'2019-04-05 05:30:00']);
	} 
	
	

	
}
