<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\EvaluationParser;
use App\Models\Resident;
use App\Models\Attending;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class EvaluationParserTest extends TestCase
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
    public function addResident($resident)
    {
        return Resident::insertGetId([
            "name" => $resident[0],
            "email" => $resident[1],
        ]);
    }

    public function addAllResidents($residents)
    {
        $ids = [];
        foreach ($residents as $resident) {
            $id = self::addResident($resident);
            array_push($ids, $id);
        }
        return $ids;
    }

    public function addAllAttendings($attendings)
    {
        foreach ($attendings as $attending) {
            Attending::insert([
                "id" => $attending[0],
                "name" => $attending[1],
            ]);
        }
    }

    public function testEvaluationParser()
    {
       // insert residents and attendings
       $residents = [
            ["Resident Test1", "test1@email"],
            ["Resident Test2", "test2@email"],
            ["Resident Name Test3", "test3@email"],
            ["Resident Test4", "test4@email"],
            ["Resident Name Test5", "test5@email"],
        ];
        $residentIds = $this->addAllResidents($residents);
        $attendings = [["1", "Attending Test1"], ["2", "Attending Name Test2"], ["3", "Attending Test3"],["4", "Attending Test4"]];
        self::addAllAttendings($attendings);
        $expectedResults = [
            "Failed Resident Name" => [
                "No matches for Resident Failed Resident Name were found on MedHub. No matches for Resident Failed Resident Name were found by OSU Find People. The Resident may be using a preffered name at OSU. Please check the information and add user to database manually.",
            ],
            "Failed Attendings" => [
                "No matches for Attending Failed Attending were found on MedHub. No matches for Attending Failed Attending were found by OSU Find People. The Attending may be using a preffered name at OSU. Please check the information and add user to database manually.",
            ],
        ];
        $expectedDataInserted = [
            [$residentIds[0], "Resident Test1", 1, "Attending Test1", "23", "TestL1", "TestD1", "TestP1", "4"],
            [$residentIds[1], "Resident Test2", 1, "Attending Test1", "495", "TestL2", "TestD2", "TestP2", "3"],
            [$residentIds[2], "Resident Name Test3", 2, "Attending Name Test2", "90", "TestL3", "", "TestP3", "3"],
            [$residentIds[3], "Resident Test4", 1, "Attending Test1", "57", "TestL4", "TestD4", "TestP4", "4"],
            [$residentIds[3], "Resident Test4", 1, "Attending Test1", "56", "TestL4", "TestD4", "TestP4", "4"],
            [$residentIds[3], "Resident Test4", 1, "Attending Test1", "239", "TestL5", "TestD5", "TestP5", "1"],
            [$residentIds[3], "Resident Test4", 3, "Attending Test3", "78", "TestL5", "TestD5", "TestP5", "1"],
            [$residentIds[4], "Resident Name Test5", 4, "Attending Test4", "107", "TestL6", "TestD6", "TestP6", "2"],
            [$residentIds[0], "Resident Test1", 4, "Attending Test4", "30", "TestL7", "TestD7", "TestP7", ""],
            [$residentIds[4], "Resident Name Test5", 4, "Attending Test4", "159", "TestL7", "TestD7", "TestP7", ""],
            [$residentIds[3], "Resident Test4", 4, "Attending Test4", "186", "TestL11", "TestD11", "TestP11", "3"],
            [$residentIds[3], "Resident Test4", 2,"Attending Name Test2", "25", "TestL11", "TestD11", "TestP11", "3"],
        ];
        $parser = new EvaluationParser("20210328");
        $results = $parser->insertEvaluateData();

        $this->assertEqualsCanonicalizing($expectedResults, $results);
        foreach ($expectedDataInserted as $expectedEntry) {
            $this->assertDatabaseHas('evaluation_data', [ 
                "date" => date("2021-03-27"),
                "rId" => $expectedEntry[0],
                "resident" => $expectedEntry[1],
                "aId" => $expectedEntry[2],
                "attending" => $expectedEntry[3],
                "diff" => $expectedEntry[4],
                "location" => $expectedEntry[5],
                "diagnosis" => $expectedEntry[6],
                "procedure" => $expectedEntry[7],
                "ASA" => $expectedEntry[8],
            ]);
        }
    }

    // Tests for getNamePossibilities()
    // name: first m last
    public function testGetNamePossibilitiesForFMLName()
    {
        $expectedNameOptions = [["Megan", "Spitz"]];
        $name = "Megan R Spitz";
        $nameOptions = EvaluationParser::getNamePossibilities($name);
        $this->assertEqualsCanonicalizing($nameOptions, $expectedNameOptions);
    }

    // name: first first m last
    public function testGetNamePossibilitiesForFFMLName()
    {
        $expectedNameOptions = [["Megan Second", "Spitz"]];
        $name = "Megan Second R Spitz";
        $nameOptions = EvaluationParser::getNamePossibilities($name);
        $this->assertEqualsCanonicalizing($nameOptions, $expectedNameOptions);
    }

    // name: first m last last
    public function testGetNamePossibilitiesForFMLLName()
    {
        $expectedNameOptions = [["Megan", "Spitz Second"]];
        $name = "Megan R Spitz Second";
        $nameOptions = EvaluationParser::getNamePossibilities($name);
        $this->assertEqualsCanonicalizing($nameOptions, $expectedNameOptions);
    }
    // name: first first m last last
    public function testGetNamePossibilitiesForFFMLLName()
    {
        $expectedNameOptions = [["Megan Second", "Spitz Third"]];
        $name = "Megan Second R Spitz Third";
        $nameOptions = EvaluationParser::getNamePossibilities($name);
        $this->assertEqualsCanonicalizing($nameOptions, $expectedNameOptions);
    }

    // name: first last
    public function testGetNamePossibilitiesForFLName()
    {
        $expectedNameOptions = [["Megan", "Spitz"]];
        $name = "Megan Spitz";
        $nameOptions = EvaluationParser::getNamePossibilities($name);
        $this->assertEqualsCanonicalizing($nameOptions, $expectedNameOptions);
    }
    // name: ambigious a b c
    public function testGetNamePossibilitiesForAmbigious3Name()
    {
        $expectedNameOptions = [["Megan", "Second Spitz"], ["Megan Second", "Spitz"]];
        $name = "Megan Second Spitz";
        $nameOptions = EvaluationParser::getNamePossibilities($name);
        $this->assertEqualsCanonicalizing($nameOptions, $expectedNameOptions);
    }

    // name: ambigious a b c d
    public function testGetNamePossibilitiesForAmbigious4Name()
    {
        $expectedNameOptions = [
            ["Megan", "Second Third Spitz"],
            ["Megan Second", "Third Spitz"],
            ["Megan Second Third", "Spitz"],
        ];
        $name = "Megan Second Third Spitz";
        $nameOptions = EvaluationParser::getNamePossibilities($name);
        $this->assertEqualsCanonicalizing($nameOptions, $expectedNameOptions);
    }
    // name: has suffix
    public function testGetNamePossibilitiesForSuffixName()
    {
        $expectedNameOptions = [["Robert", "Schroell"]];
        $name = "Robert Schroell III";
        $nameOptions = EvaluationParser::getNamePossibilities($name);
        $this->assertEqualsCanonicalizing($nameOptions, $expectedNameOptions);
    }

    // Test for getMinutesDiff()
    public function testGetMinutesDiff()
    {
        $startTime = "21-03-26 12:54";
        $endTime = "21-03-26 17:54";
        $minutes = EvaluationParser::getMinutesDiff($startTime, $endTime);
        $this->assertEquals(300, $minutes);
    }
    // Tests for getTime()
    public function testGetTimeForLineWithDate()
    {
        $parser = new EvaluationParser("20210323");
        $getTimeMethod = $this->getPrivateMethod("\App\EvaluationParser", "getTime");
        $time = $getTimeMethod->invokeArgs($parser, ["03/22/21 1930", "2021-03-22"]);
        $expectedTime = "2021-03-22 19:30";
        $this->assertEquals($expectedTime, $time);
    }

    public function testGetTimeWithNow()
    {
        $parser = new EvaluationParser("20210323");
        $getTimeMethod = $this->getPrivateMethod("\App\EvaluationParser", "getTime");
        $time = $getTimeMethod->invokeArgs($parser, ["Now", "2021-03-26"]);
        $expectedTime = "2021-03-26 05:00";
        $this->assertEquals($expectedTime, $time);
    }

    public function testGetTimeWithNoDate()
    {
        $parser = new EvaluationParser("20210323");
        $getTimeMethod = $this->getPrivateMethod("\App\EvaluationParser", "getTime");
        $time = $getTimeMethod->invokeArgs($parser, ["1430", date("y-m-d")]);
        $expectedTime = date("y-m-d", strtotime("-1 day")) . " 14:30";
        $this->assertEquals($expectedTime, $time);
    }

    // Test getDate($line)
    public function testGetDate()
    {
        $date = EvaluationParser::getDate("03/22/21");
        $this->assertEquals("2021-03-22", $date);
    }
    // Test user find people request
    public function testUserFindPeople()
    {
        try {
            $result = EvaluationParser::findPeopleOSU("Michael", "Bragalone"); 
        } catch (\Exception $e) {
            $this->fail("Exception caught for find People OSU request for Michael Bragalone:" .  $e->getCode(). " " . $e->getMessage());
        }
        $this->assertNotNull($result);
    }
}
