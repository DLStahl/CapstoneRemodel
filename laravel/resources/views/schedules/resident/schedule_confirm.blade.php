@extends('main')
@section('content')
    @if ($input['choice'] == 1)
        <h2>First Choice: </h2>
    @elseif ($input['choice'] == 2)
        <h2>Second Choice: </h2>
    @elseif ($input['choice'] == 3)
        <h2>Third Choice: </h2>
    @endif
    <p>Date: {{ $schedule_data[0]['date'] }}</p>
    <p>Location: {{ $schedule_data[0]['location'] }}</p>
    <p>Room: {{ $schedule_data[0]['room'] }}</p>
    <p>Start Time: {{ $schedule_data[0]['start_time'] }}</p>
    <p>End Time: {{ $schedule_data[0]['end_time'] }}</p>
    <p>Patient Class: {{ $schedule_data[0]['patient_class'] }}</p>

    <br><br>
    
    <input align="left" type="button" value="Confirm" id="{{ $input['id'] }}_{{ $input['choice'] }}" class='btn btn-md btn-success' onclick="confirmUpdate(this.id);">		

    <script type="text/javascript">                
    function confirmUpdate(id)
    {
        var id_ = id.substring(0, id.indexOf('_'));
        var choice = id.substr(id.indexOf('_')+1);

        // Update url to the confirmation page
        var current_url = window.location.href;
        var url = current_url.substr(0, current_url.search('/schedule/'));
        if (current_url.includes('secondday')) {
            url = url + "/schedule/secondday/" + id_ + "/" + choice + "/true";
        } else {
            url = url + "/schedule/thirdday/" + id_ + "/" + choice + "/true";
        }
        window.location.href = url;
    }

    </script>
@endsection