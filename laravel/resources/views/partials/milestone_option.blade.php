<option value="{{ $milestone->id }}" title="{{ $milestone->title }}"
    {{ $milestone->id == $selected_id ? 'selected' : '' }}>
    {{ $milestone->category }} - {{ $milestone->detail }}
</option>
