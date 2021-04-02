<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

use App\Http\Controllers\ScheduleDataController;

class ResidentFillsLearningObjectiveTest extends TestCase
{
    /**
     * Test to check a certain user exists
     *
     */
    public function testOptionTableHasLO1()
    {
        $this->assertDatabaseHas("option", ["objectives" => "Test 1"]);
    }

    public function testOptionTableHasLO2()
    {
        $this->assertDatabaseHas("option", ["objectives" => "Test 2"]);
    }

    public function testOptionTableHasLO3()
    {
        $this->assertDatabaseHas("option", ["objectives" => "Test 3"]);
    }
}
