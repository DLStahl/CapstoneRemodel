<?php

namespace Tests\Unit;

use Tests\TestCase;

class ResidentSelectMilestoneTest extends TestCase
{
    /**
     * Test to check a certain user exists
     *
     */
    public function testOptionTableHasMilestone1()
    {
        $this->assertDatabaseHas('option', ['milestones' => '1']);
    }

    public function testOptionTableHasMilestone2()
    {
        $this->assertDatabaseHas('option', ['milestones' => '3']);
    }

    public function testOptionTableHasMilestone3()
    {
        $this->assertDatabaseHas('option', ['milestones' => '5']);
    }

    public function testOptionTableHasEntryFromTestResidentGail()
    {
        $this->assertDatabaseHas('option', ['resident' => '115']);
    }
    public function testOptionTableHasEntryFromTestResidentKader()
    {
        $this->assertDatabaseHas('option', ['resident' => '113']);
    }
}
