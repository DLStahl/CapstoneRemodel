
<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
    <head>
        <!-- Required meta tags -->
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=yes">
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
        <link rel="stylesheet" type="text/css" href="css/style.css">
        <title>REMODEL</title> <!-- Change this title for each page -->
    </head>

    <body>

        <nav class="navbar">
            <div class="container">
                <a class="navbar-brand" href="https://wexnermedical.osu.edu">
                    <img src="https://wexnermedical.osu.edu/-/media/images/wexnermedical/global/modules/global/header/osuwexmedctr.png?la=en&hash=E40538F4EC98B54A105B79EC8FA1BAB09709DDDC" height="40" alt="">
                </a>
            </div>
        </nav>
        <nav class="navbar navbar-expand-lg" style="background-color: #bb0000;">
            <div class="container">
                <a class="navbar-brand2" href="/">REMODEL: REsident MilestOne-baseD Educational Learning</a>
                <!-- <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <ul class="navbar-nav mr-auto">
                        <li class="nav-item active">
                            <a class="nav-link" href="/">Home<span class="sr-only">(current)</span></a>
                        </li>
                    </ul>
                </div> -->
            </div>
        </nav>
        <br>

        <div class="container">
		<h6>The REMODEL (REsident MilestOne-baseD Educational Learning) system is designed to allow residents to identify preferences for scheduled surgical cases in which to participate.</h6>
		<ul>
 			<li>Select your first, second, and third choice by operating room for the day after tomorrow and submit your choices</li>
  			<li>For each choice (first, second, and third), create an educational objective for yourself and select a milestone from the drop-down menu which best corresponds with that educational objective</li>
  			<li>Submit and await your OR assignment!</li>
  			<li>Discuss the educational objective with your attending the night before in order create a plan for the day</li>
		</ul>

                <a class="btn btn-primary" href="/laravel/public/resident/schedule/secondday">Sign In</a>
        </div>

        <hr>
        <div id="footer-wrap">
            <div id="footer">
                <div class="container">   
                    <div id="footer_left">
                        <p>&copy; <?php echo date('Y');?> OSU Anesthesiology</p>     
                    </div>
                </div>
            </div>
        </div>

    </body>
</html>

<style>
.navbar-brand2 {
    display: inline-block;
    padding-top: .3125rem;
    padding-bottom: .3125rem;
    margin-right: 1rem;
    line-height: inherit;
    white-space: nowrap;
}
@media screen and (max-width: 770px){
    .navbar-brand2 
        {
            font-size: 2.5vw;
        }
    }
@media  screen and (min-width: 770px){
    .navbar-brand2 
        {
            font-size: 1.25rem;
        }
    }
</style>