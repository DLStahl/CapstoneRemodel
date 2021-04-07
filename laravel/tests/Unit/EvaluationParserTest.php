<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\EvaluationParser;

class EvaluationParserTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testEvaluationParser()
    {
        $parser = new EvaluationParser('20210328', true);
        $parser = new EvaluationParser('20210323', true);
        $parser = new EvaluationParser('20180418', true);
        $this->assertDatabaseHas('evaluation_data', [
            'date' => date('2018-04-17')
        ]);
    }


// Tests for getNamePossibilities()    
    // name: first m last
    public function testGetNamePossibilitiesForFMLName() {
        $name = "Megan R Spitz";
        $nameOptions = EvaluationParser::getNamePossibilities($name);
        $this->assertEquals(count($nameOptions), 1); 
        $this->assertEquals($nameOptions[0][0], "Megan");
        $this->assertEquals($nameOptions[0][1], "Spitz");
    }

    // name: first first m last
    public function testGetNamePossibilitiesForFFMLName() {
        $name = "Megan Second R Spitz";
        $nameOptions = EvaluationParser::getNamePossibilities($name);
        $this->assertEquals(count($nameOptions), 1); 
        $this->assertEquals($nameOptions[0][0], "Megan Second");
        $this->assertEquals($nameOptions[0][1], "Spitz");
    }

    // name: first m last last
    public function testGetNamePossibilitiesForFMLLName() {
        $name = "Megan R Spitz Second";
        $nameOptions = EvaluationParser::getNamePossibilities($name);
        $this->assertEquals(count($nameOptions), 1); 
        $this->assertEquals($nameOptions[0][0], "Megan");
        $this->assertEquals($nameOptions[0][1], "Spitz Second");
    }
    // name: first first m last last
    public function testGetNamePossibilitiesForFFMLLName() {
        $name = "Megan Second R Spitz Third";
        $nameOptions = EvaluationParser::getNamePossibilities($name);
        $this->assertEquals(count($nameOptions), 1); 
        $this->assertEquals($nameOptions[0][0], "Megan Second");
        $this->assertEquals($nameOptions[0][1], "Spitz Third");
    }

    // name: first last
    public function testGetNamePossibilitiesForFLName() {
        $name = "Megan Spitz";
        $nameOptions = EvaluationParser::getNamePossibilities($name);
        $this->assertEquals(count($nameOptions), 1); 
        $this->assertEquals($nameOptions[0][0], "Megan");
        $this->assertEquals($nameOptions[0][1], "Spitz");
    }
    // name: ambigious a b c 
    public function testGetNamePossibilitiesForAmbigious3Name() {
        $name = "Megan Second Spitz";
        $nameOptions = EvaluationParser::getNamePossibilities($name);
        $this->assertEquals(count($nameOptions), 2); 
        // case: first = a and last = b c
        $this->assertEquals($nameOptions[0][0], "Megan");
        $this->assertEquals($nameOptions[0][1], "Second Spitz");
        // case: first = a b and last = c
        $this->assertEquals($nameOptions[1][0], "Megan Second");
        $this->assertEquals($nameOptions[1][1], "Spitz");
    }

    // name: ambigious a b c d
    public function testGetNamePossibilitiesForAmbigious4Name() {
        $name = "Megan Second Third Spitz";
        $nameOptions =EvaluationParser::getNamePossibilities($name);
        $this->assertEquals(count($nameOptions), 3); 
        // case: first = a and last = b c d
        $this->assertEquals($nameOptions[0][0], "Megan");
        $this->assertEquals($nameOptions[0][1], "Second Third Spitz");
        // case: first = a b and last = c d
        $this->assertEquals($nameOptions[1][0], "Megan Second");
        $this->assertEquals($nameOptions[1][1], "Third Spitz");
        // case: first = a b c and last = d
        $this->assertEquals($nameOptions[2][0], "Megan Second Third");
        $this->assertEquals($nameOptions[2][1], "Spitz");
    }
    // name: has suffix
    public function testGetNamePossibilitiesForSuffixName(){
        $name = "Robert Schroell III";
        $nameOptions =EvaluationParser::getNamePossibilities($name);
        $this->assertEquals(count($nameOptions), 1); 
        // case: first = a and last = b c d
        $this->assertEquals($nameOptions[0][0], "Robert");
        $this->assertEquals($nameOptions[0][1], "Schroell");
    }

// Test for getMinutesDiff()
    public function testGetMinutesDiff() {
        $startTime = "21-03-26 12:54";
        $endTime = "21-03-26 17:54";
        $minutes = EvaluationParser::getMinutesDiff($startTime, $endTime);
        $this->assertEquals($minutes, 300);
    }
    
// Tests for getTime()

    public function testGetTimeForLineWithDate() {
        $time = EvaluationParser::getTime("03/22/21 1930", "2021-03-22");
        $expectedTime = "21-3-22 19:30";
        $this->assertEquals($time, $expectedTime);
    }

    public function testGetTimeWithNow() {
        $time = EvaluationParser::getTime("Now", "2021-03-26");
        $expectedTime = "2021-03-26 05:00";
        $this->assertEquals($time, $expectedTime);
    }

    public function testGetTimeWithNoDate() {
        $time = EvaluationParser::getTime("1430", date('y-m-d'));
        $expectedTime = date('y-m-d', strtotime("-1 day")) . " 14:30";
        $this->assertEquals($time, $expectedTime);
    }

// Test getDate($line)
    public function testGetDate() {
        $time = EvaluationParser::getDate("03/22/21");
        $this->assertEquals($time, "21-3-22");
    }
// Test user find people request
    public function testUserFindPeople() {
        $result = NULL;
        try{
            $result = EvaluationParser::findPeopleOSU("Michael", "Bragalone");
        }catch (\Exception $e){
            echo "\nTESTING AdminAddUserTest: Exception caught for find People OSU request for Michael Bragalone";
        }
		$this->assertNotNull($result);
    }
}