<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class MapRotationEvaluationTest extends TestCase
{
    /**
     * A basic test to check that the residents table has values
     *
     * @return void
     */
    public function testResidentTableHasData()
    {
        $this->assertDatabaseHas("resident", [
            "name" => "Priscilla Agbenyefia",
        ]);
    }
    /**
     * A basic test to check that the residents table has values
     *
     * @return void
     */
    public function testAttendingTableHasData()
    {
        $this->assertDatabaseHas("attending", ["name" => "David Stahl"]);
    }
    /**
     * A basic test to check that the residents table has values
     *
     * @return void
     */
    public function testEvalTableHasData()
    {
        $this->assertDatabaseHas("evaluation_data", [
            "resident" => "Adam Thomas",
        ]);
    }
}
