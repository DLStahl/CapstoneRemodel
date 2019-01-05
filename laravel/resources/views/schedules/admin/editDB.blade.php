@extends('main')
@section('content')
    <table class="table table-striped table-bordered">
        <tr>
            <th>Location</th>
            <th>Room</th>
            <th>Case Procedure</th>
            <th>Lead Surgeon</th>
            <th>Patient Class</th>
            <th>Start Time</th>
            <th>End Time</th>
            <th>Assignment</th>
            <th></th>
        </tr>
        @foreach ($datasets as $data)
            <tr>
                <form method="POST" action="editDB"><div class="form-group">

                    <input type="hidden" name="date" value="{{ $data['date'] }}">

                    <input type="hidden" name="id" value="{{ $data['id'] }}">

                    <td align="left"><input type="text" name="location" value="{{ $data['location'] }}" required></td>
                    <td align="left"><input type="text" name="room" value="{{ $data['room'] }}" required></td>
                    <td align="left">            
                        {{ $data['case_procedure'] }}
                        <br>
                        <input type="hidden" name="case_procedure_1" value="{{ $data['case_procedure'] }}">
                        <br>
                        <label>Case Procedure 2:</label>
                        <input type="text" name="case_procedure_2">
                        <label> Code:</label>
                        <input type="number" name="case_procedure_2_code" min="10000" max="99999">
                        <br>
                        <label>Case Procedure 3:</label>
                        <input type="text" name="case_procedure_3">
                        <label> Code:</label>
                        <input type="number" name="case_procedure_3_code" min="10000" max="99999">
                        <br>
                        <label>Case Procedure 4:</label>
                        <input type="text" name="case_procedure_4">
                        <label> Code:</label>
                        <input type="number" name="case_procedure_4_code" min="10000" max="99999">
                        <br>
                        <label>Case Procedure 5:</label>
                        <input type="text" name="case_procedure_5">
                        <label> Code:</label>
                        <input type="number" name="case_procedure_5_code" min="10000" max="99999">
                    </td>
                    <td align="left">
                        <label>Lead Surgeon:</label>
                        <input type="text" name="lead_surgeon" value="{{ $data['lead_surgeon'] }}" required>
                        <br>
                        <label> Code:</label>
                        <input type="number" name="lead_surgeon_code" value="{{ $data['lead_surgeon_code'] }}" required>
                    </td>
                    <td align="left"><input type="text" name="patient_class" value="{{ $data['patient_class'] }}" required></td>
                    <td align="left"><input type="time" name="start_time" value="{{ $data['start_time'] }}" required></td>
                    <td align="left"><input type="time" name="end_time" value="{{ $data['end_time'] }}" required></td>
                    <td align="left">
                        {{ $data['assignment'] }}
                        <br>
                        <select name="assignment">
                            @if( strlen($data['assignment']) > 0 )
                                <option value="{{ $data['email'] }}" selected>{{ $data['assignment'] }}</option>
                            @else
                                <option disabled selected value> -- Select a resident -- </option>
                            @endif
                            @foreach ($residents as $resident)
                                <option value="{{ $resident['email'] }}">{{ $resident['name'] }}</option>
                            @endforeach
                        </select>
                    </td>
                    <td><input align = "right" type='submit' class='btn btn-md btn-success'></td>

                </div></form>
            </tr>                             
        @endforeach
        
    </table>
@endsection