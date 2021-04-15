<?php

namespace App;

use App\Models\Admin;
use App\Models\Resident;
use App\Models\Attending;
use App\Models\Assignment;
use App\Models\Option;
use App\Models\ScheduleData;

class AdminDownload
{
    public static function updateAccess()
    {
        $dir = __DIR__ . '/../../downloads/.htaccess';
        $fp = fopen($dir, file_exists($dir) ? 'w' : 'c');

        fwrite($fp, "ShibUseHeaders On\r\n");
        fwrite($fp, "AuthType shibboleth\r\n");
        fwrite($fp, "ShibRequestSetting redirectToSSL 443\r\n");
        fwrite($fp, "ShibRequestSetting requireSession 1\r\n");

        $admins = Admin::where('exists', '1')->get();

        foreach ($admins as $admin) {
            $line = 'Require shib-user ' . $admin['email'] . "\r\n";
            fwrite($fp, $line);
        }

        fwrite($fp, "ShibRequestSetting authnContextClassRef urn:mace:osu.edu:shibboleth:ac:classes:mfa\r\n");
        fwrite($fp, "Require authnContextClassRef urn:mace:osu.edu:shibboleth:ac:classes:mfa\r\n");

        fclose($fp);
    }

    private static function updateResident($date)
    {
        $dir = __DIR__ . '/../../downloads/resident' . $date . '.csv';
        $fp = fopen($dir, file_exists($dir) ? 'w' : 'c');

        fputcsv($fp, ['name', 'email']);
        $residents = Resident::where('exists', '1')->get();

        foreach ($residents as $resident) {
            fputcsv($fp, [$resident['name'], $resident['email']]);
        }
        fclose($fp);
    }

    private static function updateAttending($date)
    {
        $dir = __DIR__ . '/../../downloads/attending' . $date . '.csv';
        $fp = fopen($dir, file_exists($dir) ? 'w' : 'c');

        fputcsv($fp, ['name', 'email']);
        $attendings = Attending::where('exists', '1')->get();

        foreach ($attendings as $attending) {
            fputcsv($fp, [$attending['name'], $attending['email']]);
        }
        fclose($fp);
    }

    private static function updateAdmin($date)
    {
        $dir = __DIR__ . '/../../downloads/admin' . $date . '.csv';
        $fp = fopen($dir, file_exists($dir) ? 'w' : 'c');

        fputcsv($fp, ['name', 'email']);
        $admins = Admin::where('exists', '1')->get();

        foreach ($admins as $admin) {
            fputcsv($fp, [$admin['name'], $admin['email']]);
        }
        fclose($fp);
    }

    private static function updateOption($date)
    {
        $dir = __DIR__ . '/../../downloads/option' . $date . '.csv';
        $fp = fopen($dir, file_exists($dir) ? 'w' : 'c');

        fputcsv($fp, [
            'date',
            'room',
            'patient class',
            'start time',
            'end time',
            'lead surgeon',
            'resident',
            'preference',
            'milestones',
            'objectives',
        ]);
        $options = null;
        if ($date == null) {
            $options = Option::orderBy('date', 'desc')->get();
        } else {
            $options = Option::where('date', $date)->get();
        }

        foreach ($options as $option) {
            $schedule_id = $option['schedule'];
            $resident_id = $option['resident'];

            $date = $option['date'];
            $room = ScheduleData::where('id', $schedule_id)->value('room');
            $patient_class = ScheduleData::where('id', $schedule_id)->value('patient_class');
            $start_time = ScheduleData::where('id', $schedule_id)->value('start_time');
            $end_time = ScheduleData::where('id', $schedule_id)->value('end_time');
            $lead_surgeon = ScheduleData::where('id', $schedule_id)->value('lead_surgeon');
            $resident = Resident::where('id', $resident_id)->value('name');
            $preference = $option['option'];
            $milestones = $option['milestones'];
            $objectives = $option['objectives'];

            fputcsv($fp, [
                $date,
                $room,
                $patient_class,
                $start_time,
                $end_time,
                $lead_surgeon,
                $resident,
                $preference,
                $milestones,
                $objectives,
            ]);
        }
        fclose($fp);
    }

    private static function updateAssignment($date)
    {
        $dir = __DIR__ . '/../../downloads/assignment' . $date . '.csv';
        $fp = fopen($dir, file_exists($dir) ? 'w' : 'c');

        fputcsv($fp, ['date', 'room', 'patient class', 'start time', 'end time', 'lead surgeon', 'resident']);

        $options = null;
        if ($date == null) {
            $options = Assignment::orderBy('date', 'desc')->get();
        } else {
            $options = Assignment::where('date', $date)->get();
        }

        foreach ($options as $option) {
            $schedule_id = $option['schedule'];
            $resident_id = $option['resident'];

            $date = $option['date'];
            $room = ScheduleData::where('id', $schedule_id)->value('room');
            $patient_class = ScheduleData::where('id', $schedule_id)->value('patient_class');
            $start_time = ScheduleData::where('id', $schedule_id)->value('start_time');
            $end_time = ScheduleData::where('id', $schedule_id)->value('end_time');
            $lead_surgeon = ScheduleData::where('id', $schedule_id)->value('lead_surgeon');
            $resident = Resident::where('id', $resident_id)->value('name');

            fputcsv($fp, [$date, $room, $patient_class, $start_time, $end_time, $lead_surgeon, $resident]);
        }
        fclose($fp);
    }

    public static function updateFiles($date = null)
    {
        self::updateResident($date);
        self::updateAttending($date);
        self::updateAdmin($date);
        self::updateAssignment($date);
        self::updateOption($date);
    }

    public static function updateURL($date)
    {
        self::updateFiles($date);
        return [
            '../../../downloads/assignment' . $date . '.csv',
            '../../../downloads/option' . $date . '.csv',
            '../../../downloads/admin' . $date . '.csv',
            '../../../downloads/resident' . $date . '.csv',
            '../../../downloads/attending' . $date . '.csv',
        ];
    }
}
