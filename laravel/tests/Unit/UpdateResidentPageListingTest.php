<?php

namespace Tests\Unit;

use Tests\TestCase;

class UpdateResidentPageListingTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */

    public function testListingResidentTableHasData()
    {
        $this->assertDatabaseHas("resident", ["id" => "1"]);
    }

    public function testListingResidentTableHasCorrectNameData()
    {
        $this->assertDatabaseHas("resident", ["name" => "Priscilla Agbenyefia"]);
    }

    public function testListingResidentTableHasCorrectIDData()
    {
        $this->assertDatabaseHas("resident", ["medhubId" => "112342"]);
    }
}
