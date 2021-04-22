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
                            @foreach ($data['roles'] as $role)
                                <p>{{ $role }}</p>
                            @endforeach
                        </td>
                    @else
                        <td align="left">None</td>
                    @endif
                </tr>
                <tr>
                    <td align="left">
                        @php
                            if (date('l', strtotime('today')) == 'Friday') {
                                echo date('l', strtotime('+3 day')), ' ', date('F', strtotime('+3 day')), ' ', date('j', strtotime('+3 day'));
                            } elseif (date('l', strtotime('today')) == 'Saturday') {
                                echo date('l', strtotime('+2 day')), ' ', date('F', strtotime('+2 day')), ' ', date('j', strtotime('+2 day'));
                            } else {
                                echo date('l', strtotime('+1 day')), ' ', date('F', strtotime('+1 day')), ' ', date('j', strtotime('+1 day'));
                            }
                        @endphp
                    </td>
                    <td align="left">
                        @if (!is_null($data['firstday']))
                            {!! nl2br($data['firstday']) !!}
                        @endif
                    </td>
                </tr>
                <tr>
                    <td align="left">
                        @php
                            if (date('l', strtotime('today')) == 'Thursday') {
                                echo date('l', strtotime('+4 day')), ' ', date('F', strtotime('+4 day')), ' ', date('j', strtotime('+4 day'));
                            } elseif (date('l', strtotime('today')) == 'Friday') {
                                echo date('l', strtotime('+4 day')), ' ', date('F', strtotime('+4 day')), ' ', date('j', strtotime('+4 day'));
                            } elseif (date('l', strtotime('today')) == 'Saturday') {
                                echo date('l', strtotime('+3 day')), ' ', date('F', strtotime('+3 day')), ' ', date('j', strtotime('+3 day'));
                            } else {
                                echo date('l', strtotime('+2 day')), ' ', date('F', strtotime('+2 day')), ' ', date('j', strtotime('+2 day'));
                            }
                        @endphp
                        @if (!is_null($data['secondday']['first']))
                            <br><button class="btn btn-md btn-success" onclick="milestones()">Edit <br>Milestones
                                Objectives</button>
                        @endif
                    </td>
                    <td align="left">
                        @php
                            if (!is_null($data['secondday']['first'])) {
                                $count = 0;
                                foreach ($data['secondday'] as $choice) {
                                    if ($count < sizeof($data['secondday']) - 1) {
                                        $count += 1;
                                        if ($choice != null) {
                                            echo nl2br($choice);
                                        }
                                    }
                                }
                            }
                        @endphp
                    </td>
                </tr>
                <tr>
                    <td align="left">
                        @php
                            if (date('l', strtotime('today')) == 'Wednesday') {
                                echo date('l', strtotime('+5 day')), ' ', date('F', strtotime('+5 day')), ' ', date('j', strtotime('+5 day'));
                            } elseif (date('l', strtotime('today')) == 'Thursday') {
                                echo date('l', strtotime('+5 day')), ' ', date('F', strtotime('+5 day')), ' ', date('j', strtotime('+5 day'));
                            } elseif (date('l', strtotime('today')) == 'Friday') {
                                echo date('l', strtotime('+5 day')), ' ', date('F', strtotime('+5 day')), ' ', date('j', strtotime('+5 day'));
                            } elseif (date('l', strtotime('today')) == 'Saturday') {
                                echo date('l', strtotime('+4 day')), ' ', date('F', strtotime('+4 day')), ' ', date('j', strtotime('+4 day'));
                            } else {
                                echo date('l', strtotime('+3 day')), ' ', date('F', strtotime('+3 day')), ' ', date('j', strtotime('+3 day'));
                            }
                        @endphp
                        @if (!is_null($data['thirdday']['first']))
                            <br><button class="btn btn-md btn-success" onclick="milestones()">Edit milestones</button>
                        @endif
                    </td>
                    <td align="left">
                        @php
                            if (!is_null($data['thirdday']['first'])) {
                                $count = 0;
                                foreach ($data['thirdday'] as $choice) {
                                    if ($count < sizeof($data['thirdday']) - 1) {
                                        $count += 1;
                                        if ($choice != null) {
                                            echo nl2br($choice);
                                        }
                                    }
                                }
                            }
                        @endphp
                    </td>
                </tr>
            </table>


            <script>
                function milestones() {
                    var ids = "{{ $data['ids'] }}";
                    var current_url = window.location.href;
                    var url = current_url.substr(0, current_url.search('/resident/'));
                    url = url + "/resident/schedule/milestonesEdit/" + ids + "/";

                    window.location.href = url;
                }

            </script>
        </div>
    </div>
    <style>
        .btn {
            white-space: normal;
        }

    </style>
@endsection
