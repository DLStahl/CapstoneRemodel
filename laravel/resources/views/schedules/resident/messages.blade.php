<?php
	use App\Resident;
	use App\Admin;
	use App\Announcements;

	$super_access = false;
	if (Admin::where('email', $_SERVER['HTTP_EMAIL'])->where('exists','1')->exists()) {
		$super_access = true;
	}
	$past_announcements = Announcements::orderBy('created_at', 'DESC')->get();
	$possible_responses = Announcements::orderBy('created_at', 'ASC')->get();
?>
@extends('main')
@section('content')
<div class="row">
    <div class="col-md-12">
		@if($super_access)
            <hr>
            <h2>
            <form action="announcement" method ="POST">
                {{ csrf_field() }}
                <div class="form-group">
                    <label name="message">New Announcement:</label>
                    <textarea id="message" name="message" class="form-control" placeholder="Type your message here..." /></textarea>
					<input type="hidden" id="parent_message_id" name="parent_message_id" value="-1">
                </div>
                <input type="submit" value="Send Message" class="btn btn-success">
            </form>
            </h2>
		@endif
        <hr>
        <h2>
			<label name="message">Past Announcements:</label>
		</h2>
		@foreach ($past_announcements as $past_announcement)
			@if($past_announcement->parent_message_id == -1)
				<div class="row">
					<div class="col-md-11">
						<?php
							$submitter = '';
							if($past_announcement->user_type == 1) {
								$submitter = Admin::where('id', $past_announcement->user_id)->value('name');
							} elseif ($past_announcement->user_type == 2) {
								$submitter = Attending::where('id', $past_announcement->user_id)->value('name');
							} elseif ($past_announcement->user_type == 3) {
								$submitter = Resident::where('id', $past_announcement->user_id)->value('name');
							}
						?>
						<h4>{{$past_announcement->message}}</h4>
						<p>{{$submitter}} on {{$past_announcement->created_at}}</p>
					</div>
					<div class="col-md-1">
						@if($super_access)
							<form action="deleteannouncement" method ="POST">
								<input type="submit" value="DELETE" class="btn btn-warning">
								<input type="hidden" id="message_id" name="message_id" value="{{$past_announcement->id}}">
							</form>
						@endif
					</div>
				</div>
				<form action="announcement" method ="POST">
					{{ csrf_field() }}
					<div class="form-group">
						<textarea id="message" name="message" class="form-control" placeholder="Type your response to the above announcement or the comments below here..." /></textarea>
						<input type="hidden" id="parent_message_id" name="parent_message_id" value="{{$past_announcement->id}}">
					</div>
					<input type="submit" value="Respond" class="btn btn-success">
				</form>
				<br>
				@foreach ($possible_responses as $past_announcement_response)
					@if($past_announcement_response->parent_message_id == $past_announcement->id)
						<div class="row">
							<div class="col-md-1"></div>
							<div class="col-md-10">
								<?php
									$submitter2 = '';
									if($past_announcement_response->user_type == 1) {
										$submitter2 = Admin::where('id', $past_announcement_response->user_id)->value('name');
									} elseif ($past_announcement_response->user_type == 2) {
										$submitter2 = Attending::where('id', $past_announcement_response->user_id)->value('name');
									} elseif ($past_announcement_response->user_type == 3) {
										$submitter2 = Resident::where('id', $past_announcement_response->user_id)->value('name');
									}
								?>	
								<p>{{$past_announcement_response->message}}<br>{{$submitter2}} on {{$past_announcement_response->created_at}}</p>
								<hr>
							</div>
							<div class="col-md-1">
								@if($super_access)
									<form action="deleteannouncement" method ="POST">
										<input type="submit" value="DELETE" class="btn btn-warning">
										<input type="hidden" id="message_id" name="message_id" value="{{$past_announcement_response->id}}">
									</form>
								@endif
							</div>
						</div>
					@endif
				@endforeach
				<hr>
			@endif
		@endforeach
    </div>
</div>
@endsection