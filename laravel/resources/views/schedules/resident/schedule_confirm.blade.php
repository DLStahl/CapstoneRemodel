@extends('main')
@section('content')

	<?php

		//echo var_dump($input);

	?>

	<h2>First Choice: </h2>

		<p>Date: {{ $schedule_data1[0]['date'] }}</p>
		<p>Location: {{ $schedule_data1[0]['location'] }}</p>
		<p>Room: {{ $schedule_data1[0]['room'] }}</p>
		<p>Start Time: {{ $schedule_data1[0]['start_time'] }}</p>
		<p>End Time: {{ $schedule_data1[0]['end_time'] }}</p>
		<p>Patient Class: {{ $schedule_data1[0]['patient_class'] }}</p>

	<h2>Second Choice: </h2>
		@if(is_null($schedule_data2))
			<p>None</p>
		@else
			<p>Date: {{ $schedule_data2[0]['date'] }}</p>
			<p>Location: {{ $schedule_data2[0]['location'] }}</p>
			<p>Room: {{ $schedule_data2[0]['room'] }}</p>
			<p>Start Time: {{ $schedule_data2[0]['start_time'] }}</p>
			<p>End Time: {{ $schedule_data2[0]['end_time'] }}</p>
			<p>Patient Class: {{ $schedule_data2[0]['patient_class'] }}</p>
		@endif

	<h2>Third Choice: </h2>
		@if(is_null($schedule_data3))
			<p>None</p>
		@else
			<p>Date: {{ $schedule_data3[0]['date'] }}</p>
			<p>Location: {{ $schedule_data3[0]['location'] }}</p>
			<p>Room: {{ $schedule_data3[0]['room'] }}</p>
			<p>Start Time: {{ $schedule_data3[0]['start_time'] }}</p>
			<p>End Time: {{ $schedule_data3[0]['end_time'] }}</p>
			<p>Patient Class: {{ $schedule_data1[0]['patient_class'] }}</p>
		@endif
    <br><br>

	<input align="left" type="button" value="Confirm" id="{{$input[0]['id'] }}_{{$input[1]['id'] }}_{{$input[2]['id'] }}" class='btn btn-md btn-success' onclick="confirmUpdate(this.id);">

    <script type="text/javascript">
    function confirmUpdate(id)
    {

        // Update url to the confirmation page
        var current_url = window.location.href;
        var url = current_url.substr(0, current_url.search('/schedule/'));
        if (current_url.includes('secondday')) {
            url = url + "/schedule/secondday/milestones/" + id;
        } else {
            url = url + "/schedule/thirdday/milestones/" + id;
        }
        window.location.href = url;
    }

    </script>
@endsection
