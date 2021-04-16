<button id='1stbutton' type="button" class="btn btn-primary" onclick="updateURL('firstday');">
    @if (date('l', strtotime('today')) == 'Friday')
        {{ date('l F j', strtotime('+3 day')) }}
    @elseif (date('l', strtotime('today')) == 'Saturday')
        {{ date('l F j', strtotime('+2 day')) }}
    @else
        Tomorrow
    @endif
</button>
<button id='2ndbutton' type="button" class="btn btn-primary" onclick="updateURL('secondday');">
    @if (date('l', strtotime('today')) == 'Thursday')
        {{ date('l F j', strtotime('+4 day')) }}
    @elseif (date('l', strtotime('today')) == 'Friday')
        {{ date('l F j', strtotime('+4 day')) }}
    @elseif (date('l', strtotime('today')) == 'Saturday')
        {{ date('l F j', strtotime('+3 day')) }}
    @else
        {{ date('l F j', strtotime('+2 day')) }}
    @endif
</button>
<br>

<script type="text/javascript">
    // Set the selected date button to red (hex color #bb0000)
    if (window.location.href.indexOf("firstday") > -1) {
        $('#1stbutton').css('background-color', '#bb0000');
    } else if (window.location.href.indexOf("secondday") > -1) {
        $('#2ndbutton').css('background-color', '#bb0000');
        // } else if (window.location.href.indexOf("thirdday") > -1){
        // $('#3rdbutton').css('background-color', '#bb0000');
    }

    function updateURL(day) {
        // Update url to the selected date
        var current_url = window.location.href;
        var url = current_url.substr(0, current_url.search('/schedule/'));
        url = url + "/schedule/" + day + "/";
        window.location.href = url;
    }

</script>
