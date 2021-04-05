<?php

namespace Tests\Unit;

use Tests\TestCase;

class GenerateUserNotFoundNotificationTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */

    public function testGenerateResidentTableHasData()
    {
        $this->assertDatabaseHas("resident", ["id" => "1"]);
    }

    public function testGenerateResidentTableNoticesMissingData()
    {
        $this->assertDatabaseMissing("resident", ["id" => "9999999"]);
    }

    public function testGenerateResidentTableHasCorrectNameData()
    {
        $this->assertDatabaseHas("resident", ["name" => "Amy Baumann"]);
    }

    public function testGenerateResidentTableHasCorrectIDData()
    {
        $this->assertDatabaseHas("resident", ["medhubId" => "114144"]);
    }
}
