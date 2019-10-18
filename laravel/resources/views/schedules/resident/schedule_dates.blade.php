<button id='1stbutton' type="button" class="btn btn-primary" onclick="updateURL('firstday');"><?php
    if (date("l", strtotime('today'))=='Friday') {
        echo date("l", strtotime('+3 day')),' ', date('F',strtotime('+3 day')),' ',date('j',strtotime('+3 day'));
    }
    elseif (date("l", strtotime('today'))=='Saturday') {
        echo date("l", strtotime('+2 day')),' ', date('F',strtotime('+2 day')),' ',date('j',strtotime('+2 day'));
    }
    else{
        // echo date("l", strtotime('+1 day')),' ', date('F',strtotime('+1 day')),' ',date('j',strtotime('+1 day'));
        echo 'Tomorrow';
    }
?></button>
<button id='2ndbutton' type="button" class="btn btn-primary" onclick="updateURL('secondday');"><?php

    if(date("l", strtotime('today'))=='Thursday'){
        echo date("l", strtotime('+4 day')),' ', date('F',strtotime('+4 day')),' ',date('j',strtotime('+4 day'));
    }
    elseif (date("l", strtotime('today'))=='Friday') {
        echo date("l", strtotime('+4 day')),' ', date('F',strtotime('+4 day')),' ',date('j',strtotime('+4 day'));
    }
    elseif (date("l", strtotime('today'))=='Saturday') {
        echo date("l", strtotime('+3 day')),' ', date('F',strtotime('+3 day')),' ',date('j',strtotime('+3 day'));
    }
    else{
        echo date("l", strtotime('+2 day')),' ', date('F',strtotime('+2 day')),' ',date('j',strtotime('+2 day'));
    }

?></button>
<!-- <button id='3rdbutton' type="button" class="btn btn-primary" onclick="location.href='thirdday';"><?php

    // if(date("l", strtotime('today'))=='Wednesday'){
    //     echo date("l", strtotime('+5 day')),' ', date('F',strtotime('+5 day')),' ',date('j',strtotime('+5 day'));
    // }
    // elseif(date("l", strtotime('today'))=='Thursday'){
    //     echo date("l", strtotime('+5 day')),' ', date('F',strtotime('+5 day')),' ',date('j',strtotime('+5 day'));
    // }
    // elseif (date("l", strtotime('today'))=='Friday') {
    //     echo date("l", strtotime('+5 day')),' ', date('F',strtotime('+5 day')),' ',date('j',strtotime('+5 day'));
    // }
    // elseif (date("l", strtotime('today'))=='Saturday') {
    //     echo date("l", strtotime('+4 day')),' ', date('F',strtotime('+4 day')),' ',date('j',strtotime('+4 day'));
    // }
    // else{
    //     echo date("l", strtotime('+3 day')),' ', date('F',strtotime('+3 day')),' ',date('j',strtotime('+3 day'));
    // }

?></button> -->
<br>

<script type="text/javascript">
    // $(document).ready(function() {
        if (window.location.href.indexOf("firstday") > -1){
            $('#1stbutton').css('background-color', '#bb0000');
        } else if (window.location.href.indexOf("secondday") > -1){
            $('#2ndbutton').css('background-color', '#bb0000');
        // } else if (window.location.href.indexOf("thirdday") > -1){
            // $('#3rdbutton').css('background-color', '#bb0000');
        }

        function updateURL(day)
        {
            // Update url to the selected date
            var current_url = window.location.href;
            var url = current_url.substr(0, current_url.search('/schedule/'));
            url = url + "/schedule/" + day +"/";
            window.location.href = url;
        }
    // });
</script>
