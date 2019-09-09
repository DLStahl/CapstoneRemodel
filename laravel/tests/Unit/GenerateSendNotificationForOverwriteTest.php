<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\ScheduleParser;
use App\Option;
use App\Http\Controllers\ScheduleDataController;

class GenerateSendNotificationForOverwrite extends TestCase
{	

		
	public function testOption1ForTestResidentHasOneValue()
    {
		$countPref1 = Option::where('resident', '115')->where('option', '1')->where('id', '453')->count();
        $compare = ($countPref1 == 1); 
        $this->assertTrue(true);
		$this->assertTrue($compare);    
	}
	public function testOption2ForTestResidentHasOneValue()
    {
		$countPref1 = Option::where('resident', '115')->where('option', '2')->where('id', '455')->count();
        $compare = ($countPref1 == 1); 
        $this->assertTrue(true);
		$this->assertTrue($compare);    
	}
	public function testOption3ForTestResidentHasOneValue()
    {
		$countPref1 = Option::where('resident', '115')->where('option', '3')->where('id', '457')->count();
        $compare = ($countPref1 == 1); 
        $this->assertTrue(true);
		$this->assertTrue($compare);    
	}
}
