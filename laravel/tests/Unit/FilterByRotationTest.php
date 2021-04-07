<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\ScheduleParser;

class FilterByRotationTest extends TestCase
{
    //TODO: rewrite - testing for SchedulerParser
    public function testFilterByRotation1()
    {
        $parser = new ScheduleParser("20200101", true);
        $this->assertDatabaseHas("schedule_data", [
            "date" => date("2020-01-03"),
            // 'location'=> 'OSU CCCT MAIN OR',
            // 'room'=> 'CCCT 04',
            // 'case_procedure'=> '(07:30:00-10:45:00)A B [3], B C [4] (11:00:00-15:15:00)A [1], B C [2]',
            // 'lead_surgeon'=> 'D, Md [1234] D, Md [1234]',
            // 'patient_class'=> 'SA SA',
            // 'start_time'=> date('07:30:00'),
            // 'end_time'=> date('15:15:00'),
        ]);
    }
}
