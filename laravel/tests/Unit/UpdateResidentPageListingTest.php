<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

use App\ScheduleParser;
use App\EvaluationParser;

class UpdateResidentPageListingTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
	
	public function testListingResidentTableHasData()
    {
        $this->assertDatabaseHas('resident',['id' => '1']);
    }
	
	
	public function testListingResidentTableHasCorrectNameData()
    {
        $this->assertDatabaseHas('resident',['name' => 'Amy Baumann']);
    }
	
		public function testListingResidentTableHasCorrectIDData()
    {
        $this->assertDatabaseHas('resident',['UserID' => '114146']);
    }
	
	
}
