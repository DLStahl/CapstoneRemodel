<?php

namespace Tests\Unit;

use Tests\TestCase;

class ResidentViewTable extends TestCase
{
    public function testViewScheduleDataHasStartTime()
    {
        $this->assertDatabaseHas("schedule_data", ["start_time" => "07:40:00"]);
    }

    public function testViewScheduleDataHasORRoom()
    {
        $this->assertDatabaseHas("schedule_data", [
            "location" => "OSU UH MAIN OR",
        ]);
    }

    public function testViewScheduleDataHasRoom()
    {
        $this->assertDatabaseHas("schedule_data", ["room" => "UH-16"]);
    }
}
