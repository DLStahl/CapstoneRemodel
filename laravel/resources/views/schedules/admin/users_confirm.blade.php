@extends('main')
@section('content')

    @if (strcmp($data['op'], "deleteUser")==0)
        <h2>{{ $data['role'] }} with Email address: {{ $data['email'] }} will be deleted.</h2>
    @else
        <h2>{{ $data['role'] }}, {{ $data['name'] }} with Email address: {{ $data['email'] }} will be added.</h2>
    @endif

    <input align="left" type="button" value="Confirm" id="{{ $data['email'] }}_{{ $data['role'] }}/{{ $data['name'] }}[{{ $data['op'] }}" class='btn btn-md btn-success' onclick="confirmUpdate(this.id);">		

    <script type="text/javascript">                
        function confirmUpdate(id)
        {
            var email = id.substring(0, id.indexOf('_'));
            var role = id.substring(id.indexOf('_')+1, id.indexOf('/'));
            var name = id.substring(id.indexOf('/')+1, id.indexOf('['));
            var op = id.substring(id.indexOf('[')+1);

            // Update url to the confirmation page
            var current_url = window.location.href;
            var url = "";
            if (current_url.includes('deleteUser')) {
                url = current_url.substring(0, current_url.indexOf('/deleteUser/'));
            } else {
                url = current_url.substring(0, current_url.indexOf('/addUser/'));
            }
            url = url + "/" + op + "/" + role + "/" + email + "/true/" + name;
            window.location.href = url;
        }

    </script>
@endsection