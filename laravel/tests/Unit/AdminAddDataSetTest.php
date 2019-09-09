<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

use App\ScheduleParser;
use App\Option;
use App\EvaluationParser;
use App\Http\Controllers\MedhubController;

class AdminAddDataSetTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
	
	public function testAdminAddDataSetDataTableHasData()
    {
        $this->assertDatabaseHas('attending',['id' => '1']);
		$this->assertDatabaseHas('option',['id' => '1']);
		$this->assertDatabaseHas('schedule_data',['id' => '1']);
    }
	
	// public function testAdminAddDataSetPost()
    // {
        // $ac = new AdminController(); 
		// $response = $ac->postAddDB(); 
		// $this->assertNotNull($response); 
    // }
	
	
	
	
	
}
