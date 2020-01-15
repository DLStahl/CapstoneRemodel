@extends('main')
@section('content')
	<h3>Hello, <?php echo $_SERVER["HTTP_DISPLAYNAME"]; ?>,</h3>

	<p>REMODEL (REsident MilestOne-baseD Educational Learning) is a system designed by David Stahl, MD (Associate Residency Program Director) in collaboration
		with the CAPSTONE Teams from The Ohio State University (OSU) Department of Computer Science & Engineering, and in conjunction with leadership from
		OSU Department Anesthesiology for the benefit of our anesthesiology residents.</p>


	<?php
		use App\Resident;
		use App\Admin;

		$super_access = false;
		$access = false;
		if (Admin::where('email', $_SERVER['HTTP_EMAIL'])->where('exists','1')->exists()) {
			$super_access = true;
			$access = true;
		} else if (Resident::where('email', $_SERVER['HTTP_EMAIL'])->where('exists','1')->exists()) {
			$access = true;
		}
	?>

	<br>

	<button class="btn btn-primary" onclick="resident({{$access}})">Resident Page</button>

	<button class="btn btn-primary" onclick="admin({{$super_access}})">Admin Page</button>

	<br>

	<!-- <br><br>
	<a class="btn btn-primary" href="resident">Resident Page</a>
	<br><br>
	<a class="btn btn-primary" href="admin">Admin Page</a> -->

	<script type="text/javascript">
        
		function resident(access)
        {
            if(access){
                window.location.href = "resident";

            } else alert('You must be a resident registered with this site to access this feature, please use the contact us link if you believe you should have access');


        }

    </script>
	<script type="text/javascript">
        function admin(super_access)
        {
            if(super_access){
                window.location.href = "admin";

            } else alert('You must be a site administrator to access this feature, please use the contact us link if you believe you should have access');


        }

    </script>

@endsection
