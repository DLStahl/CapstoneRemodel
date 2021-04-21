<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Http\Controllers\MedhubController;

class RetrieveResidentIDTest extends TestCase
{
    /**
     * A basic test to check the connection to medhub api with TestPOST call
     *
     * @return void
     */
    public function testMedHubAPIConnection()
    {
        $MHC = new MedhubController();
        $testPOST = json_decode($MHC->testPOST()->getBody(), true);
        $response = $testPOST['response'];
        $this->assertTrue($response == 'success');
    }

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
     * A basic test to check that the residents table has values
     *
     * @return void
     */
    public function testMedHubResidentTableFilled()
    {
        $this->assertDatabaseHas('resident', [
            'name' => 'Priscilla Agbenyefia',
        ]);
    }

    /**
     * A basic test to check the active residents call returns values that match up to the residents table
     *
     * @return void
     */
    // public function testMedHubActiveResidentsInResidentTable()
    // {
    // $MHC = new MedhubController();
    // $usersArr = json_decode($MHC->activeResidentsPOST()->getBody(), true);
    // $this->assertNotNull($usersArr);
    // // $userID corresponding to Priscilla Agbenyefia who is a user of the site
    // $userID = $usersArr[1]['userID'];
    // $this->assertDatabaseHas('resident',['UserID' => $userID]);
    // }
}
