<?php

namespace Tests\Unit;

use Tests\TestCase;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class UpdateAnesthesiologistsDataTest extends TestCase
{
    use DatabaseTransactions;

    public function testUpdateAnesthesiologistDataCommand()
    {
        $this->artisan("update:anesthesiologists_data");
        $this->assertDatabaseHas("anesthesiologists", [["updated_at", ">", Carbon::today()]]);
    }
}
