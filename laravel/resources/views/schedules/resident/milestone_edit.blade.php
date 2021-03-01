@extends('main')

@section('content')

	<div id = "Resident Form">
        <h4>Resident Preferences</h4><br>
        <form method="POST" action="../confirm">
		<div class="form-group">
			<!-- TODO: Use a for loop -->
            <h5>Your 1st Preference: Room {{ $data1['schedule']['room'] }} with {{ $data1['attending'] }} </h5>
            <label>Select your Milestone:</label><br>

            <select name="milestones1" id="milestones1" required>
                <option value="" selected> -- Select a Milestone -- </option>
					@if(!is_null($milestones))
						@foreach ($milestones as $milestone)
							<?php
								$milestone_id=$milestone['id'];
								$category = $milestone['category'];
								$title = $milestone['title'];
								$detail = $milestone['detail'];
								if ($milestone_id == $data1['milestone']['id']){
									echo '<option value="'.$milestone_id.'" title="'.$title.'" selected>'.$category.' - '.$detail.'</option>';
								} else {
									echo '<option value="'.$milestone_id.'" title="'.$title.'">'.$category.' - '.$detail.'</option>';
								}
				            ?>
						@endforeach
					@endif
            </select>

            <br>

            <label>What is your educational objective for this OR today?</label><br>

            <textarea rows="3" name="objectives1" id="objectives1" class="form-control" required>{{ $data1['objective'] }}</textarea>
			
			<div id="anesthesiologist_preference">
				<label>Anesthesiologist Preference:</label>
				<br>
				<select name = "prefAnest1" id = "prefAnest1" class="PreferenceSelector">
					<option selected="selected">No Preference</option>
					@foreach($anesthesiologists as $a)
						<option value="{{ $a->id }}">Dr. {{ $a->first_name }} {{ $a->last_name }}</option>
					@endforeach
				</select>
			</div>
			<br>

	        @if(is_null($data2['schedule']))
				<h5>Your 2nd Preference: None</h5>
			@else
				<h5>Your 2nd Preference: Room {{ $data2['schedule']['room'] }} with {{ $data2['attending'] }}</h5>
                <label>Select your Milestone:</label><br>

                <select name="milestones2" id="milestones2" required>
					<option value="" selected> -- Select a Milestone -- </option>
					@if(!is_null($milestones))
						@foreach ($milestones as $milestone)
							<?php
								$milestone_id=$milestone['id'];
								$category = $milestone['category'];
								$title = $milestone['title'];
								$detail = $milestone['detail'];
								if ($milestone_id == $data2['milestone']['id']){
									echo '<option value="'.$milestone_id.'" title="'.$title.'" selected>'.$category.' - '.$detail.'</option>';
								} else {
									echo '<option value="'.$milestone_id.'" title="'.$title.'">'.$category.' - '.$detail.'</option>';
								}
							?>
						@endforeach
					@endif
                </select>

                <br>

                <label>What is your educational objective for this OR today?</label><br>

                <textarea rows="3" name="objectives2" id="objectives2" class="form-control" required>{{ $data2['objective'] }}</textarea>

				<div id="anesthesiologist_preference">
					<label>Anesthesiologist Preference:</label>
					<br>
					<select name = "prefAnest2" id = "prefAnest2" class="PreferenceSelector">
						<option selected="selected">No Preference</option>
						@foreach($anesthesiologists as $a)
							<option value="{{ $a->id }}">Dr. {{ $a->first_name }} {{ $a->last_name }}</option>
						@endforeach
					</select>
				</div>
				<br>
	        @endif


	        @if(is_null($data3['schedule']))
				<h5>Your 3rd Preference: None</h5>
			@else
				<h5>Your 3rd Preference: Room {{ $data3['schedule']['room'] }} with {{ $data3['attending'] }}</h5>
                <label>Select your Milestone:</label><br>

                <select name="milestones3" id="milestones3" required>
					<option value="" selected> -- Select a Milestone -- </option>
					@if(!is_null($milestones))
						@foreach ($milestones as $milestone)
							<?php
								$milestone_id=$milestone['id'];
								$category = $milestone['category'];
								$title = $milestone['title'];
								$detail = $milestone['detail'];
								if ($milestone_id == $data3['milestone']['id']){
									echo '<option value="'.$milestone_id.'" title="'.$title.'" selected>'.$category.' - '.$detail.'</option>';
								} else {
									echo '<option value="'.$milestone_id.'" title="'.$title.'">'.$category.' - '.$detail.'</option>';
								}
							?>
						@endforeach
					@endif
                </select>

                <br>

                <label>What is your educational objective for this OR today?</label><br>

                <textarea rows="3" name="objectives3" id="objectives3" class="form-control" required>{{$data3['objective']}}</textarea>

				<div id="anesthesiologist_preference">
					<label>Anesthesiologist Preference:</label>
					<br>
					<select name = "prefAnest3" id = "prefAnest3" class="PreferenceSelector">
						<option selected="selected">No Preference</option>
						@foreach($anesthesiologists as $a)
							<option value="{{ $a->id }}">Dr. {{ $a->first_name }} {{ $a->last_name }}</option>
						@endforeach
					</select>
				</div>
				<br>
	        @endif

            <input type="hidden" name="schedule_id" value="{{ $id }}">
			<input align = "right" type="submit" value="Next" class='btn btn-md btn-success'>
		</div>
        </form>
	</div>

@endsection
