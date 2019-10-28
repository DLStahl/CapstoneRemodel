<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ResidentMySelectionPageTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */


    // My selection consists of Milestones 
    public function testConsistsMilestones()
    {
        // Retrieve Milestone information
        // Check if there is Milestone information the same
        $this->assertTrue(true);
    }
    // My selection consists of objective 
    public function testConsistsObjective()
    {
        // Retrieve Objective information
        // Check if there is Objective information
        $this->assertTrue(true);
    }
    // My selection edit button link to the edit page
    public function testConsistsEditLinkButton()
    {
        // Visit the myselection page
        // Get the button information 
        // Check if it link to the edit page
        $this->assertTrue(true);
    }
    // Edit for Milestone did from my selection page changed the resident selection
    public function testMilestoneChanges()
    {
        $this->assertTrue(true);
        $this->assertFalse(false);
    }
    // Edit for Objective did from my selection page changed the resident selection
    public function testObjectiveChanges()
    {
        $this->assertTrue(true);
        $this->assertFalse(false);
    }
}
