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
		$countPref1 = Option::where('resident', '115')->where('option', '1')->where('date', '2019-09-23')->count();
        $compare = ($countPref1 == 1); 
        $this->assertTrue(true);
		$this->assertTrue($compare);    
	}
	public function testOption2ForTestResidentHasOneValue()
    {
		$countPref1 = Option::where('resident', '115')->where('option', '2')->where('date', '2019-09-23')->count();
        $compare = ($countPref1 == 1); 
        $this->assertTrue(true);
		$this->assertTrue($compare);    
	}
	public function testOption3ForTestResidentHasOneValue()
    {
		$countPref1 = Option::where('resident', '115')->where('option', '3')->where('date', '2019-09-23')->count();
        $compare = ($countPref1 == 1); 
        $this->assertTrue(true);
		$this->assertTrue($compare);    
	}
	public function testResidentonlysubmit1Prefernce()
	{
		$countPref1 = Option::where('resident', '300')->where('option', '1')->where('date', '2019-09-25')->count();
        $compare = ($countPref1 == 1); 
        $this->assertTrue(true);
		$this->assertTrue($compare);   

		$countPref2 = Option::where('resident', '300')->where('option', '2')->where('date', '2019-09-25')->count();
        $compare = ($countPref2 == 0); 
        $this->assertTrue(true);
		$this->assertTrue($compare);    

		$countPref3 = Option::where('resident', '300')->where('option', '3')->where('date', '2019-09-25')->count();
        $compare = ($countPref3 == 0); 
        $this->assertTrue(true);
		$this->assertTrue($compare); 
	}
	public function testResidentonlysubmit2Prefernce()
	{
		$countPref1 = Option::where('resident', '113')->where('option', '1')->where('date','2019-09-25')->count();
        $compare = ($countPref1 == 1); 
        $this->assertTrue(true);
		$this->assertTrue($compare);   

		$countPref2 = Option::where('resident', '113')->where('option', '2')->where('date','2019-09-25')->count();
        $compare = ($countPref2 == 1); 
        $this->assertTrue(true);
		$this->assertTrue($compare);  
		
		$countPref3 = Option::where('resident', '113')->where('option', '3')->where('date', '2019-09-25')->count();
        $compare = ($countPref3 == 0); 
        $this->assertTrue(true);
		$this->assertTrue($compare); 
	}
}
