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
            "staff_key" => "d835e8f4-f262-4731-bb82-167c6009aa3e",
        ]);
    }
}
