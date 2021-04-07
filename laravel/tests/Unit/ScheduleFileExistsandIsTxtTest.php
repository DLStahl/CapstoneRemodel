<?php

namespace Tests\Unit;

use Tests\TestCase;

class ScheduleFileExistsandIsTxtTest extends TestCase
{
    /**
     * Test if the schedule file exists
     * @return void
     */

    public function TestUploadSchedulePageHasAScheduleFile()
    {
        $this->assertFileExists("/laravel/storage/app/ResidentRotationSchedule/medhub-report.txt");
    }

    public function TestUploadSchedulePageHasTxtFile()
    {
        $reg = "^.*\.(txt)$^";
        $result = -1;
        $actual = 1;
        $file = "/laravel/storage/app/ResidentRotationSchedule/medhub-rotations.txt";
        if (preg_match($reg, $file)) {
            $result = 1;
        } else {
            $result = 0;
        }

        $this->assertEquals($result, $actual);
    }

    public function TestFileNameAsMedhubReportTxtOnlyFile()
    {
        $reg = "(medhub-report)\.(txt)";
        $result = -1;
        $actual = 1;
        $file = "/laravel/storage/app/ResidentRotationSchedule/medhub-rotations.txt";

        if (preg_match($reg, $file)) {
            $result = 1;
        } else {
            $result = 0;
        }

        $this->assertEquals($result, $actual);
    }

}
