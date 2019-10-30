<nav class="navbar">
    <div class="container">
        <a class="navbar-brand" href="https://wexnermedical.osu.edu">
            <img src="https://wexnermedical.osu.edu/-/media/images/wexnermedical/global/modules/global/header/osuwexmedctr.png?la=en&hash=E40538F4EC98B54A105B79EC8FA1BAB09709DDDC" height="40" alt="">
        </a>
    </div>
</nav>
<nav class="navbar navbar-expand-lg" style="background-color: #bb0000;">
    <div class="container">
        <a class="navbar-brand" href="/laravel/public/">REMODEL: REsident MilestOne-baseD Educational Learning</a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <ul class="navbar-nav mr-auto">
                <li class="nav-item active">
                    <a class="nav-link" href="/laravel/public/">Home<span class="sr-only">(current)</span></a>
                </li>
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
    </div>
</nav>
<br>
