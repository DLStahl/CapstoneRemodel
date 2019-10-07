@extends('main')
@section('title', '| About')
@section('content')
    <div class="row">
        <div class="col-md-12">
            <h2>Personal Information</h2>
            <table class="table table-striped table-bordered" id="personal_info_table">
                <tr>
                    <td align="left">Name</td>
                    <td aligh="left">{{ $data['name'] }}</td>
                </tr>
                <tr>
                    <td align="left">Email</td>
                    <td align="left">{{ $data['email'] }}</td>
                </tr>
                <tr>
                    <td align="left">Role</td>
                    @if (count($data['roles']) > 0)
                        <td align="left">
                        @foreach($data['roles'] as $role)
                            <p>{{ $role }}</p>
                        @endforeach
                        </td>
                    @else
                        <td align="left">None</td>
                    @endif
                </tr>
                <tr>
                    <td align="left"><?php
        if (date("l", strtotime('today'))=='Friday') {
            echo date("l", strtotime('+3 day')),' ', date('F',strtotime('+3 day')),' ',date('j',strtotime('+3 day'));
        }
        elseif (date("l", strtotime('today'))=='Saturday') {
            echo date("l", strtotime('+2 day')),' ', date('F',strtotime('+2 day')),' ',date('j',strtotime('+2 day'));
        }
        else{
            echo date("l", strtotime('+1 day')),' ', date('F',strtotime('+1 day')),' ',date('j',strtotime('+1 day'));
        }
    ?></td>
                    <td align="left">
                    @if ($data['firstday'] != null)
                        {!! nl2br($choice) !!}
                    @endif
                    </td>
                </tr>
                <tr>
                    <td align="left"><?php

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
        ?>
        @if(!is_null($data['secondday']['first']))
            <br><button class="btn btn-md btn-success" onclick="milestones()">Edit milestones</button>
        @endif
    </td>
                    <td align="left">
                        @if(!is_null($data['secondday']['first']))
                            @foreach ($data['secondday'] as $choice)
                                @if ($choice != null)
                                    {!! nl2br($choice) !!}
                                    <!-- <p>{{ $choice }}</p> -->
                                @endif
                            @endforeach
                        @endif
                    </td>
                </tr>
                <tr>
                    <td align="left"><?php

        if(date("l", strtotime('today'))=='Wednesday'){
            echo date("l", strtotime('+5 day')),' ', date('F',strtotime('+5 day')),' ',date('j',strtotime('+5 day'));
        }
        elseif(date("l", strtotime('today'))=='Thursday'){
            echo date("l", strtotime('+5 day')),' ', date('F',strtotime('+5 day')),' ',date('j',strtotime('+5 day'));
        }
        elseif (date("l", strtotime('today'))=='Friday') {
            echo date("l", strtotime('+5 day')),' ', date('F',strtotime('+5 day')),' ',date('j',strtotime('+5 day'));
        }
        elseif (date("l", strtotime('today'))=='Saturday') {
            echo date("l", strtotime('+4 day')),' ', date('F',strtotime('+4 day')),' ',date('j',strtotime('+4 day'));
        }
        else{
            echo date("l", strtotime('+3 day')),' ', date('F',strtotime('+3 day')),' ',date('j',strtotime('+3 day'));
        }
        ?>
        @if(!is_null($data['thirdday']['first']))
            <br><button class="btn btn-md btn-success" onclick="milestones()">Edit milestones</button>
        @endif
    </td>
                    <td align="left">
                        @if(!is_null($data['thirdday']['first']))
                            @foreach ($data['thirdday'] as $choice)
                                @if ($choice != null)
                                    {!! nl2br($choice) !!}
                                    <!-- <p>{{ $choice }}</p> -->
                                @endif
                            @endforeach
                        @endif
                    </td>
                </tr>
            </table>


<script>
 function milestones(){

        var id1 = 0;
        var id2 = 0;
        var id3 = 0;
        var ids = "<?php echo $data['ids'] ?>";

        //  if (ids != null){
        //    if(!ids.includes(":") ){
        //       id1 = ids.substring(1);
        //     }
        //     else if(ids.includes(":") && !ids.includes(",")) {
        //       id1 = ids.substring(1, ids.indexOf(":"));
        //       id2 = ids.substring( ids.indexOf(":")+1);
        //    }

        //     else{
        //       id1 = ids.substring(1, ids.indexOf(":"));
        //       id2 = ids.substring(ids.indexOf(":") + 1, ids.indexOf(","));
        //       id3 = ids.substring(ids.indexOf(",") + 1 );
        //     }

        // }

        var current_url = window.location.href;
        var url = current_url.substr(0, current_url.search('/resident/'));
        // if (date == 'secondday') {
            // url = url + "/resident/schedule/milestonesEdit/" + id1 + "_" + id2 + "_" + id3 +"/";

            url = url + "/resident/schedule/milestonesEdit/" + ids +"/";
        // } else {
            // url = url + "/resident/schedule/thirdday/milestonesEdit/" + id1 + "_" + id2 + "_" + id3 +"/"
        // }
        // alert(url);

        window.location.href = url;
    }

</script>
        </div>
    </div>
@endsection
