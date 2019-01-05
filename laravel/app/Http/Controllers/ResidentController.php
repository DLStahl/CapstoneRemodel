<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Admin;
use App\Resident;

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
    
}
