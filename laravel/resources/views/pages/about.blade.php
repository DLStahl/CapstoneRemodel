@extends('main')
@section('title', '| About')
@section('content')
    <div class="row">
        <div class="col-md-12">
            <h2>Personal Information</h2>
            <table class="table table-striped table-bordered" id="personal_info_table">
                <tr>
                    <td>Name</td>
                    <td>{{ $data['name'] }}</td>
                </tr>
                <tr>
                    <td>Email</td>
                    <td>{{ $data['email'] }}</td>
                </tr>
                <tr>
                    <td>Role</td>
                    <td>
                        {{ count($data['roles']) > 0 ? join(', ', $data['roles']) : 'No roles' }}
                    </td>
                </tr>
                <tr>
                    <td>
                        @if (date('l', strtotime('today')) == 'Friday')
                            {{ date('l F j', strtotime('+3 day')) }}
                        @elseif (date('l', strtotime('today')) == 'Saturday')
                            {{ date('l F j', strtotime('+2 day')) }}
                        @else
                            {{ date('l F j', strtotime('+1 day')) }} @endif
                    </td>
                    <td>
                        @if (!is_null($data['firstday']))
                            {!! nl2br($data['firstday']) !!}
                        @endif
                    </td>
                </tr>
                <tr>
                    <td>
                        @if (date('l', strtotime('today')) == 'Thursday')
                            {{ date('l F j', strtotime('+4 day')) }}
                        @elseif (date('l', strtotime('today')) == 'Friday')
                            {{ date('l F j', strtotime('+4 day')) }}
                        @elseif (date('l', strtotime('today')) == 'Saturday')
                            {{ date('l F j', strtotime('+3 day')) }}
                        @else
                            {{ date('l F j', strtotime('+2 day')) }} @endif
                        @if (!is_null($data['secondday']['first']))
                            <br><button class="btn btn-md btn-success" onclick="milestones()">Edit <br>Milestones
                                Objectives</button>
                        @endif
                    </td>
                    <td>
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
                    <td>
                        @if (date('l', strtotime('today')) == 'Wednesday')
                            {{ date('l F j', strtotime('+5 day')) }}
                        @elseif (date('l', strtotime('today')) == 'Thursday')
                            {{ date('l F j', strtotime('+5 day')) }}
                        @elseif (date('l', strtotime('today')) == 'Friday')
                            {{ date('l F j', strtotime('+5 day')) }}
                        @elseif (date('l', strtotime('today')) == 'Saturday')
                            {{ date('l F j', strtotime('+4 day')) }}
                        @else
                            {{ date('l F j', strtotime('+3 day')) }} @endif
                        @if (!is_null($data['thirdday']['first']))
                            <br><button class="btn btn-md btn-success" onclick="milestones()">Edit milestones</button>
                        @endif
                    </td>
                    <td>
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
