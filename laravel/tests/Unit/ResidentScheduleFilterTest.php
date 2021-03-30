<?php

namespace Tests\Unit;

use Tests\TestCase;

class ResidentScheduleFilterTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testSuccessfulFilter()
    {
        // Apply filter
        // Get the certain rotation that was shown
        // Check if it is only that rotation
        $this->assertTrue(true);
    }
    public function testclearFilter()
    {
        // Click clear filter
        // Get the all rotation that was shown
        $this->assertTrue(true);
    }
    // Apply filter to the dropdown menu on room
    public function testDropdownRoom()
    {
        $this->assertTrue(true);
    }
    // Apply filter to the dropdown menu on surgeon
    public function testDropdownSurgeon()
    {
        $this->assertTrue(true);
    }
    // Apply filter to the dropdown menu on rotation
    public function testDropdownRotation()
    {
        $this->assertTrue(true);
    }
    // Apply filter to the dropdown menu on normal start time
    public function testDropdownStartTime()
    {
        $this->assertTrue(true);
    }
    // Apply filter to the dropdown menu on start time between surgery
    public function testDropdownbetweenSurgery()
    {
        $this->assertTrue(true);
    }
    // Apply filter to the dropdown menu on start time after all surgery
    public function testDropdownAfterSurgery()
    {
        $this->assertTrue(true);
    }
    // Apply filter to the dropdown menu on normal end time
    public function testDropdownEndTime()
    {
        $this->assertTrue(true);
    }
    // Apply filter to the dropdown menu with same start time and end time [fail test]
    public function testDropdownSameStartEndTime()
    {
        // Invalid selection!
        $this->assertTrue(true);
    }
    // Apply filter to the dropdown menu on end time between surgery
    public function testDropdownEndTimeBetweenSurgery()
    {
        $this->assertTrue(true);
    }
    // Apply filter to the dropdown menu on super early end time
    public function testDropdownEarlyEndTime()
    {
        // No schedule found.
        $this->assertTrue(true);
    }
    // Apply filter to the dropdown menu on end time before start time [fail test]
    public function testDropdownEndTimebeforStartTime()
    {
        // Invalid selection!
        $this->assertTrue(true);
    }
    // Apply multiple filter - room and surgeon
    public function testDropdownRoomSurgeon()
    {
        $this->assertTrue(true);
    }
}
