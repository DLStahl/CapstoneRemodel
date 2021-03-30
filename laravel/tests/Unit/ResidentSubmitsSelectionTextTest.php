<?php

namespace Tests\Unit;

use Tests\TestCase;

class ResidentSubmitsSelectionText extends TestCase
{
    public function testOptionTableHasSubmissionOfPref1()
    {
        $this->assertDatabaseHas("option", ["id" => "1"]);
    }
    public function testOptionTableHasSubmissionOfPref2()
    {
        $this->assertDatabaseHas("option", ["id" => "3"]);
    }
    public function testOptionTableHasSubmissionOfPref3()
    {
        $this->assertDatabaseHas("option", ["id" => "7"]);
    }
}
