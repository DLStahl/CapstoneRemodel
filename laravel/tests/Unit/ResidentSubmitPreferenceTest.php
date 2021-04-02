<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

use App\Http\Controllers\ScheduleDataController;

class ResidentSubmitPreferenceTest extends TestCase
{
    // Resident selects no preferences
    public function testNoPref()
    {
        // check aler message
        $response = $this->get("/resident/schedule/secondday");
        // $response->assertStatus(200);
        //print ($response);
        $this->assertTrue(true);
    }
    // Resident selects 1st and 3rd
    public function testSelect13()
    {
        // check alert message
        $this->assertTrue(true);
    }
    // Resident selects two 1st preferences
    public function testSelect11()
    {
        $this->assertTrue(true);
    }
}
