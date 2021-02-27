<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

use Carbon\Carbon;

class UpdateAnesthesiologistsDataTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testExample()
    {
        $this->artisan("update:anesthesiologists_data");
        $this->assertDatabaseHas("anesthesiologists", [
            ["updated_at", ">", Carbon::today()],
        ]);
        $this->assertDatabaseHas("anesthesiologists", [
            "staff_key" => "04f3e583-b2dc-43af-a6bc-695984106807",
        ]);
    }
}
