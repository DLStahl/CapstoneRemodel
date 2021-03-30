<?php

namespace Tests\Unit;

use Tests\TestCase;

class EvaluateGenerateTimeIntervals extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testAssignmentTableHasData()
    {
        $this->assertDatabaseHas("assignment", ["id" => "1"]);
    }

    public function testEvaluationDataTableHasData()
    {
        $this->assertDatabaseHas("evaluation_data", ["id" => "71"]);
    }
}
