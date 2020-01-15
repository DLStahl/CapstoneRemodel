<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\DatabaseMigrations;

use App\Http\Controllers\ScheduleDataController;

class ResidentConfirmationFlowTest extends TestCase
{
    //use DatabaseMigrations;
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testFlowFromSelectionToConfirmation()
    {
        //$browser = @section('content');
        //$browser->click('@login-button');
        //$testPost = json_decode($PC->)
        // Go to the selection page
        // check if next is milestone and objective page
        $this->assertTrue(true);
        // Check if next is confirmation page
        $this->assertTrue(true);
    }
}
