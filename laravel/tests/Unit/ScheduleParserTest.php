<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\ScheduleParser;

use Illuminate\Foundation\Testing\DatabaseTransactions;

class ScheduleParserTest extends TestCase
{
    use DatabaseTransactions;

    public function testScheduleParser()
    {
        $expectedDataInserted = [
            ["2021-03-22", "OSU CCCT MAIN OR", "CCCT 18 Leasing UH12", "09:45:00", "14:00:00"],
            ["2021-03-22", "OSU ROSS EP", "EP 04", "16:35:00", "17:35:00"],
            ["2021-03-22", "OSU UH MAIN OR", "UH-13", null, null],
            ["2021-03-22", "OSU UH SAME DAY SURGERY MAIN OR", "UH TBD", null, null],
            ["2021-03-22", "OSU UH SAME DAY SURGERY MAIN OR", "SDS-03", "09:05:00", "11:50:00"],
            ["2021-03-22", "OSU UH MAIN OR", "UH-13", null, null],
            ["2021-03-22", "OSU UH MAIN OR", "UH-13", "07:40:00", "17:00:00"],
            ["2021-03-22", "OSU CCCT MAIN OR", "CCCT TBD", null, null],
            ["2021-03-22", "OSU CCCT MAIN OR", "CCCT 10", "07:45:00", "16:00:00"],
            ["2021-03-22", "OSU ROSS MAIN OR", "RHH-05", "14:55:00", "17:45:00"],
            ["2021-03-22", "OSU ROSS CATH", "CATH 03", "09:00:00", "10:00:00"],
            ["2021-03-22", "OSU CCCT MAIN OR", "CCCT V22-MRI", "07:45:00", "11:30:00"],
            ["2021-03-22", "OSU ROSS EP", "IPR PROCEDURES", null, null],
            ["2021-03-22", "OSU ROSS EP", "EP Drug Load", "10:00:00", "11:00:00"],
            ["2021-03-24", "OSU ROSS EP", "EP 06", "14:00:00", "15:30:00"],
            ["2021-03-24", "OSU UH MAIN OR", "UH TBD", null, null],
            ["2021-03-24", "OSU ROSS CATH", "CATH 03", "08:00:00", "09:00:00"],
        ];
        $parser = new ScheduleParser("20210320", true);
        foreach($expectedDataInserted as $expected){
            $this->assertDatabaseHas("schedule_data", [
                "date" => $expected[0],
                "location" => $expected[1],
                "room" => $expected[2],
                "start_time" => $expected[3],
                "end_time" => $expected[4],
            ]);
        }
    }
}
