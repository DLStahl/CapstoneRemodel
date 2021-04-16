<?php

namespace App\Http\Controllers;

class ResidentController extends Controller
{
    public function getIndex()
    {
        return view('schedules.resident.resident');
    }

    public function getInstructions()
    {
        return view('schedules.resident.instructions');
    }

    public function getSchedule()
    {
        return view('schedules.resident.schedule');
    }

    /**
     * Route to post messages page
     */
    public function getMessages()
    {
        return view('schedules.resident.messages');
    }
}
