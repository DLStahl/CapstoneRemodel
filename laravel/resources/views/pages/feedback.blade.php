@extends('main')
@section('content')
    <?php

        // url: survey/yearmonday
        // e.g. survey/20180709

        use App\Resident;
        use App\Attending;

        /**
         * Determine the role of the current user
         */
        $email = $_SERVER['HTTP_EMAIL'];
        $role = null;
        $data = null;
        if (Resident::where('email', $email)->exists())
        {
            $role = "Resident";
            $data_ = Resident::where('email', $email)->get();
            $data = $data_[0];
        } else if (Attending::where('email', $email)->exists())
        {
            $role = "Attending";
            $data_ = Attending::where('email', $email)->get();
            $data = $data_[0];
        }
    ?>

    @if ($role == null)
        <h1>You do not have the permission to visit this page!</h1>
    @else
        <h1>Summary of Your Surgery</h1>
        <table class="table table-striped table-bordered">
            <tr>
                <td align="left">Name</td>
                <td align="left">{{ $data['name'] }}</td>
            </tr>
            <tr>
                <td align="left">Role</td>
                <td align="left">{{ $role }}</td>
            </tr>
            <tr>
                <td align="left">Date</td>
                <td align="left">{{ $data_date }}</td>
            </tr>
            <tr>
                <td align="left">Email</td>
                <td align="left">{{ $email }}</td>
            </tr>           
        </table>
    @endif
@endsection