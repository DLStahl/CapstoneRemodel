@extends('main')
@section('content')
    @if (strcmp($data['op'], 'add')==0)
        <h4>This milestone will be added:</h4>
        <p><strong>Code: </strong>{{ $data['abbr_name'] }}<br>
        <strong>Category: </strong>{{ $data['full_name'] }}<br>
        <strong>Detail: </strong>{{ $data['detail'] }}</p>
    @elseif(strcmp($data['op'], 'delete')==0)
        <h4>This milestone will be deleted:</h4>
        <p><strong>Code: </strong>{{ $data['old_abbr_name'] }}<br>
        <strong>Category: </strong>{{ $data['old_full_name'] }}<br>
        <strong>Detail: </strong>{{ $data['old_detail'] }}</p>
    @elseif(strcmp($data['op'], 'update')==0)
        <h4>Previous milestone:</h4>
        <p><strong>Code: </strong>{{ $data['old_abbr_name'] }}<br>
        <strong>Category: </strong>{{ $data['old_full_name'] }}<br>
        <strong>Detail: </strong>{{ $data['old_detail'] }}</p>

        <br>
        <h4>Current milestone:</h4>
        <p><strong>Code: </strong>{{ $data['abbr_name'] }}<br>
        <strong>Category: </strong>{{ $data['full_name'] }}<br>
        <strong>Detail: </strong>{{ $data['detail'] }}</p>
    @endif
    <br>
    <input align="left" type="button" value="Confirm" class='btn btn-md btn-success' onclick="confirmUpdate();">

    <script type="text/javascript">

        function confirmUpdate()
        {
            var op = "<?php echo $data['op'] ?>";
            var id = "<?php echo $data['id'] ?>";
            var abbr_name = "<?php echo $data['abbr_name'] ?>";
            var full_name = "<?php echo $data['full_name'] ?>";
            var detail = "<?php echo $data['detail'] ?>";

            // Update url to the update milstone datatable
            var current_url = window.location.href;
            current_url = current_url.substr(0, current_url.search('/milestones/')) + "/milestones";
            var url = "";
            if (op == "add"){
                url = current_url + "/add/true/null/" + abbr_name + "/" + full_name + "/" + detail;
            } else if (op == "delete"){
                url = current_url + "/delete/true/" + id;
            } else if (op == "update"){
                url = current_url + "/update/true/" + id + "/" + abbr_name + "/" + full_name + "/" + detail;
            }
            window.location.href = url;

        }

    </script>
@endsection
