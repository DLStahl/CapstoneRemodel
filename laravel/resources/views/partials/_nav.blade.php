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
                    <a class="nav-link" href="/laravel/public/about" id="about">My Selections</a>
                    <script>
                        // document.getElementById("about").href = window.location.pathname + "/about";
                        //if (window.location.pathname == "/")
                        //{
                        //    document.getElementById("return").style.visibility = "hidden";
                        //}
                        if (window.location.pathname.includes("resident"))
                        {
                            document.getElementById("about").href = "/laravel/public/resident/about";
                        } else if (window.location.pathname.includes("admin"))
                        {
                            document.getElementById("about").href = "/laravel/public/admin/about";
                        }
                    </script> 
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="/laravel/public/contact" id="contact">Contact Us</a>
                    <script>
                            if (window.location.pathname.includes("resident"))
                            {
                                document.getElementById("contact").href = "/laravel/public/resident/contact";
                            } else if (window.location.pathname.includes("admin"))
                            {
                                document.getElementById("contact").href = "/laravel/public/admin/contact";
                            }
                    </script> 
                </li>
            </ul>
        </div>
    </div>
</nav>
<br>