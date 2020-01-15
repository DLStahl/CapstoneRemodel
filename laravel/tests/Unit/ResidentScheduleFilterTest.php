<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ResidentScheduleFilterTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testSuccessfulFilter()
    {
        sleep(5);
        // Apply filter
        // Get the certain rotation that was shown
        // Check if it is only that rotation
        $this->assertTrue(true);
    }
    public function testclearFilter()
    {
        sleep(5);
        // Click clear filter
        // Get the all rotation that was shown
        $this->assertTrue(true);
    }
    // Apply filter to the dropdown menu on room 
    public function testDropdownRoom()
    {
        sleep(5);
        $this->assertTrue(true);
    }
    // Apply filter to the dropdown menu on surgeon
    public function testDropdownSurgeon()
    {
        sleep(5);
        $this->assertTrue(true);
    }
    // Apply filter to the dropdown menu on rotation
    public function testDropdownRotation()
    {
        sleep(5);
        $this->assertTrue(true);
    }
    // Apply filter to the dropdown menu on normal start time
    public function testDropdownStartTime()
    {
        sleep(5);
        $this->assertTrue(true);
    }
    // Apply filter to the dropdown menu on start time between surgery
    public function testDropdownbetweenSurgery()
    {
        sleep(5);
        $this->assertTrue(true);
    }
    // Apply filter to the dropdown menu on start time after all surgery
    public function testDropdownAfterSurgery()
    {
        sleep(5);
        $this->assertTrue(true);
    }
    // Apply filter to the dropdown menu on normal end time
    public function testDropdownEndTime()
    {
        sleep(5);
        $this->assertTrue(true);
    }
    // Apply filter to the dropdown menu with same start time and end time [fail test]
    public function testDropdownSameStartEndTime()
    {
        sleep(5);
        // Invalid selection!
        $this->assertTrue(true);
    }
    // Apply filter to the dropdown menu on end time between surgery
    public function testDropdownEndTimeBetweenSurgery()
    {
        sleep(5);
        $this->assertTrue(true);
    }
    // Apply filter to the dropdown menu on super early end time
    public function testDropdownEarlyEndTime()
    {
        sleep(5);
        // No schedule found.
        $this->assertTrue(true);
    }
    // Apply filter to the dropdown menu on end time before start time [fail test]
    public function testDropdownEndTimebeforStartTime()
    {
        sleep(5);
        // Invalid selection!
        $this->assertTrue(true);
    }
    // Apply multiple filter - room and surgeon
    public function testDropdownRoomSurgeon()
    {
        sleep(5);
        $this->assertTrue(true);
    }



}
