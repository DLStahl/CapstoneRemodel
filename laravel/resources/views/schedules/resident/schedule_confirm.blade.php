@extends('main')
@section('content')
	<!-- Use a hidden form to store selected schedule ids, milestones and education objectives. -->
	<form method="POST" action="./submit">
		<input type="hidden" name="schedule_id" value="{{ $id }}">
	<div class="row">
		@if(!is_null($previous[0]))
		<div class="col-12 col-md-6">
			<h3>Your Previous Selections</h3>
			<h4>First Choice: </h4>
			<p>Date: {{ $previous[0]['schedule'][0]['date'] }}</p>
			<p>Room: {{ $previous[0]['schedule'][0]['room'] }}</p>
			<p>Case Procedures:<br><?php
				$case_procedure = $previous[0]['schedule'][0]['case_procedure'];
				$case_procedure = preg_replace('/[0-9]+/', '', $case_procedure);
				$case_procedure = preg_replace('/[:\/]/', '', $case_procedure);
				$case_procedure = preg_replace('/\(|\)/', '', $case_procedure);
				$case_procedure = str_replace(' [','',$case_procedure);
				$case_procedure = str_replace(array('[',']'),'',$case_procedure);
				echo nl2br($case_procedure);
			?>
			</p>
		    <p>Milestone:<br> {{ $previous[0]['milestone'][0]['category'] }} - {{$previous[0]['milestone'][0]['detail'] }}</p>
		    <p>Objective:<br> {{ $previous[0]['prevPref'][0]['objectives'] }}</p>
			@if ($previous[0]['pref_anest'] != '[]') 
				<p>Preferred Anesthesiologist: <br> {{$previous[0]['pref_anest'][0]['first_name']}} {{$previous[0]['pref_anest'][0]['last_name']}}</p>
				@else 
				<p>Preferred Anesthesiologist: <br> No Preference </p>
				@endif
							


			<h4>Second Choice: </h4>
		    @if(is_null($previous[1]))
				<p>None</p>
			@else
				<p>Date: {{ $previous[1]['schedule'][0]['date'] }}</p>
				<p>Room: {{ $previous[1]['schedule'][0]['room'] }}</p>
				<p>Case Procedures:<br><?php
					$case_procedure = $previous[1]['schedule'][0]['case_procedure'];
					$case_procedure = preg_replace('/[0-9]+/', '', $case_procedure);
					$case_procedure = preg_replace('/[:\/]/', '', $case_procedure);
					$case_procedure = preg_replace('/\(|\)/', '', $case_procedure);
					$case_procedure = str_replace(' [','',$case_procedure);
					$case_procedure = str_replace(array('[',']'),'',$case_procedure);
					echo nl2br($case_procedure);
				?>
				</p>
			    <p>Milestone:<br> {{ $previous[1]['milestone'][0]['category'] }} - {{$previous[1]['milestone'][0]['detail'] }}</p>
			    <p>Objective:<br> {{ $previous[1]['prevPref'][0]['objectives'] }}</p>
				@if ($previous[1]['pref_anest'] != '[]')
				<p>Preferred Anesthesiologist: <br> {{$previous[1]['pref_anest'][0]['first_name']}} {{$previous[1]['pref_anest'][0]['last_name']}}</p>
				@else 
				<p>Preferred Anesthesiologist: <br> No Preference </p>
				@endif
			@endif


			<h4>Third Choice: </h4>
			@if(is_null($previous[2]))
				<p>None</p>
			@else
				<p>Date: {{ $previous[2]['schedule'][0]['date'] }}</p>
				<p>Room: {{ $previous[2]['schedule'][0]['room'] }}</p>
				<p>Case Procedures:<br><?php
					$case_procedure = $previous[2]['schedule'][0]['case_procedure'];
					$case_procedure = preg_replace('/[0-9]+/', '', $case_procedure);
					$case_procedure = preg_replace('/[:\/]/', '', $case_procedure);
					$case_procedure = preg_replace('/\(|\)/', '', $case_procedure);
					$case_procedure = str_replace(' [','',$case_procedure);
					$case_procedure = str_replace(array('[',']'),'',$case_procedure);
					echo nl2br($case_procedure);
				?>
				</p>
			    <p>Milestone:<br> {{ $previous[2]['milestone'][0]['category'] }} - {{$previous[2]['milestone'][0]['detail'] }}</p>
			    <p>Objective:<br> {{ $previous[2]['prevPref'][0]['objectives'] }}</p>
				@if ($previous[2]['pref_anest'] != '[]')
				<p>Preferred Anesthesiologist: <br> {{$previous[2]['pref_anest'][0]['first_name']}} {{$previous[2]['pref_anest'][0]['last_name']}}</p>
				@else 
				<p>Preferred Anesthesiologist: <br> No Preference </p>
				@endif

		    	
			@endif
		</div>
		<br><br>
		@endif

<div class="col-12 col-md-6">
	<h3>Your Current Selections</h3>
	<h4>First Choice: </h4>
		<p>Date: {{ $input[0]['schedule'][0]['date'] }}</p>
		<p>Room: {{ $input[0]['schedule'][0]['room'] }}</p>
		<p>Case Procedures:<br><?php
			$case_procedure = $input[0]['schedule'][0]['case_procedure'];
			$case_procedure = preg_replace('/[0-9]+/', '', $case_procedure);
			$case_procedure = preg_replace('/[:\/]/', '', $case_procedure);
			$case_procedure = preg_replace('/\(|\)/', '', $case_procedure);
			$case_procedure = str_replace(' [','',$case_procedure);
			$case_procedure = str_replace(array('[',']'),'',$case_procedure);
			echo nl2br($case_procedure);
		?>
		</p>
	    <p>Milestone:<br> {{ $input[0]['milestones'][0]['category'] }} - {{ $input[0]['milestones'][0]['detail'] }}</p>
	    <p>Objective:<br> {{ $input[0]['objectives'] }}</p>
		@if ($_REQUEST['pref_anest1'] != "No Preference") 
		<p>Preferred Anesthesiologist:<br> {{$input[0]['pref_anest'][0]['first_name'] }} {{ $input[0]['pref_anest'][0]['last_name']}}</p>
			@else
			<p> Preferred Anesthesiologist:<br> No Preference </p>
			@endif

		<input type="hidden" name="option1" value="{{ $input[0]['choice'] }}">
		<input type="hidden" name="milestones1" value="{{ $input[0]['milestones'][0]['id'] }}">
		<input type="hidden" name="objectives1" value="{{ $input[0]['objectives'] }}">
		@if ($_REQUEST['pref_anest1'] != "No Preference")
				<input type="hidden" name="pref_anest1" value="{{ $input[0]['pref_anest'][0]['id'] }}">
			@else 
				<input type="hidden" name="pref_anest1" value=0>
			@endif



	<h4>Second Choice: </h4>
		@if(is_null($input[1]))
			<p>None</p>
		@else
			<p>Date: {{ $input[1]['schedule'][0]['date'] }}</p>
			<p>Room: {{ $input[1]['schedule'][0]['room'] }}</p>
			<p>Case Procedures:<br><?php
				$case_procedure = $input[1]['schedule'][0]['case_procedure'];
				$case_procedure = preg_replace('/[0-9]+/', '', $case_procedure);
				$case_procedure = preg_replace('/[:\/]/', '', $case_procedure);
				$case_procedure = preg_replace('/\(|\)/', '', $case_procedure);
				$case_procedure = str_replace(' [','',$case_procedure);
				$case_procedure = str_replace(array('[',']'),'',$case_procedure);
				echo nl2br($case_procedure);
			?>
			</p>
		    <p>Milestone:<br> {{ $input[1]['milestones'][0]['category'] }} - {{ $input[1]['milestones'][0]['detail'] }}</p>
			<p> Objective:<br> {{ $input[1]['objectives'] }}</p>
			@if ($_REQUEST['pref_anest2'] != "No Preference") 
			<p>Preferred Anesthesiologist:<br> {{$input[1]['pref_anest'][0]['first_name'] }} {{ $input[1]['pref_anest'][0]['last_name']}}</p>
			@else
			<p> Preferred Anesthesiologist:<br> No Preference </p>
			@endif
			
			<!-- Put the preffered Anesthesiologist here --> 

			<input type="hidden" name="option2" value="{{ $input[1]['choice']}}">
			<input type="hidden" name="milestones2" value="{{ $input[1]['milestones'][0]['id']}}">
			<input type="hidden" name="objectives2" value="{{ $input[1]['objectives']}}">
			@if ($_REQUEST['pref_anest2'] != "No Preference")
				<input type="hidden" name="pref_anest2" value="{{ $input[1]['pref_anest'][0]['id'] }}">
			@else 
				<input type="hidden" name="pref_anest2" value=0>
			@endif


		@endif

	<h4>Third Choice: </h4>
		@if(is_null($input[2]))
			<p>None</p>
		@else
			<p>Date: {{ $input[2]['schedule'][0]['date'] }}</p>
			<p>Room: {{ $input[2]['schedule'][0]['room'] }}</p>
			<p>Case Procedures:<br><?php
				$case_procedure = $input[2]['schedule'][0]['case_procedure'];
				$case_procedure = preg_replace('/[0-9]+/', '', $case_procedure);
				$case_procedure = preg_replace('/[:\/]/', '', $case_procedure);
				$case_procedure = preg_replace('/\(|\)/', '', $case_procedure);
				$case_procedure = str_replace(' [','',$case_procedure);
				$case_procedure = str_replace(array('[',']'),'',$case_procedure);
				echo nl2br($case_procedure);
			?>
			</p>
		    <p>Milestone:<br> {{ $input[2]['milestones'][0]['category'] }} - {{ $input[2]['milestones'][0]['detail'] }}</p>
			<p> Objective:<br> {{ $input[2]['objectives'] }}</p>
			@if ($_REQUEST['pref_anest3'] != "No Preference") 
			<p>Preferred Anesthesiologist:<br> {{$input[2]['pref_anest'][0]['first_name'] }} {{ $input[2]['pref_anest'][0]['last_name']}}</p>
			@else
			<p> Preferred Anesthesiologist:<br> No Preference </p>
			@endif

			
			<input type="hidden" name="option3" value="{{ $input[2]['choice']}}">
			<input type="hidden" name="milestones3" value="{{ $input[2]['milestones'][0]['id']}}">
			<input type="hidden" name="objectives3" value="{{ $input[2]['objectives']}}">
			@if ($_REQUEST['pref_anest3'] != "No Preference")
				<input type="hidden" name="pref_anest3" value="{{ $input[2]['pref_anest'][0]['id'] }}">
			@else 
				<input type="hidden" name="pref_anest3" value=0>
			@endif

		@endif
	    <br><br>
</div>
</div>
		<input align = "left" type="submit" value="Confirm" class='btn btn-md btn-success'>

	</form>
@endsection
