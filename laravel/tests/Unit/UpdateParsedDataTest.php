<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

use App\ScheduleParser;
use App\EvaluationParser;

class UpdateParsedDataTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testParsedAssignmentTableHasData()
    {
        $this->assertDatabaseHas('assignment',['id' => '1']);
    }
	
	public function testParsedEvaluationDataTableHasCorrectedNameData()
    {
        $this->assertDatabaseHas('evaluation_data',['resident' => 'Amy Baumann']);
    }
	
	public function testParsedEvaluationDataTableHasData()
    {
        $this->assertDatabaseHas('evaluation_data',['id' => '1']);
    }
	
	public function testParsedResidentTableHasData()
    {
        $this->assertDatabaseHas('resident',['id' => '1']);
    }
	
	
	public function testParsedResidentTableHasCorrectNameData()
    {
        $this->assertDatabaseHas('resident',['name' => 'Amy Baumann']);
    }
	
	
}
