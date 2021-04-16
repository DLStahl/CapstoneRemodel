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
        $parser = new EvaluationParser('20180418', true);
        $this->assertDatabaseHas('evaluation_data', [
            'date' => date('2018-04-17'),
        ]);
    }
}
