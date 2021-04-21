<?php

namespace Tests\Unit;

use Tests\TestCase;

class ResidentSubmitsFromOneScreenTest extends TestCase
{
    /**
     * Test to check a certain user exists
     *
     */
    public function testOptionTableHasTestSubmissionOfPref1()
    {
        $this->assertDatabaseHas('option', ['id' => '1']);
    }
    public function testOptionTableHasTestSubmissionOfPref2()
    {
        $this->assertDatabaseHas('option', ['id' => '11']);
    }
    public function testOptionTableHasTestSubmissionOfPref3()
    {
        $this->assertDatabaseHas('option', ['id' => '3']);
    }

    public function testOptionTableHasPref1()
    {
        $this->assertDatabaseHas('option', ['option' => '1']);
    }
    public function testOptionTableHasPref2()
    {
        $this->assertDatabaseHas('option', ['option' => '2']);
    }
    public function testOptionTableHasPref3()
    {
        $this->assertDatabaseHas('option', ['option' => '3']);
    }
}
