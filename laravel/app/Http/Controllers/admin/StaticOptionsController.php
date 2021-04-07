<?php

namespace App\Http\Controllers\admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Models\ScheduleDataStatic;

class StaticOptionsController extends Controller
{
    public function viewPage()
    {
        $table = new ScheduleDataStatic();

        $columns = $table->getTableColumns();

        $data = ScheduleDataStatic::select("*")->get();

        $primaryKeyField = $table->getKeyName();

        return view("crud.databasetable", compact("columns", "data", "primaryKeyField"));
    }
}
