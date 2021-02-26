<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

use App\Http\Controllers\ScheduleDataController;

class ScheduleDataControllerTestForScheduleTable extends TestCase
{
    /**
     * Test to check a certain user exists
     *
     */
    public function testScheduleDataTableHasCurrentProcedure()
    {
        $this->assertDatabaseHas("schedule_data", ["case_procedure" => "test"]);
    }

    public function testScheduleDataHasLeadSurgeon()
    {
        $this->assertDatabaseHas("schedule_data", ["lead_surgeon" => "test"]);
    }

    public function testScheduleDataHasStartTime()
    {
        $this->assertDatabaseHas("schedule_data", ["start_time" => "07:40:00"]);
    }

    public function testScheduleDataHasEndTime()
    {
        $this->assertDatabaseHas("schedule_data", ["end_time" => "15:11:00"]);
    }

    public function testScheduleDataHasID()
    {
        $this->assertDatabaseHas("schedule_data", ["id" => "22479"]);
    }

    public function testScheduleDataHasORRoom()
    {
        $this->assertDatabaseHas("schedule_data", [
            "location" => "OSU UH MAIN OR",
        ]);
    }

    public function testScheduleDataHasDate()
    {
        $this->assertDatabaseHas("schedule_data", ["date" => "2018-11-13"]);
    }

    public function testRoom()
    {
        $this->assertDatabaseHas("schedule_data", ["room" => "UH-16"]);
    }
}
