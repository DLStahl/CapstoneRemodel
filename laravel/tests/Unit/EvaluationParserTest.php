<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\EvaluationParser;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class EvaluationParserTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    use DatabaseTransactions;
    public function testEvaluationParser()
    {
        //$parser = new EvaluationParser('20210328', true);
        $parser = new EvaluationParser('20210323', true);
        //$parser = new EvaluationParser('20180418', true);
        $results = $parser->insertEvaluateData();
        //$parser->notifyForAllFailedUsers($results, config("mail.admin.name"), config("mail.admin.email"));
        $this->assertDatabaseHas('evaluation_data', [
            'date' => date('2021-03-22')
        ]);
    }


// Tests for getNamePossibilities()    
    // name: first m last
    public function testGetNamePossibilitiesForFMLName() {
        $expectedNameOptions = [
            ["Megan" , "Spitz"],
        ];
        $name = "Megan R Spitz";
        $nameOptions = EvaluationParser::getNamePossibilities($name);
        $this->assertEqualsCanonicalizing($nameOptions, $expectedNameOptions);
    }

    // name: first first m last
    public function testGetNamePossibilitiesForFFMLName() {
        $expectedNameOptions = [
            ["Megan Second", "Spitz"],
        ];
        $name = "Megan Second R Spitz";
        $nameOptions = EvaluationParser::getNamePossibilities($name);
        $this->assertEqualsCanonicalizing($nameOptions, $expectedNameOptions);
    }

    // name: first m last last
    public function testGetNamePossibilitiesForFMLLName() {
        $expectedNameOptions = [
            ["Megan", "Spitz Second"],
        ];
        $name = "Megan R Spitz Second";
        $nameOptions = EvaluationParser::getNamePossibilities($name);
        $this->assertEqualsCanonicalizing($nameOptions, $expectedNameOptions);
    }
    // name: first first m last last
    public function testGetNamePossibilitiesForFFMLLName() {
        $expectedNameOptions = [
            ["Megan Second", "Spitz Third"],
        ];
        $name = "Megan Second R Spitz Third";
        $nameOptions = EvaluationParser::getNamePossibilities($name);
        $this->assertEqualsCanonicalizing($nameOptions, $expectedNameOptions);
    }

    // name: first last
    public function testGetNamePossibilitiesForFLName() {
        $expectedNameOptions = [
            ["Megan","Spitz"],
        ];
        $name = "Megan Spitz";
        $nameOptions = EvaluationParser::getNamePossibilities($name);
        $this->assertEqualsCanonicalizing($nameOptions, $expectedNameOptions);
    }
    // name: ambigious a b c 
    public function testGetNamePossibilitiesForAmbigious3Name() {
        $expectedNameOptions = [
            ["Megan", "Second Spitz"],
            ["Megan Second", "Spitz"],
        ];
        $name = "Megan Second Spitz";
        $nameOptions = EvaluationParser::getNamePossibilities($name);
        $this->assertEqualsCanonicalizing($nameOptions, $expectedNameOptions);
    }

    // name: ambigious a b c d
    public function testGetNamePossibilitiesForAmbigious4Name() {
        $expectedNameOptions = [
            ["Megan", "Second Third Spitz"],
            ["Megan Second", "Third Spitz"],
            ["Megan Second Third", "Spitz"],
        ];
        $name = "Megan Second Third Spitz";
        $nameOptions =EvaluationParser::getNamePossibilities($name);
        $this->assertEqualsCanonicalizing($nameOptions, $expectedNameOptions);
    }
    // name: has suffix
    public function testGetNamePossibilitiesForSuffixName(){
        $expectedNameOptions = [
            ["Robert", "Schroell"],
        ];
        $name = "Robert Schroell III";
        $nameOptions =EvaluationParser::getNamePossibilities($name);
        $this->assertEqualsCanonicalizing($nameOptions, $expectedNameOptions);
    }

// Test for getMinutesDiff()
    public function testGetMinutesDiff() {
        $startTime = "21-03-26 12:54";
        $endTime = "21-03-26 17:54";
        $minutes = EvaluationParser::getMinutesDiff($startTime, $endTime);
        $this->assertEquals(300, $minutes);
    }
    
// Tests for getTime()

    public function testGetTimeForLineWithDate() {
        $time = EvaluationParser::getTime("03/22/21 1930", "2021-03-22");
        $expectedTime = "2021-03-22 19:30";
        $this->assertEquals($expectedTime, $time);
    }

    public function testGetTimeWithNow() {
        $time = EvaluationParser::getTime("Now", "2021-03-26");
        $expectedTime = "2021-03-26 05:00";
        $this->assertEquals($expectedTime, $time);
    }

    public function testGetTimeWithNoDate() {
        $time = EvaluationParser::getTime("1430", date('y-m-d'));
        $expectedTime = date('y-m-d', strtotime("-1 day")) . " 14:30";
        $this->assertEquals($expectedTime, $time);
    }

// Test getDate($line)
    public function testGetDate() {
        $date = EvaluationParser::getDate("03/22/21");
        $this->assertEquals("2021-03-22", $date);
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