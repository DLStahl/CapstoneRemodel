<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

use App\Console\Commands\PushSchedule;
use Google_Client; 
use Google_Service_Drive;
use Google_Service_Sheets;
use Google_Service_Sheets_ValueRange;
use Google_Service_Sheets_BatchUpdateSpreadsheetRequest; 

class GoogleAPITest extends TestCase {
    /**
     * A basic test of a connection to the google client.
     *
     * @return void
     */
    public function testGoogleClientConnection() {
        $ps = new PushSchedule(); 
        $client = $ps->getClient();
        $this->assertNotNull($client);
    }

    /**
     * A basic test of a connection to the google client, then to google sheets.
     *
     * @return void
     */
    public function testGoogleSheetsAvailability() {
        $ps = new PushSchedule(); 
        $client = $ps->getClient();
        $service = new Google_Service_Sheets($client);
        $this->assertNotNull($service);
    }
}
