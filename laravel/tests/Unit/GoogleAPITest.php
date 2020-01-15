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

class GoogleAPITest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testGoogleClientConnection()
    {
        $ps = new PushSchedule(); 
		$client = $ps->getClient();
		$this->assertNotNull($client);
    }

	public function testGoogleSheetsAvailability()
    {
        $ps = new PushSchedule(); 
		$client = $ps->getClient();
		$service = new Google_Service_Sheets($client);
		$this->assertNotNull($service);
    }
	
	// public function testGoogleSheetsCreateSheet()
    // {
        // $ps = new PushSchedule(); 
		// $client = $ps->getClient();
        // $service = new Google_Service_Sheets($client);
        // $spreadsheetId = '1npNBs_j6BvmZO29GHlEJ-mROGhtBEqM7_KNKdAnNLxY';
	
        // // create new sheet for today 
        // $newSheet = new Google_Service_Sheets_BatchUpdateSpreadsheetRequest(array(
                // 'requests' => array(
                    // 'addSheet' => array(
                        // 'properties' => array(
                            // 'title' => 'testSheet'
                        // )
                    // )
                // )
            // ));	
        // $response = $service->spreadsheets->batchUpdate('1npNBs_j6BvmZO29GHlEJ-mROGhtBEqM7_KNKdAnNLxY', $newSheet);
		// $this->assertNotNull($response);
    // }
	
	// public function testGoogleSheetsWriteSheet()
    // {
        // $ps = new PushSchedule(); 
		// $client = $ps->getClient();
		// $service = new Google_Service_Sheets($client);
        // $spreadsheetId = '1npNBs_j6BvmZO29GHlEJ-mROGhtBEqM7_KNKdAnNLxY';
        // $title = '\''.'testSheet'.'\'!';
        // $range = $title.'A1:G15';
        // $csv = array();
        // $body = new Google_Service_Sheets_ValueRange([
            // 'values' => $csv
        // ]);
        // $params = [
            // 'valueInputOption' => 'USER_ENTERED'
        // ];
        // $result = $service->spreadsheets_values->update($spreadsheetId, $range, $body, $params);
		// $this->assertNotNull($result);
    // }
	
	public function testCSVReadSheet()
    {
        $file = fopen('/usr/local/webs/remodel.anesthesiology_dev/htdocs/assignment.csv', 'r');
        $csv = array();
        while (($line = fgetcsv($file)) !== FALSE) {
            //$line is an array of the csv elements
            $csv[] = $line;
        }
        fclose($file);
		$this->assertNotNull($csv);
    }
}
