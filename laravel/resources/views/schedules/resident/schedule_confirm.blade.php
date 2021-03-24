@extends('main')
@section('content')
	<!-- Use a hidden form to store selected schedule ids, milestones, education objectives, and anest pref. -->
<form method="POST" action="./submit">
	<input type="hidden" name="schedule_id" value="{{ $id }}">
	<div class="row">
		@if(!is_null($previousChoices[0]))
			<div class="col">
				<h3>Your Previous Selections</h3>
			</div>
		@endif
		<div class="col">
			<h3>Your Current Selections</h3>
		</div>
	</div>
	@for($i = 0; $i < 3; $i++)
		<div class="row">
			<!-- Previous Choice -->
			@if(!is_null($previousChoices[0]))
				<div class="col">
					<h4>{{ $choiceTypes[$i] }} Choice: </h4>
					@if(is_null($previousChoices[$i]))
						<p>None</p>
					@else
						<p>Date: {{ $previousChoices[$i]['schedule'][0]['date'] }}</p>
						<p>Room: {{ $previousChoices[$i]['schedule'][0]['room'] }}</p>
						<p>Case Procedures:<br> {!! nl2br($previousChoices[$i]['case_procedure']) !!}</p>
						<p>Milestone:<br> {{ $previousChoices[$i]['milestone'][0]['category'] }} - {{$previousChoices[$i]['milestone'][0]['detail'] }}</p>
						<p>Objective:<br> {{ $previousChoices[$i]['objective'] }}</p>
						@if ($previousChoices[$i]['anesthesiologist_pref'] != '[]')
							<p>Preferred Anesthesiologist: <br> {{$previousChoices[$i]['anesthesiologist_pref'][0]['first_name']}} {{$previousChoices[$i]['anesthesiologist_pref'][0]['last_name']}}</p>	
						@else
							<p>Preferred Anesthesiologist: <br> No Preference </p>
						@endif
					@endif
				</div>
			@endif
			<!-- Current Choice -->
			<div class="col">
				<h4>{{ $choiceTypes[$i]  }} Choice: </h4>
				@if(is_null($currentChoices[$i]))
					<p>None</p>
				@else
					<p>Date: {{ $currentChoices[$i]['schedule'][0]['date'] }}</p>
					<p>Room: {{ $currentChoices[$i]['schedule'][0]['room'] }}</p>
					<p>Case Procedures:<br> {!! nl2br($currentChoices[$i]['case_procedure']) !!}</p>
					<p>Milestone:<br> {{ $currentChoices[$i]['milestone'][0]['category'] }} - {{$currentChoices[$i]['milestone'][0]['detail'] }}</p>
					<p>Objective:<br> {{ $currentChoices[$i]['objective'] }}</p>
					@if ($currentChoices[$i]['anesthesiologist_pref'] != '[]') 
						<p>Preferred Anesthesiologist:<br> {{$currentChoices[$i]['anesthesiologist_pref'][0]['first_name'] }} {{ $currentChoices[$i]['anesthesiologist_pref'][0]['last_name']}}</p>
						<input type="hidden" name="pref_anest{{$i+1}}" value="{{ $currentChoices[$i]['anesthesiologist_pref'][0]['id'] }}">
					@else
						<p> Preferred Anesthesiologist:<br> No Preference </p>
						<input type="hidden" name="pref_anest{{$i+1}}" value=0>
					@endif
					<input type="hidden" name="option{{$i+1}}" value="{{$i+1}}">
					<input type="hidden" name="milestones{{$i+1}}" value="{{ $currentChoices[$i]['milestone'][0]['id'] }}">
					<input type="hidden" name="objectives{{$i+1}}" value="{{ $currentChoices[$i]['objective'] }}">
					
				@endif
			</div>
		</div>
	@endfor
	<input type="submit" value="Confirm" class='btn btn-md btn-success'>
</form>
@endsection
