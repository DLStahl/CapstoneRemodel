<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\ScheduleParser;
use App\Models\FilterRotation;

use Illuminate\Foundation\Testing\DatabaseTransactions;

class ScheduleParserTest extends TestCase
{
    use DatabaseTransactions;

    // sets a private method for a given class to be accessible so it can be used in testing
    // link: https://stackoverflow.com/questions/249664/best-practices-to-test-protected-methods-with-phpunit
    public function getPrivateMethod($className, $methodName)
    {
        $reflector = new \ReflectionClass($className);
        $method = $reflector->getMethod($methodName);
        $method->setAccessible(true);
        return $method;
    }

    public function testScheduleParser()
    {
        $expectedDataInserted = [
            ['2021-03-22', 'OSU CCCT MAIN OR', 'CCCT 18 Leasing UH12', '09:45:00', '14:00:00'],
            ['2021-03-22', 'OSU ROSS EP', 'EP 04', '16:35:00', '17:35:00'],
            ['2021-03-22', 'OSU UH MAIN OR', 'UH-13', null, null],
            ['2021-03-22', 'OSU UH SAME DAY SURGERY MAIN OR', 'UH TBD', null, null],
            ['2021-03-22', 'OSU UH SAME DAY SURGERY MAIN OR', 'SDS-03', '09:05:00', '11:50:00'],
            ['2021-03-22', 'OSU UH MAIN OR', 'UH-13', null, null],
            ['2021-03-22', 'OSU UH MAIN OR', 'UH-13', '07:40:00', '17:00:00'],
            ['2021-03-22', 'OSU CCCT MAIN OR', 'CCCT TBD', null, null],
            ['2021-03-22', 'OSU CCCT MAIN OR', 'CCCT 10', '07:45:00', '16:00:00'],
            ['2021-03-22', 'OSU ROSS MAIN OR', 'RHH-05', '14:55:00', '17:45:00'],
            ['2021-03-22', 'OSU ROSS CATH', 'CATH 03', '09:00:00', '10:00:00'],
            ['2021-03-22', 'OSU CCCT MAIN OR', 'CCCT V22-MRI', '07:45:00', '11:30:00'],
            ['2021-03-22', 'OSU ROSS EP', 'IPR PROCEDURES', null, null],
            ['2021-03-22', 'OSU ROSS EP', 'EP Drug Load', '10:00:00', '11:00:00'],
            ['2021-03-24', 'OSU ROSS EP', 'EP 06', '14:00:00', '15:30:00'],
            ['2021-03-24', 'OSU UH MAIN OR', 'UH TBD', null, null],
            ['2021-03-24', 'OSU ROSS CATH', 'CATH 03', '08:00:00', '09:00:00'],
        ];
        $parser = new ScheduleParser('20210320', true);
        foreach ($expectedDataInserted as $expected) {
            $this->assertDatabaseHas('schedule_data', [
                'date' => $expected[0],
                'location' => $expected[1],
                'room' => $expected[2],
                'start_time' => $expected[3],
                'end_time' => $expected[4],
            ]);
        }
    }

    public function testGetLineTimeGiven4Digits()
    {
        $parser = new ScheduleParser('12345678', true);
        $getTimeMethod = $this->getPrivateMethod('\App\ScheduleParser', 'getLineTime');
        $time = $getTimeMethod->invokeArgs($parser, [['1430'], 0]);
        $expectedTime = '14:30:00';
        $this->assertEquals($expectedTime, $time);
    }

    public function testGetLineTimeGiven3Digits()
    {
        $parser = new ScheduleParser('12345678', true);
        $getTimeMethod = $this->getPrivateMethod('\App\ScheduleParser', 'getLineTime');
        $time = $getTimeMethod->invokeArgs($parser, [['830'], 0]);
        $expectedTime = '08:30:00';
        $this->assertEquals($expectedTime, $time);
    }

    public function testGetLineTimeGivenNoTime()
    {
        $parser = new ScheduleParser('12345678', true);
        $getTimeMethod = $this->getPrivateMethod('\App\ScheduleParser', 'getLineTime');
        $time = $getTimeMethod->invokeArgs($parser, [[''], 0]);
        $expectedTime = null;
        $this->assertEquals($expectedTime, $time);
    }

    public function testGetLineDate()
    {
        $parser = new ScheduleParser('12345678', true);
        $getDateMethod = $this->getPrivateMethod('\App\ScheduleParser', 'getLineDate');
        $date = $getDateMethod->invokeArgs($parser, [['3/22/2021'], 0]);
        $expectedDate = '2021-03-22';
        $this->assertEquals($expectedDate, $date);
    }

    public function testGetRotation()
    {
        //insert mock data
        FilterRotation::insert([
            'surgeon' => 'Testing Surgeon',
            'rotation' => 'Basic',
        ]);
        $parser = new ScheduleParser('12345678', true);
        $getRotationMethod = $this->getPrivateMethod('\App\ScheduleParser', 'getRotation');
        $rotation = $getRotationMethod->invokeArgs($parser, ['Testing Surgeon']);
        $expectedRotation = 'Basic';
        $this->assertEquals($expectedRotation, $rotation);
    }

    public function testGetRotationGivenSurgeonMiddleName()
    {
        //insert mock data
        FilterRotation::insert([
            'surgeon' => 'Testing Surgeon',
            'rotation' => 'Basic',
        ]);
        $parser = new ScheduleParser('12345678', true);
        $getRotationMethod = $this->getPrivateMethod('\App\ScheduleParser', 'getRotation');
        $rotation = $getRotationMethod->invokeArgs($parser, ['Testing M Surgeon']);
        $expectedRotation = 'Basic';
        $this->assertEquals($expectedRotation, $rotation);
    }
}
