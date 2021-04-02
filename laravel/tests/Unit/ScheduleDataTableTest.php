<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

use App\Http\Controllers\ScheduleDataController;

class ScheduleDataTableTest extends TestCase
{
    public function testScheduleDataHasStartTime()
    {
        $this->assertDatabaseHas("schedule_data", ["start_time" => "07:40:00"]);
    }

    public function testScheduleDataHasORRoom()
    {
        $this->assertDatabaseHas("schedule_data", [
            "location" => "OSU UH MAIN OR",
        ]);
    }

    public function testRoom()
    {
        $this->assertDatabaseHas("schedule_data", ["room" => "UH-16"]);
    }
}
