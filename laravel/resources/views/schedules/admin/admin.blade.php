@extends('main')

@section('content')
	<a class="btn btn-primary osu-grey" href="admin/users">Edit Users</a>	
    <a class="btn btn-primary" href="admin/schedules">Edit Schedules</a>
    <a class="btn btn-primary" href="admin/milestones">Edit Milestones</a>
    <a class="btn btn-primary" href="admin/download">Download Data Sheets</a>
    <a class="btn btn-primary" href="admin/resetTickets">Reset Tickets</a>
    <a class="btn btn-primary" href="admin/evaluation">View Resident/Attending Pairings</a>
	<a class="btn btn-primary" href="admin/uploadForm">Upload Schedule</a>
    <a class="btn btn-primary" href="admin/medhubtest">MedHub Test</a>
    <a class="btn btn-primary" href="admin/filterrotation">Filter Rotations</a>
@endsection