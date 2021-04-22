@extends('main')

@section('content')

    <div id="Resident Form">
        <h4>Resident Preferences</h4>
        <form method="POST" action="../confirm">
            @foreach ($resident_choices as $choice)
                @if (is_null($choice['schedule']))
                    <h5>Preference #{{ $loop->iteration }}: None</h5>
                @else
                    <h5>Preference #{{ $loop->iteration }}: Room {{ $choice['schedule']['room'] }} with
                        {{ $choice['lead_surgeon'] }} </h5>

                    <div class="form-group">
                        <label for="milestones{{ $loop->iteration }}">Select your Milestone:</label>
                        <select class="form-control" name="milestones{{ $loop->iteration }}"
                            id="milestones{{ $loop->iteration }}" required>
                            <option value="" selected> -- Select a Milestone -- </option>
                            @if (!is_null($milestones))
                                @foreach ($milestones as $milestone)
                                    @include('partials.milestone_option', [
                                    'milestone' => $milestone,
                                    'selected_id' => (isset($choice['milestone']) ? $choice['milestone']['id'] : null)
                                    ])
                                @endforeach
                            @endif
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="objectives{{ $loop->iteration }}">What is your educational objective for this OR
                            today?</label>
                        <textarea rows="3" name="objectives{{ $loop->iteration }}" id="objectives{{ $loop->iteration }}"
                            class="form-control"
                            required>{{ isset($choice['objective']) ? $choice['objective'] : null }}</textarea>
                    </div>

                    <div class="form-group">
                        <label for="pref_anest{{ $loop->iteration }}">Anesthesiologist Preference:</label>
                        <select class="form-control" name="pref_anest{{ $loop->iteration }}"
                            id="pref_anest{{ $loop->iteration }}">
                            <option selected="selected">No Preference</option>
                            @foreach ($anesthesiologists as $a)
                                <option value="{{ $a->id }}"
                                    {{ isset($choice['pref_anest']) && $a->id == $choice['pref_anest'] ? 'selected' : '' }}>
                                    Dr. {{ $a->first_name }} {{ $a->last_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                @endif
            @endforeach

            <input type="hidden" name="schedule_id" value="{{ $id }}">
            <input align="right" type="submit" value="Next" class='btn btn-md btn-success'>
        </form>
    </div>

@endsection
