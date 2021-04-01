@extends('main')
@section('content')
    @if ($valid == 0)
        <h3>Sorry, your csv file is invalid. It must have 3 columns of code, category, and detail.</h3>
    @elseif (sizeof($data['new']) > 0 || sizeof($data['update']) > 0 ||sizeof($data['invalid']) > 0)
        <br>
        <form id="uploadFileForm" action="./uploadUpdate" method="post">
            <input hidden type="text" value="{{ $filepath }}" name="filepath">

            @if (sizeof($data['new']) > 0)
                <h4>These milestones will be added:</h4>
                <table class="table table-striped table-bordered" id="modify_table">
                    <tr>
                        <th> Code </th>
                        <th> Category </th>
                        <th> Detail </th>
                    </tr>
                    @foreach ($data['new'] as $mile)
                        <tr>
                            <td> {{ $mile['abbr_name'] }} </td>
                            <td> {{ $mile['full_name'] }} </td>
                            <td> {{ $mile['detail'] }} </td>
                        </tr>
                    @endforeach
                </table>
                <br>
            @endif
            @if (sizeof($data['update']) > 0)
                <h4>These milestones will be updated:</h4>
                <table class="table table-striped table-bordered" id="modify_table">
                    <tr>
                        <th> Code </th>
                        <th> Category </th>
                        <th> Detail </th>
                    </tr>
                    @foreach ($data['update'] as $mile)
                        <tr>
                            <td> {{ $mile['abbr_name'] }} </td>
                            <td> {{ $mile['full_name'] }} </td>
                            <td> {{ $mile['detail'] }} </td>
                        </tr>
                    @endforeach
                </table>
                <br>
            @endif
            @if (sizeof($data['invalid']) > 0)
                <h4>These milestones will not be updated because they don't have complete information:</h4>
                <table class="table table-striped table-bordered" id="modify_table">
                    <tr>
                        <th> Code </th>
                        <th> Category </th>
                        <th> Detail </th>
                    </tr>
                    @foreach ($data['invalid'] as $mile)
                        <tr>
                            <td> {{ $mile['abbr_name'] }} </td>
                            <td> {{ $mile['full_name'] }} </td>
                            <td> {{ $mile['detail'] }} </td>
                        </tr>
                    @endforeach
                </table>
                <br>
            @endif
            @if (sizeof($data['new']) > 0 || sizeof($data['update']) > 0)
                <button type="submit" class="btn btn-success">Confirm</button>
            @endif
        </form>

        <script type="text/javascript">
            var newData = @json($data['new']);
            var updateData = @json($data['update']);
            var invalidData = @json($data['invalid']);
            console.log(newData);
            console.log(updateData);
            console.log(invalidData);

        </script>
    @else
        <h3>Sorry, no data is found in the csv file.</h3>
    @endif
@endsection
