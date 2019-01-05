<html>
    <head>
        <title>REMODEL</title>
        <?php
            $url = "/laravel/public/resident/schedule/thirdday";
            if (strpos($_SERVER['REQUEST_URI'], 'secondday') !== false) {
                $url = "/laravel/public/resident/schedule/secondday";
            }
            
        ?>
        <meta http-equiv="refresh" content="0; URL={{ $url }}">
        <meta name="keywords" content="automatic redirection">
    </head>
    <body>
        <h4>Successfully Update Preferences!</h4>
        <p>
            If your browser does not automatically go there within a few seconds, 
            you may want to go to <a href="{{ $url }}">the destination</a> manually.
        </p>
    </body>
</html>

