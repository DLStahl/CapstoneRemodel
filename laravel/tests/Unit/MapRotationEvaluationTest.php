<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class MapRotationEvaluation extends TestCase
{
    /**
     * A basic test to check that the residents table has values
     *
     * @return void
     */
	 public function testResidentTableHasData()
    {
        $this->assertDatabaseHas('resident',['name' => 'Priscilla Agbenyefia']);
    }
	/**
     * A basic test to check that the residents table has values
     *
     * @return void
     */
	 public function testAttendingTableHasData()
    {
        $this->assertDatabaseHas('attending',['name' => 'David L Stahl']);
    }
	/**
     * A basic test to check that the residents table has values
     *
     * @return void
     */
	 public function testEvalTableHasData()
    {
        $this->assertDatabaseHas('evaluation_data',['resident' => 'Yousef Alghothani']);
    }
}
