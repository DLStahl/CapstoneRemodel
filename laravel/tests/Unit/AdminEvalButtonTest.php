<?php

namespace Tests\Unit;

use Tests\TestCase;

class AdminEvalButtonTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */

    public function testAdminEvalButtonEvaluationDataTableHasData()
    {
        $this->assertDatabaseHas('evaluation_data', ['id' => '71']);
    }
}
