<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Http\Controllers\MedhubController;

class SaveMedHubInfoTest extends TestCase
{
    // TODO: move methods into Medhub Controller Test

    /**
     * A basic test to check the active residents call returns values
     *
     * @return void
     */
    public function testMedHubActiveResidentsPOST()
    {
        $MHC = new MedhubController();
        $usersArr = json_decode($MHC->activeResidentsPOST()->getBody(), true);
        $this->assertNotNull($usersArr);
    }

    /**
     * A basic test to check the active residents call returns values
     *
     * @return void
     */
    public function testMedHubActiveFacultyPOST()
    {
        $MHC = new MedhubController();
        $usersArr = json_decode($MHC->activeFacultyPOST()->getBody(), true);
        $this->assertNotNull($usersArr);
    }
}
