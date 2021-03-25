@extends('main')
@section('content')
	<!-- Use a hidden form to store selected schedule ids, milestones, education objectives, and anest pref. -->
<form method="POST" action="./submit">
	<input type="hidden" name="schedule_id" value="{{ $id }}">
	<div class="row">
		@if(!is_null($previousChoices[0]))
			<div class="col-12 col-md">
				<h3>Your Previous Selections</h3>
			</div>
		@endif
		<div class="col-12 col-md">
			<h3>Your Current Selections</h3>
		</div>
	</div>
	@foreach($choiceTypes as $choiceType)
		<div class="row">
			@if(!is_null($previousChoices[0]))
				<div class="col-12 col-md">
					<h4>{{ $choiceType }} Choice: </h4>
					@if(is_null($previousChoices[$loop->iteration - 1]))
						<p>None</p>
					@else
						<p>Date: {{ $previousChoices[$loop->iteration - 1]['schedule'][0]['date'] }}</p>
						<p>Room: {{ $previousChoices[$loop->iteration - 1]['schedule'][0]['room'] }}</p>
						<p>Case Procedures:<br> {!! nl2br($previousChoices[$loop->iteration - 1]['case_procedure']) !!}</p>
						<p>Milestone:<br> {{ $previousChoices[$loop->iteration - 1]['milestone'][0]['category'] }} - {{$previousChoices[$loop->iteration - 1]['milestone'][0]['detail'] }}</p>
						<p>Objective:<br> {{ $previousChoices[$loop->iteration - 1]['objective'] }}</p>
						@if ($previousChoices[$loop->iteration - 1]['anesthesiologist_pref'] != '[]')
							<p>Preferred Anesthesiologist: <br> {{$previousChoices[$loop->iteration - 1]['anesthesiologist_pref'][0]['first_name']}} {{$previousChoices[$loop->iteration - 1]['anesthesiologist_pref'][0]['last_name']}}</p>	
						@else
							<p>Preferred Anesthesiologist: <br> No Preference </p>
						@endif
					@endif
				</div>
			@endif
			<div class="col-12 col-md">
				<h4>{{ $choiceType }} Choice: </h4>
				@if(is_null($currentChoices[$loop->iteration - 1]))
					<p>None</p>
				@else
					<p>Date: {{ $currentChoices[$loop->iteration - 1]['schedule'][0]['date'] }}</p>
					<p>Room: {{ $currentChoices[$loop->iteration - 1]['schedule'][0]['room'] }}</p>
					<p>Case Procedures:<br> {!! nl2br($currentChoices[$loop->iteration - 1]['case_procedure']) !!}</p>
					<p>Milestone:<br> {{ $currentChoices[$loop->iteration - 1]['milestone'][0]['category'] }} - {{$currentChoices[$loop->iteration - 1]['milestone'][0]['detail'] }}</p>
					<p>Objective:<br> {{ $currentChoices[$loop->iteration - 1]['objective'] }}</p>
					@if ($currentChoices[$loop->iteration - 1]['anesthesiologist_pref'] != '[]') 
						<p>Preferred Anesthesiologist:<br> {{$currentChoices[$loop->iteration - 1]['anesthesiologist_pref'][0]['first_name'] }} {{ $currentChoices[$loop->iteration - 1]['anesthesiologist_pref'][0]['last_name']}}</p>
						<input type="hidden" name="pref_anest{{$loop->iteration}}" value="{{ $currentChoices[$loop->iteration - 1]['anesthesiologist_pref'][0]['id'] }}">
					@else
						<p> Preferred Anesthesiologist:<br> No Preference </p>
						<input type="hidden" name="pref_anest{{$loop->iteration}}" value=0>
					@endif
					<input type="hidden" name="option{{$loop->iteration}}" value="{{$loop->iteration}}">
					<input type="hidden" name="milestones{{$loop->iteration}}" value="{{ $currentChoices[$loop->iteration - 1]['milestone'][0]['id'] }}">
					<input type="hidden" name="objectives{{$loop->iteration}}" value="{{ $currentChoices[$loop->iteration - 1]['objective'] }}">
					
				@endif
			</div>
		</div>
	@endforeach
	<input type="submit" value="Confirm" class='btn btn-md btn-success'>
</form>
@endsection
