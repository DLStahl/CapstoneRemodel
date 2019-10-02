@extends('main')

@section('content')

	<div id = "Resident Form">
        <h4>Resident Preferences</h4><br>
        <form method="POST" action="../../confirm">
		<div class="form-group">
            <h5>Your 1st Preference: Room {{ $room1 }} with {{ $attending1 }} </h5>
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
								echo '<option value="'.$milestone_id.'" title="'.$title.'">'.$category.' - '.$detail.'</option>';
				            ?>
						@endforeach
					@endif
            </select>

            <br>

            <label>What is your educational objective for this OR today?</label><br>

            <textarea rows="3" name="objectives1" id="objectives1" class="form-control" required></textarea><br>


	        @if(is_null($room2))
				<h5>Your 2nd Preference: None</h5>
			@else
				<h5>Your 2nd Preference: Room {{ $room2 }} with {{ $attending2 }}</h5>
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
								echo '<option value="'.$milestone_id.'" title="'.$title.'">'.$category.' - '.$detail.'</option>';
							?>
						@endforeach
					@endif
                </select>

                <br>

                <label>What is your educational objective for this OR today?</label><br>

                <textarea rows="3" name="objectives2" id="objectives2" class="form-control" required></textarea><br>
	        @endif


	        @if(is_null($room3))
				<h5>Your 3nd Preference: None</h5>
			@else
				<h5>Your 3rd Preference: Room {{ $room3 }} with {{ $attending3 }}</h5>
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
								echo '<option value="'.$milestone_id.'" title="'.$title.'">'.$category.' - '.$detail.'</option>';
							?>
						@endforeach
					@endif
                </select>

                <br>

                <label>What is your educational objective for this OR today?</label><br>

                <textarea rows="3" name="objectives3" id="objectives3" class="form-control" required></textarea><br>

                <br>
	        @endif

            <input type="hidden" name="schedule_id" value="{{ $id }}">
			<input align = "right" type="submit" value="Next" class='btn btn-md btn-success'>
		</div>
        </form>
	</div>

@endsection
