<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Admin;
use App\Models\Anesthesiologist;
use App\Models\Announcements;
use App\Models\Resident;
use App\Models\Attending;
use App\Models\Option;
use App\Models\Milestone;
use App\Models\ScheduleData;
use App\Models\Assignment;
use App\EvaluationParser;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;

class PagesController extends Controller
{
    private function calculateFirst()
    {
        if (date('l', strtotime('today')) == 'Friday') {
            return date('Y-m-d', strtotime('+3 day'));
        } elseif (date('l', strtotime('today')) == 'Saturday') {
            return date('Y-m-d', strtotime('+2 day'));
        }
        return date('Y-m-d', strtotime('+1 day'));
    }

    private function calculateSecond()
    {
        if (date('l', strtotime('today')) == 'Thursday' || date('l', strtotime('today')) == 'Friday') {
            return date('Y-m-d', strtotime('+4 day'));
        } elseif (date('l', strtotime('today')) == 'Saturday') {
            return date('Y-m-d', strtotime('+3 day'));
        }
        return date('Y-m-d', strtotime('+2 day'));
    }

    private function calculateThird()
    {
        if (
            date('l', strtotime('today')) == 'Wednesday' ||
            date('l', strtotime('today')) == 'Thursday' ||
            date('l', strtotime('today')) == 'Friday'
        ) {
            return date('Y-m-d', strtotime('+5 day'));
        } elseif (date('l', strtotime('today')) == 'Saturday') {
            return date('Y-m-d', strtotime('+4 day'));
        }
        return date('Y-m-d', strtotime('+3 day'));
    }

    private function processSingleChoice($id)
    {
        $date = ScheduleData::where('id', $id)->value('date');
        $location = ScheduleData::where('id', $id)->value('location');
        $room = ScheduleData::where('id', $id)->value('room');
        $case_procedure = ScheduleData::where('id', $id)->value('case_procedure');
        $case_procedure = preg_replace('/[0-9]+/', '', $case_procedure);
        $case_procedure = preg_replace('/[:\/]/', '', $case_procedure);
        $case_procedure = preg_replace('/\(|\)/', '', $case_procedure);
        $case_procedure = str_replace(' [', '', $case_procedure);
        $case_procedure = str_replace(['[', ']'], '', $case_procedure);
        $start_t = ScheduleData::where('id', $id)->value('start_time');
        $end_t = ScheduleData::where('id', $id)->value('end_time');
        if (strlen($start_t) < 1) {
            $start_t = 'N/A';
        }
        if (strlen($end_t) < 1) {
            $end_t = 'N/A';
        }

        return 'Room ' . $room . "\n Case procedure: \n" . $case_procedure . 'Time: ' . $start_t . ' - ' . $end_t;
    }

    private function processChoices($date, $id)
    {
        $day_arr = [
            'first' => null,
            'second' => null,
            'third' => null,
            'ids' => null,
        ];

        $schedule1 = Option::where('date', $date)
            ->where('resident', $id)
            ->where('option', 1)
            ->value('schedule');
        $milestone1 = Option::where('date', $date)
            ->where('resident', $id)
            ->where('option', 1)
            ->value('milestones');
        $milestone1C = Milestone::where('id', $milestone1)->value('category');
        $milestone1D = Milestone::where('id', $milestone1)->value('detail');
        $objective1 = Option::where('date', $date)
            ->where('resident', $id)
            ->where('option', 1)
            ->value('objectives');
        $pref_anest_id = Option::where('date', $date)
            ->where('resident', $id)
            ->where('option', 1)
            ->value('anesthesiologist_id');
        if ($pref_anest_id != null) {
            $pref_anest1 =
                Anesthesiologist::where('id', $pref_anest_id)->value('first_name') .
                ' ' .
                Anesthesiologist::where('id', $pref_anest_id)->value('last_name');
        } else {
            $pref_anest1 = 'No Preference';
        }
        $schedule2 = Option::where('date', $date)
            ->where('resident', $id)
            ->where('option', 2)
            ->value('schedule');
        $milestone2 = Option::where('date', $date)
            ->where('resident', $id)
            ->where('option', 2)
            ->value('milestones');
        $milestone2C = Milestone::where('id', $milestone2)->value('category');
        $milestone2D = Milestone::where('id', $milestone2)->value('detail');
        $objective2 = Option::where('date', $date)
            ->where('resident', $id)
            ->where('option', 2)
            ->value('objectives');
        $pref_anest_id = Option::where('date', $date)
            ->where('resident', $id)
            ->where('option', 2)
            ->value('anesthesiologist_id');
        if ($pref_anest_id != null) {
            $pref_anest2 =
                Anesthesiologist::where('id', $pref_anest_id)->value('first_name') .
                ' ' .
                Anesthesiologist::where('id', $pref_anest_id)->value('last_name');
        } else {
            $pref_anest2 = 'No Preference';
        }
        $schedule3 = Option::where('date', $date)
            ->where('resident', $id)
            ->where('option', 3)
            ->value('schedule');
        $milestone3 = Option::where('date', $date)
            ->where('resident', $id)
            ->where('option', 3)
            ->value('milestones');
        $milestone3C = Milestone::where('id', $milestone3)->value('category');
        $milestone3D = Milestone::where('id', $milestone3)->value('detail');
        $objective3 = Option::where('date', $date)
            ->where('resident', $id)
            ->where('option', 3)
            ->value('objectives');
        $pref_anest_id = Option::where('date', $date)
            ->where('resident', $id)
            ->where('option', 3)
            ->value('anesthesiologist_id');
        if ($pref_anest_id != null) {
            $pref_anest3 =
                Anesthesiologist::where('id', $pref_anest_id)->value('first_name') .
                ' ' .
                Anesthesiologist::where('id', $pref_anest_id)->value('last_name');
        } else {
            $pref_anest3 = 'No Preference';
        }

        if ($schedule1 != null) {
            $first_choice = self::processSingleChoice($schedule1);
            $first_choice = "First Choice: $first_choice\nMilestone: $milestone1C - $milestone1D\nObjective: $objective1\nAnesthesiologist Preference: $pref_anest1";
            $day_arr['first'] = $first_choice;
            $day_arr['ids'] = $schedule1 . '_';
        } else {
            $day_arr['ids'] = '0_';
        }

        if ($schedule2 != null) {
            $second_choice = self::processSingleChoice($schedule2);
            $second_choice = "\n\nSecond Choice: $second_choice\nMilestone: $milestone2C - $milestone2D\nObjective: $objective2\nAnesthesiologist Preference: $pref_anest2";
            $day_arr['second'] = $second_choice;
            $day_arr['ids'] .= $schedule2 . '_';
        } else {
            $day_arr['ids'] .= '0_';
        }

        if ($schedule3 != null) {
            $third_choice = self::processSingleChoice($schedule3);
            $third_choice = "\n\nThird Choice: $third_choice\nMilestone: $milestone3C - $milestone3D\nObjective: $objective3\nAnesthesiologist Preference: $pref_anest3";
            $day_arr['third'] = $third_choice;
            $day_arr['ids'] .= $schedule3 . '_';
        } else {
            $day_arr['ids'] .= '0_';
        }

        return $day_arr;
    }

    public function getIndex()
    {
        return view('schedules.index');
    }

    public function getAbout()
    {
        date_default_timezone_set('America/New_York');

        // Update user information here
        $name = $_SERVER['HTTP_DISPLAYNAME'];
        $email = $_SERVER['HTTP_EMAIL'];
        $roles = [];
        if (
            Admin::where('email', $email)
                ->where('exists', '1')
                ->exists()
        ) {
            array_push($roles, 'Admin');
        }
        if (
            Resident::where('email', $email)
                ->where('exists', '1')
                ->exists()
        ) {
            array_push($roles, 'Resident');
        }

        // Update user schedule here
        $id = Resident::where('email', $_SERVER['HTTP_EMAIL'])->value('id');
        $date = self::calculateFirst();
        $firstday = null;
        $assignment = Assignment::where('date', $date)->where('resident', $id);
        if ($assignment->exists()) {
            $firstday_schedule_id = Assignment::where('date', $date)
                ->where('resident', $id)
                ->value('schedule');
            $option = Option::where('id', $assignment->value('option'));
            $milestone = $option->value('milestones');
            $milestoneC = Milestone::where('id', $milestone)->value('category');
            $milestoneD = Milestone::where('id', $milestone)->value('detail');
            $objective = $option->value('objectives');

            $choice = self::processSingleChoice($firstday_schedule_id);
            $firstday = "$choice\nMilestone: $milestoneC - $milestoneD\nObjective: $objective";
        }

        $date = self::calculateSecond();
        $secondday = self::processChoices($date, $id);
        $ids = $secondday['ids'];

        $date = self::calculateThird();
        $thirdday = self::processChoices($date, $id);

        // Parse data into array
        $data = [
            'name' => $name,
            'email' => $email,
            'roles' => $roles,
            'firstday' => $firstday,
            'secondday' => $secondday,
            'thirdday' => $thirdday,
            'ids' => $ids,
        ];

        return view('pages.about', compact('data'));
    }

    public function postAnnouncement(Request $request)
    {
        $message = $request->message;
        if (strlen($message) > 0) {
            if (
                Admin::where('email', $_SERVER['HTTP_EMAIL'])
                    ->where('exists', '1')
                    ->exists()
            ) {
                $user_type = 1;
                $user_id = Admin::where('email', $_SERVER['HTTP_EMAIL'])->value('id');
            } elseif (
                Attending::where('email', $_SERVER['HTTP_EMAIL'])
                    ->where('exists', '1')
                    ->exists()
            ) {
                $user_type = 2;
                $user_id = Attending::where('email', $_SERVER['HTTP_EMAIL'])->value('id');
            } elseif (
                Resident::where('email', $_SERVER['HTTP_EMAIL'])
                    ->where('exists', '1')
                    ->exists()
            ) {
                $user_type = 3;
                $user_id = Resident::where('email', $_SERVER['HTTP_EMAIL'])->value('id');
            }

            Announcements::insert([
                'message' => $message,
                'user_type' => $user_type,
                'user_id' => $user_id,
                'parent_message_id' => $request->parent_message_id,
                'created_at' => Carbon::now(),
            ]);
        }

        return back();
        //return view('schedules.resident.messages');
    }

    public function deleteAnnouncement(Request $request)
    {
        Announcements::where('id', $request->message_id)->delete();

        return back();
        //return view('schedules.resident.messages');
    }

    public function getContact()
    {
        return view('pages.contact');
    }

    public function postContact(Request $request)
    {
        $this->validate($request, [
            'email' => 'required|email',
            'subject' => 'min:1',
            'body' => 'min:',
        ]);

        $data = [
            'email' => $request->email,
            'subject' => $request->subject,
            'bodyMessage' => $request->body,
        ];

        Mail::send('emails.contact', $data, function ($message) use ($data) {
            $message->from($data['email']);
            $message->to('David.Stahl@osumc.edu');
            $message->subject($data['subject']);
        });

        $request->session()->flash('success', 'Your message was sent!');

        return view('pages.contact');
    }

    public function getAcknowledgements()
    {
        return view('pages.acknowledgements');
    }

    public function getFeedback($date)
    {
        $year = substr($date, 0, 4);
        $mon = substr($date, 4, 2);
        $day = substr($date, 6, 2);
        $data_date = $year . '-' . $mon . '-' . $day;

        return view('pages.feedback', compact('data_date'));
    }

    public function test()
    {
        $parser = new EvaluationParser(date('omd', strtotime('today')), true);
        return 'test1';
    }
}
