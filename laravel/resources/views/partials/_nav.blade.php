<!-- these links are moved into _header -->
<!-- <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script> -->

<nav class="navbar">
    <div class="container">
        <a class="navbar-brand" href="https://wexnermedical.osu.edu">
            <img src="https://wexnermedical.osu.edu/-/media/images/wexnermedical/global/modules/global/header/osuwexmedctr.png?la=en&hash=E40538F4EC98B54A105B79EC8FA1BAB09709DDDC" height="40" alt="">
        </a>
    </div>
</nav>
<nav class="navbar navbar-expand-lg" style="background-color: #bb0000;">
    <div class="container">
        <!-- <a class="navbar-brand" href="/laravel/public/" style="font-size:3vw">REMODEL: REsident MilestOne-baseD Educational Learning</a> -->
        <a class="navbar-brand" href="/laravel/public/">REMODEL: REsident MilestOne-baseD Educational Learning</a>
        <button class="navbar-toggler ml-auto custom-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
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
</nav>
<br>

<style>
.custom-toggler.navbar-toggler {
    border-color: rgb(255,255,255);
    background-color:#e7e7e7 color: black;
}
</style>
