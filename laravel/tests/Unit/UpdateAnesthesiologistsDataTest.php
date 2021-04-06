<?php

namespace Tests\Unit;

use Tests\TestCase;
use Carbon\Carbon;

class UpdateAnesthesiologistsDataTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testUpdateAnesthesiologistDataCommand()
    {
        $this->artisan('update:anesthesiologists_data');
        $this->assertDatabaseHas('anesthesiologists',[['updated_at', '>', Carbon::today()]]);
    }
}
