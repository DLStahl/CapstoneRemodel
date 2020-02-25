<nav class="navbar">
    <div class="container">
        <a class="navbar-brand" href="https://wexnermedical.osu.edu">
            <img src="https://wexnermedical.osu.edu/-/media/images/wexnermedical/global/modules/global/header/osuwexmedctr.png?la=en&hash=E40538F4EC98B54A105B79EC8FA1BAB09709DDDC" height="40" alt="">
        </a>
    </div>
</nav>
<?php
	use App\Resident;
	use App\Admin;

	$super_access = false;
	if (Admin::where('email', $_SERVER['HTTP_EMAIL'])->where('exists','1')->exists()) {
		$super_access = true;
	}
?>
<nav class="navbar navbar-expand-lg" style="background-color: #bb0000;">
    <div class="container">
        <a class="navbar-brand2" href="/laravel/public/resident/schedule/secondday">REMODEL: REsident MilestOne-baseD Educational Learning</a>
        <button class="navbar-toggler ml-auto custom-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
            <font color="white">≡</font>
        </button>
        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <ul class="navbar-nav mr-auto">
                <li class="nav-item active">
                    <a class="nav-link" href="/laravel/public/resident/schedule/secondday">Home<span class="sr-only">(current)</span></a>
                </li>
				@if($super_access)
				<li class="nav-item">
                    <a class="nav-link" href="#" onclick="openNav()" id="dashboard" style="display:none;">Admin Dashboard</a>
                    <script>
                            document.getElementById("dashboard").style.display = "block";
                            
							/* Open the sidenav */
                            function openNav() {
                              document.getElementById("adminsidenav").style.width = "100%";
                            }

                            /* Close/hide the sidenav */
                            function closeNav() {
                              document.getElementById("adminsidenav").style.width = "0";
                            }
                    </script>
                </li>
				@endif
                <li class="nav-item">
                    <a class="nav-link" href="/laravel/public/resident/about" id="about">My Selections</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="/laravel/public/contact" id="contact">Contact Us</a>
                    <script>
                            if (window.location.pathname.indexOf("resident") != -1 )
                            {
                                document.getElementById("contact").href = "/laravel/public/resident/contact";
                            } else if (window.location.pathname.indexOf("admin") != -1 )
                            {
                                document.getElementById("contact").href = "/laravel/public/admin/contact";
                            }
                    </script>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="/laravel/public/acknowledgements" id="acknowledgements">Acknowledgements</a>
                    <script>
                        if(window.location.pathname.indexOf("resident") != -1)
                        {
                            document.getElementById("acknowledgements").href = "/laravel/public/resident/acknowledgements";
                        }
                        else if (window.location.pathname.indexOf("admin") != -1)
                        {
                            document.getElementById("acknowledgements").href = "/laravel/public/admin/acknowledgements";
                        }
                    </script>
	            </li>
            </ul>
        </div>
</nav>
<br>
@if($super_access)
<!--admin dashboard-->
<div id="adminsidenav" class="sidenav">
    <a href="javascript:void(0)" class="closebtn" onclick="closeNav()">&times;</a>
    <a href="/laravel/public/admin/db/resident">Edit Residents</a>
    <a href="/laravel/public/admin/db/attending">Edit Attendings</a>
    <a href="/laravel/public/admin/db/filter_rotation">Edit Surgeons/Rotations</a>
    <a href="/laravel/public/admin/db/admin">Edit Admins</a>
    <a href="/laravel/public/admin/db/evaluation_forms">Edit Evaluation Forms</a>
    <a href="/laravel/public/admin/db/schedule_data_static">Edit Static Schedule Data</a>
    <a href="/laravel/public/admin/schedules">Edit Schedules</a>
    <a href="/laravel/public/admin/milestones">Edit Milestones</a>
    <a href="/laravel/public/admin/postmessage">Post Messages</a>
    <a href="/laravel/public/admin/download">Download Data Sheets</a>
    <a href="/laravel/public/admin/resetTickets">Reset Tickets</a>
    <a href="/laravel/public/admin/evaluation">View Resident/Attending Pairings</a>
    <a href="/laravel/public/admin/uploadForm">Upload Schedule</a>
    <a href="/laravel/public/admin/medhubtest">MedHub Test</a>
</div>
@endif
<script>
if ($(window).width() < 770) {
   console.log("hi");
}
</script>
<style>
.custom-toggler.navbar-toggler {
 /* border-color: rgb(255,255,255); */
    background-color:#e7e7e7 color: black;
}

.navbar-brand2 {
    display: inline-block;
    padding-top: .3125rem;
    padding-bottom: .3125rem;
    margin-right: 1rem;
    line-height: inherit;
    white-space: nowrap;
}

/* The side navigation menu */
.sidenav {
  height: 100%;
  width: 0;
  position: fixed;
  z-index: 10; /* puts sidenav in front of everything else on the page */
  top: 0; /* Stay at the top */
  left: 0;
  background-color: #FFFFFF; /* White*/
  overflow-x: hidden; /* Disable horizontal scroll */
  padding-top: 60px;
  transition: 0.5s; /* 0.5 second transition effect to slide in the sidenav */
  text-align: left;
}

/* The navigation menu links */
.sidenav a {
  padding: 8px 8px 8px 8px;
  text-decoration: none;
  font-size: 25px;
  color: #bb0000;
  display: block;
  transition: 0.3s;
}

.sidenav a:hover {
  text-decoration: underline;
}

/* X button in top right corner to close sidenav*/
.sidenav .closebtn {
  position: absolute;
  top: 0;
  right: 25px;
  font-size: 36px;
  margin-left: 50px;
}

@media screen and (max-width: 770px){
    .navbar-brand2 
        {
            font-size: .7rem;
        }
    }
@media  screen and (min-width: 770px){
    .navbar-brand2 
        {
            font-size: 1.25rem;
        }
    }
</style>
