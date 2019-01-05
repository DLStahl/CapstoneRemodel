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
                    <td align="left">First Next Week Day</td>
                    <td align="left">
                    @if ($data['firstday'] != null)
                        {{ $data['firstday'] }}
                    @endif
                    </td>
                </tr>
                <tr>
                    <td align="left">Second Next Week Day</td>
                    <td align="left">
                        @foreach ($data['secondday'] as $choice)
                            @if ($choice != null)
                                <p>{{ $choice }}</p>
                            @endif
                        @endforeach
                    </td>
                </tr>  
                <tr>
                    <td align="left">Third Next Week Day</td>
                    <td align="left">
                        @foreach ($data['thirdday'] as $choice)
                            @if ($choice != null)
                                <p>{{ $choice }}</p>
                            @endif
                        @endforeach
                    </td>   
                </tr>                              
            </table>
        </div>
    </div>
@endsection