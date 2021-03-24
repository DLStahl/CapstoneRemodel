@extends('main')
@section('content')
	<!-- Use a hidden form to store selected schedule ids, milestones and education objectives. -->
<form method="POST" action="./submit">
	<input type="hidden" name="schedule_id" value="{{ $id }}">
	<div class="row">
		@if(!is_null($previousChoices[0]))
			<div class="col-12 col-md-6">
				<h3>Your Previous Selections</h3>
				@foreach ($previousChoices as $previousChoice)
					<h4>{{ $previousChoice['choiceType'] }} Choice: </h4>
					@if(is_null($previousChoice))
						<p>None</p>
					@else
						<p>Date: {{ $previousChoice['schedule'][0]['date'] }}</p>
						<p>Room: {{ $previousChoice['schedule'][0]['room'] }}</p>
						<p>Case Procedures:<br> {!! nl2br($previousChoice['case_procedure']) !!}</p>
						<p>Milestone:<br> {{ $previousChoice['milestone'][0]['category'] }} - {{$previousChoice['milestone'][0]['detail'] }}</p>
						<p>Objective:<br> {{ $previousChoice['objective'] }}</p>
						@if ($previousChoice['anesthesiologist_pref'] != '[]')
							<p>Preferred Anesthesiologist: <br> {{$previousChoice['anesthesiologist_pref'][0]['first_name']}} {{$previousChoice['anesthesiologist_pref'][0]['last_name']}}</p>	
						@else
							<p>Preferred Anesthesiologist: <br> No Preference </p>
						@endif
					@endif
				@endforeach
			</div>
			<br><br>
		@endif

		<div class="col-12 col-md-6">
			<h3>Your Current Selections</h3>
			@foreach($currentChoices as $currentChoice)
				<h4>{{ $currentChoice['choiceType'] }} Choice: </h4>
				@if(is_null($currentChoice))
					<p>None</p>
				@else
					<p>Date: {{ $currentChoice['schedule'][0]['date'] }}</p>
					<p>Room: {{ $currentChoice['schedule'][0]['room'] }}</p>
					<p>Case Procedures:<br> {!! nl2br($currentChoice['case_procedure']) !!}</p>
					<p>Milestone:<br> {{ $currentChoice['milestone'][0]['category'] }} - {{$currentChoice['milestone'][0]['detail'] }}</p>
					<p>Objective:<br> {{ $currentChoice['objective'] }}</p>
					@if ($currentChoice['anesthesiologist_pref'] != '[]') 
						<p>Preferred Anesthesiologist:<br> {{$currentChoice['anesthesiologist_pref'][0]['first_name'] }} {{ $currentChoice['anesthesiologist_pref'][0]['last_name']}}</p>
						<input type="hidden" name="pref_anest{{$loop->iteration }}" value="{{ $currentChoice['anesthesiologist_pref'][0]['id'] }}">
					@else
						<p> Preferred Anesthesiologist:<br> No Preference </p>
						<input type="hidden" name="pref_anest{{$loop->iteration}}" value=0>
					@endif
					<input type="hidden" name="option{{$loop->iteration}}" value="{{ $loop->iteration}}">
					<input type="hidden" name="milestones{{$loop->iteration}}" value="{{ $currentChoice['milestone'][0]['id'] }}">
					<input type="hidden" name="objectives{{$loop->iteration}}" value="{{ $currentChoice['objective'] }}">
					
				@endif
			@endforeach
		</div>
	</div>
	<input align = "left" type="submit" value="Confirm" class='btn btn-md btn-success'>
</form>
@endsection
