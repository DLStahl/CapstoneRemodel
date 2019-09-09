<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

use App\ScheduleParser;
use App\EvaluationParser;
use App\Http\Controllers\MedhubController;

class AdminEvalButtonTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
	
	public function testAdminEvalButtonEvaluationDataTableHasData()
    {
        $this->assertDatabaseHas('evaluation_data',['id' => '1']);
    }
	
	
	
}
