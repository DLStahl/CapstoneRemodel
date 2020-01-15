<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

use App\ScheduleParser;
use App\EvaluationParser;

class EvaluateGenerateTimeIntervals extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testAssignmentTableHasData()
    {
        $this->assertDatabaseHas('assignment',['id' => '1']);
    }
	
	public function testEvaluationDataTableHasData()
    {
        $this->assertDatabaseHas('evaluation_data',['id' => '71']);
    }
	
	public function testScheduleDataTableHasData()
    {
        $this->assertDatabaseHas('schedule_data',['id' => '15']);
    }
	
}
