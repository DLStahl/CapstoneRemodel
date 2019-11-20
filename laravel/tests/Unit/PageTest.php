<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

use App\ScheduleParser;
use App\EvaluationParser;
use App\Http\Controllers\MedhubController;





class UserSubscriptionTest extends TestCase
{
    public function setUp()
    {
        // $this->setHost('localhost');
        // $this->setPort(4444);
        // $this->setBrowserUrl('https://remodel.anesthesiology-dev.org.ohio-state.edu/laravel/public/');
        // $this->setBrowser('firefox');
        
    }
    public function test1()
    {
        sleep(16);
        $this->assertTrue(true);
    }
}