@extends('main')

@section('content')
    <h1>
        @php
            date_default_timezone_set('America/New_York');
        @endphp
        Today's Date: {{ date('l F j', strtotime('today')) }}
        <br>
        Visit Time: {{ date('h:i:s a', time()) }}
    </h1>
    <br><br><br>
    <h5>Select Your Date</h5>
    <br><br>
    <button type="button" class="btn btn-primary" onclick="location.href='schedule/firstday';">
        @if (date('l', strtotime('today')) == 'Friday')
            {{ date('l F j', strtotime('+3 day')) }}
        @elseif (date('l', strtotime('today')) == 'Saturday')
            {{ date('l F j', strtotime('+2 day')) }}
        @else
            {{ date('l F j', strtotime('+1 day')) }}
        @endif
    </button>
    <button type="button" class="btn btn-primary" onclick="location.href='schedule/secondday';">
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
    <button type="button" class="btn btn-primary" onclick="location.href='schedule/thirdday';">
        @if (date('l', strtotime('today')) == 'Wednesday')
            {{ date('l F j', strtotime('+5 day')) }}
        @elseif (date('l', strtotime('today')) == 'Thursday')
            {{ date('l F j', strtotime('+5 day')) }}
        @elseif (date('l', strtotime('today')) == 'Friday')
            {{ date('l F j', strtotime('+5 day')) }}
        @elseif (date('l', strtotime('today')) == 'Saturday')
            {{ date('l F j', strtotime('+4 day')) }}
        @else
            {{ date('l F j', strtotime('+3 day')) }}
        @endif
    </button>

    <br><br><br>

@endsection
