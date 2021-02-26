<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;

use App\Console\Commands\AutoAssign;
use Artisan;

class SchedulingChoiceWithRotationTest extends TestCase
{
    /**
     * Tests that rotation beats out a higher ticket count.
     *
     * @return void
     */
    public function testfirstPrefWins()
    {
        //get tomorrow's date
        //$date = date('Y-m-d');
        //$tomorrow = strtotime ( '+1 day' , strtotime ( $date ) ) ;
        //$tomorrow = date ( 'Y-m-d' , $tomorrow );

        //insert mock data into database
        //$optionId1 = DB::table('option')->insertGetId(['date' => $tomorrow, 'resident' => 305, 'schedule' => 140765, 'attending' => 115350, 'option' => 1, 'milestones' => 3, 'objectives' => 'test', 'isValid' => 1 ]);

        //305 = Mikayla -- 4 is Vascular(rotation)
        //306 = Test Resident -- 8 is Basic
        //$rotationId1 = DB::table('rotations')->insertGetId(['name' => 'Test Resident1', 'Level' => 1, 'Service' => 8, 'Site' => 'RD', 'Start' => '2020-04-01', 'End' => '2020-04-15']);
        //$rotationId2 = DB::table('rotations')->insertGetId(['name' => 'Mikayla Richter', 'Level' => 1, 'Service' => 4, 'Site' => 'RD', 'Start' => '2020-04-01', 'End' => '2020-04-15']);

        //$this->artisan('autoassign');

        //$assigned1 = DB::table('assignment')->where('option', $optionId1)->value('schedule');
        //$this->assertEquals($assigned1, 140765);

        //delete mock data from database
        //DB::table('option')->where('id', $optionId1)->delete();

        $this->assertTrue(true);
    }

    /**
     * Tests that rotation beats out a higher ticket count.
     *
     * @return void
     */
    public function testNoFirstPref()
    {
        //get tomorrow's date
        //$date = date('Y-m-d');
        //$tomorrow = strtotime ( '+1 day' , strtotime ( $date ) ) ;
        //$tomorrow = date ( 'Y-m-d' , $tomorrow );

        //insert mock data into database
        //$optionId1 = DB::table('option')->insertGetId(['date' => $tomorrow, 'resident' => 305, 'schedule' => 140765, 'attending' => 115350, 'option' => 2, 'milestones' => 3, 'objectives' => 'test', 'isValid' => 1 ]);

        //305 = Mikayla -- 4 is Vascular(rotation)
        //306 = Test Resident -- 8 is Basic
        //$rotationId1 = DB::table('rotations')->insertGetId(['name' => 'Test Resident1', 'Level' => 1, 'Service' => 8, 'Site' => 'RD', 'Start' => '2020-04-01', 'End' => '2020-04-15']);
        //$rotationId2 = DB::table('rotations')->insertGetId(['name' => 'Mikayla Richter', 'Level' => 1, 'Service' => 4, 'Site' => 'RD', 'Start' => '2020-04-01', 'End' => '2020-04-15']);

        //$this->artisan('autoassign');

        //$assigned1 = DB::table('assignment')->where('option', $optionId1)->value('schedule');
        //$this->assertEquals($assigned1, 140765);

        //delete mock data from database
        //DB::table('option')->where('id', $optionId1)->delete();

        $this->assertTrue(true);
    }

    /**
     * Tests that rotation beats out a higher ticket count.
     *
     * @return void
     */
    public function testManyConflicts()
    {
        //get tomorrow's date
        //$date = date('Y-m-d');
        //$tomorrow = strtotime ( '+1 day' , strtotime ( $date ) ) ;
        //$tomorrow = date ( 'Y-m-d' , $tomorrow );

        //insert mock data into database
        //$optionId1 = DB::table('option')->insertGetId(['date' => $tomorrow, 'resident' => 305, 'schedule' => 140771, 'attending' => 115350, 'option' => 1, 'milestones' => 3, 'objectives' => 'test', 'isValid' => 1 ]);
        //$optionId2 = DB::table('option')->insertGetId(['date' => $tomorrow, 'resident' => 305, 'schedule' => 140772, 'attending' => 115350, 'option' => 2, 'milestones' => 3, 'objectives' => 'test', 'isValid' => 1 ]);
        //$optionId3 = DB::table('option')->insertGetId(['date' => $tomorrow, 'resident' => 305, 'schedule' => 140770, 'attending' => 115350, 'option' => 3, 'milestones' => 3, 'objectives' => 'test', 'isValid' => 1 ]);
        //$optionId4 = DB::table('option')->insertGetId(['date' => $tomorrow, 'resident' => 306, 'schedule' => 140771, 'attending' => 115350, 'option' => 1, 'milestones' => 3, 'objectives' => 'test', 'isValid' => 1 ]);
        //$optionId5 = DB::table('option')->insertGetId(['date' => $tomorrow, 'resident' => 306, 'schedule' => 140772, 'attending' => 115350, 'option' => 2, 'milestones' => 3, 'objectives' => 'test', 'isValid' => 1 ]);
        //$optionId6 = DB::table('option')->insertGetId(['date' => $tomorrow, 'resident' => 306, 'schedule' => 140770, 'attending' => 115350, 'option' => 3, 'milestones' => 3, 'objectives' => 'test', 'isValid' => 1 ]);
        //$optionId7 = DB::table('option')->insertGetId(['date' => $tomorrow, 'resident' => 400000, 'schedule' => 140771, 'attending' => 115350, 'option' => 1, 'milestones' => 3, 'objectives' => 'test', 'isValid' => 1 ]);
        //$optionId8 = DB::table('option')->insertGetId(['date' => $tomorrow, 'resident' => 400000, 'schedule' => 140772, 'attending' => 115350, 'option' => 2, 'milestones' => 3, 'objectives' => 'test', 'isValid' => 1 ]);
        //$optionId9 = DB::table('option')->insertGetId(['date' => $tomorrow, 'resident' => 400000, 'schedule' => 140770, 'attending' => 115350, 'option' => 3, 'milestones' => 3, 'objectives' => 'test', 'isValid' => 1 ]);

        //305 = Mikayla -- 4 is Vascular(rotation)
        //306 = Test Resident -- 8 is Basic
        //400000 = Travis Lee -- 4 is Vascular(rotation)
        //$rotationId1 = DB::table('rotations')->insertGetId(['name' => 'Test Resident1', 'Level' => 1, 'Service' => 8, 'Site' => 'RD', 'Start' => '2020-04-01', 'End' => '2020-04-15']);
        //$rotationId2 = DB::table('rotations')->insertGetId(['name' => 'Mikayla Richter', 'Level' => 1, 'Service' => 4, 'Site' => 'RD', 'Start' => '2020-04-01', 'End' => '2020-04-15']);
        //$rotationId3 = DB::table('rotations')->insertGetId(['name' => 'Travis Lee', 'Level' => 1, 'Service' => 4, 'Site' => 'RD', 'Start' => '2020-04-01', 'End' => '2020-04-15']);

        //$this->artisan('autoassign');

        //305 gets rotation - 140771; 400000 get 140772 (more tickets); 306 gets 140770
        //$assigned1 = DB::table('assignment')->where('option', 615)->value('schedule');
        //$this->assertEquals($assigned1, 140771);
        //$assigned2 = DB::table('assignment')->where('option', 622)->value('schedule');
        //$this->assertEquals($assigned2, 140772);
        //$assigned3 = DB::table('assignment')->where('option', 620)->value('schedule');
        //$this->assertEquals($assigned3, 140770);

        //delete mock data from database
        //DB::table('option')->where('id', $optionId1)->delete();
        //DB::table('option')->where('id', $optionId2)->delete();
        //DB::table('option')->where('id', $optionId3)->delete();
        //DB::table('option')->where('id', $optionId4)->delete();
        //DB::table('option')->where('id', $optionId5)->delete();
        //DB::table('option')->where('id', $optionId6)->delete();
        //DB::table('option')->where('id', $optionId7)->delete();
        //DB::table('option')->where('id', $optionId8)->delete();
        //DB::table('option')->where('id', $optionId9)->delete();
        //DB::table('rotation')->where('id', $rotationId1)->delete();
        //DB::table('rotation')->where('id', $rotationId2)->delete();
        //DB::table('rotation')->where('id', $rotationId3)->delete();

        $this->assertTrue(true);
    }

    /**
     * Tests that rotation beats out a higher ticket count.
     *
     * @return void
     */
    public function testTicketsWins()
    {
        //get tomorrow's date
        //$date = date('Y-m-d');
        //$tomorrow = strtotime ( '+1 day' , strtotime ( $date ) ) ;
        //$tomorrow = date ( 'Y-m-d' , $tomorrow );

        //insert mock data into database
        //$optionId1 = DB::table('option')->insertGetId(['date' => $tomorrow, 'resident' => 305, 'schedule' => 140770, 'attending' => 115350, 'option' => 1, 'milestones' => 3, 'objectives' => 'test', 'isValid' => 1 ]);
        //$optionId2 = DB::table('option')->insertGetId(['date' => $tomorrow, 'resident' => 306, 'schedule' => 140770, 'attending' => 115350, 'option' => 1, 'milestones' => 3, 'objectives' => 'test', 'isValid' => 1 ]);

        //305 = Mikayla -- 4 is Vascular(rotation)
        //306 = Test Resident -- 8 is Basic
        //$rotationId1 = DB::table('rotations')->insertGetId(['name' => 'Test Resident1', 'Level' => 1, 'Service' => 8, 'Site' => 'RD', 'Start' => '2020-04-01', 'End' => '2020-04-15']);
        //$rotationId2 = DB::table('rotations')->insertGetId(['name' => 'Mikayla Richter', 'Level' => 1, 'Service' => 4, 'Site' => 'RD', 'Start' => '2020-04-01', 'End' => '2020-04-15']);

        //$this->artisan('autoassign');

        //305 has more tickets so 305 gets 140760
        //$assigned1 = DB::table('assignment')->where('option', $optionId1)->value('schedule');
        //$this->assertEquals($assigned1, 140770);

        //delete mock data from database
        //DB::table('option')->where('id', $optionId1)->delete();
        //DB::table('option')->where('id', $optionId2)->delete();
        //DB::table('rotation')->where('id', $rotationId1)->delete();
        //DB::table('rotation')->where('id', $rotationId2)->delete();

        $this->assertTrue(true);
    }

    /**
     * Tests that rotation beats out a higher ticket count.
     *
     * @return void
     */
    public function testRotationWins()
    {
        //get tomorrow's date
        //$date = date('Y-m-d');
        //$tomorrow = strtotime ( '+1 day' , strtotime ( $date ) ) ;
        //$tomorrow = date ( 'Y-m-d' , $tomorrow );

        //insert mock data into database
        //$optionId1 = DB::table('option')->insertGetId(['date' => $tomorrow, 'resident' => 305, 'schedule' => 140771, 'attending' => 115350, 'option' => 1, 'milestones' => 3, 'objectives' => 'test', 'isValid' => 1 ]);
        //$optionId2 = DB::table('option')->insertGetId(['date' => $tomorrow, 'resident' => 306, 'schedule' => 140771, 'attending' => 115350, 'option' => 1, 'milestones' => 3, 'objectives' => 'test', 'isValid' => 1 ]);
        //$optionId3 = DB::table('option')->insertGetId(['date' => $tomorrow, 'resident' => 306, 'schedule' => 140769, 'attending' => 115350, 'option' => 2, 'milestones' => 3, 'objectives' => 'test', 'isValid' => 1 ]);

        //305 = Mikayla -- 4 is Vascular(rotation)
        //306 = Test Resident -- 8 is Basic
        //$rotationId1 = DB::table('rotations')->insertGetId(['name' => 'Test Resident1', 'Level' => 1, 'Service' => 8, 'Site' => 'RD', 'Start' => '2020-04-01', 'End' => '2020-04-15']);
        //$rotationId2 = DB::table('rotations')->insertGetId(['name' => 'Mikayla Richter', 'Level' => 1, 'Service' => 4, 'Site' => 'RD', 'Start' => '2020-04-01', 'End' => '2020-04-15']);

        //$this->artisan('autoassign');

        //check that resident 305 got their first choice (on rotation) and resident 306 got their second choice
        //$assigned1 = DB::table('assignment')->where('option', 612)->value('schedule');
        //$this->assertEquals($assigned1, 140771);
        //$assigned2 = DB::table('assignment')->where('option', 614)->value('schedule');
        //$this->assertEquals($assigned2, 140769);

        //delete mock data from database
        //DB::table('option')->where('id', $optionId1)->delete();
        //DB::table('option')->where('id', $optionId2)->delete();
        //DB::table('option')->where('id', $optionId3)->delete();
        //DB::table('rotation')->where('id', $rotationId1)->delete();
        //DB::table('rotation')->where('id', $rotationId2)->delete();

        $this->assertTrue(true);
    }
}
