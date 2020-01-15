<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

use App\ScheduleParser;
use App\EvaluationParser;
use App\Resident;

class SendNotificationTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */

	public function testSendNotificationResidentTableHasData()
    {
        $this->assertDatabaseHas('resident',['id' => '1']);
    }

	public function testSendNotificationResidentTableNoticesMissingData()
    {
        $this->assertDatabaseMissing('resident',['id' => '9999999']);
    }

	public function testSendNofiticaionResidentTableHasCorrectNameData()
    {
        $this->assertDatabaseHas('resident',['name' => 'Amy Baumann']);
    }

	// public function testNotificationResidentTableHasCorrectIDData()
  //   {
  //       $this->assertDatabaseHas('resident',['medhubId' => '114146']);
  //   }

	// public function testNotificationSent()
  //   {
	// 	$ep = new EvaluationParser(date("o", strtotime('today')).date("m", strtotime('today')).date("d", strtotime('today')), true);
	// 	$result = $ep->notifyAddResident('Test', 'p1353818@nwytg.net', "Test Resident");
	// 	$this->assertNotNull($result);
  //   }




}
