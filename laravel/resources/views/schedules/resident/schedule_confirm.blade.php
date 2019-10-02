@extends('main')
@section('content')

	<form method="POST" action="./submit">
		<input type="hidden" name="schedule_id" value="{{ $id }}">

		@if(!is_null($previous[0]))
			<h2>Your Previous Selections</h2>
			<h4>First Choice: </h4>
			<p>Date: {{ $previous[0]['schedule'][0]['date'] }}</p>
			<p>Location: {{ $previous[0]['schedule'][0]['location'] }}</p>
			<p>Room: {{ $previous[0]['schedule'][0]['room'] }}</p>
			<p>Start Time: {{ $previous[0]['schedule'][0]['start_time'] }}</p>
			<p>End Time: {{ $previous[0]['schedule'][0]['end_time'] }}</p>
		    <p>Milestone: {{ $previous[0]['milestone'][0]['category'] }} - {{$previous[0]['milestone'][0]['detail'] }}</p>
		    <p>Objective: {{ $previous[0]['prevPref'][0]['objectives'] }}</p>


			<h4>Second Choice: </h4>
		    @if(is_null($previous[1]))
				<p>None</p>
			@else
				<p>Date: {{ $previous[1]['schedule'][0]['date'] }}</p>
				<p>Location: {{ $previous[1]['schedule'][0]['location'] }}</p>
				<p>Room: {{ $previous[1]['schedule'][0]['room'] }}</p>
				<p>Start Time: {{ $previous[1]['schedule'][0]['start_time'] }}</p>
				<p>End Time: {{ $previous[1]['schedule'][0]['end_time'] }}</p>
			    <p>Milestone: {{ $previous[1]['milestone'][0]['category'] }} - {{$previous[1]['milestone'][0]['detail'] }}</p>
			    <p>Objective: {{ $previous[1]['prevPref'][0]['objectives'] }}</p>
			@endif


			<h4>Third Choice: </h4>
			@if(is_null($previous[2]))
				<p>None</p>
			@else
				<p>Date: {{ $previous[2]['schedule'][0]['date'] }}</p>
				<p>Location: {{ $previous[2]['schedule'][0]['location'] }}</p>
				<p>Room: {{ $previous[2]['schedule'][0]['room'] }}</p>
				<p>Start Time: {{ $previous[2]['schedule'][0]['start_time'] }}</p>
				<p>End Time: {{ $previous[2]['schedule'][0]['end_time'] }}</p>
			    <p>Milestone: {{ $previous[2]['milestone'][0]['category'] }} - {{$previous[2]['milestone'][0]['detail'] }}</p>
			    <p>Objective: {{ $previous[2]['prevPref'][0]['objectives'] }}</p>
		    	
			@endif

		@endif

	<br><br>
	<h2>Your Current Selections</h2>


	<h4>First Choice: </h4>

		<p>Date: {{ $input[0]['schedule'][0]['date'] }}</p>
		<p>Location: {{ $input[0]['schedule'][0]['location'] }}</p>
		<p>Room: {{ $input[0]['schedule'][0]['room'] }}</p>
		<p>Start Time: {{ $input[0]['schedule'][0]['start_time'] }}</p>
		<p>End Time: {{ $input[0]['schedule'][0]['end_time'] }}</p>
		<p>Patient Class: {{ $input[0]['schedule'][0]['patient_class'] }}</p>

	    <p>Milestone: {{ $input[0]['milestones'][0]['category'] }} - {{ $input[0]['milestones'][0]['detail'] }}</p>
	    <p>Objective: {{ $input[0]['objectives'] }}</p>

		<input type="hidden" name="option1" value="{{ $input[0]['choice'] }}">
		<input type="hidden" name="milestones1" value="{{ $input[0]['milestones'][0]['id'] }}">
		<input type="hidden" name="objectives1" value="{{ $input[0]['objectives'] }}">


	<h4>Second Choice: </h4>
		@if(is_null($input[1]))
			<p>None</p>
		@else
			<p>Date: {{ $input[1]['schedule'][0]['date'] }}</p>
			<p>Location: {{ $input[1]['schedule'][0]['location'] }}</p>
			<p>Room: {{ $input[1]['schedule'][0]['room'] }}</p>
			<p>Start Time: {{ $input[1]['schedule'][0]['start_time'] }}</p>
			<p>End Time: {{ $input[1]['schedule'][0]['end_time'] }}</p>
			<p>Patient Class: {{ $input[1]['schedule'][0]['patient_class'] }}</p>

		    <p>Milestone: {{ $input[1]['milestones'][0]['category'] }} - {{ $input[1]['milestones'][0]['detail'] }}</p>
			<p> Objective: {{ $input[1]['objectives'] }}</p>

			<input type="hidden" name="option2" value="{{ $input[1]['choice']}}">
			<input type="hidden" name="milestones2" value="{{ $input[1]['milestones'][0]['id']}}">
			<input type="hidden" name="objectives2" value="{{ $input[1]['objectives']}}">

		@endif

	<h4>Third Choice: </h4>
		@if(is_null($input[2]))
			<p>None</p>
		@else
			<p>Date: {{ $input[2]['schedule'][0]['date'] }}</p>
			<p>Location: {{ $input[2]['schedule'][0]['location'] }}</p>
			<p>Room: {{ $input[2]['schedule'][0]['room'] }}</p>
			<p>Start Time: {{ $input[2]['schedule'][0]['start_time'] }}</p>
			<p>End Time: {{ $input[2]['schedule'][0]['end_time'] }}</p>
			<p>Patient Class: {{ $input[2]['schedule'][0]['patient_class'] }}</p>

		    <p>Milestone: {{ $input[2]['milestones'][0]['category'] }} - {{ $input[2]['milestones'][0]['detail'] }}</p>
			<p> Objective: {{ $input[2]['objectives'] }}</p>
			<input type="hidden" name="option3" value="{{ $input[2]['choice']}}">
			<input type="hidden" name="milestones3" value="{{ $input[2]['milestones'][0]['id']}}">
			<input type="hidden" name="objectives3" value="{{ $input[2]['objectives']}}">

		@endif
	    <br><br>

		<input align = "left" type="submit" value="Confirm" class='btn btn-md btn-success'>

	</form>
@endsection
