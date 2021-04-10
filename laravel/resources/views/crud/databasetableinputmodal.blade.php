{{-- /resources/views/crud/databaseinputmodal.blade.php --}}
{{-- confirmation modal autoinclude --}}

<div class="modal fade" id="{{ $id }}" role="dialog" aria-labelledby="{{ $id }}" aria-hidden="true"
    style="z-index: {{ $z_index }};">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="modalMainTitle">{{ $title }}</h4>
                @if ($has_options)
                    <div class="btn-group">
                        <button type="button" class="btn btn-link dropdown-toggle options" data-toggle="dropdown"
                            aria-haspopup="true" aria-expanded="false">Options</button>
                        <div class="dropdown-menu">
                            <a id="duplicateButton" class="dropdown-item actionEdit" href="#" data-toggle="modal"
                                data-target="#confirmActionModal">Duplicate</a>
                            <div class="dropdown-divider"></div>
                            <a id="deleteButton" class="dropdown-item delete actionEdit" href="#" data-toggle="modal"
                                data-target="#confirmActionModal">Delete</a>
                        </div>
                    </div>
                @endif
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                @foreach ($columns as $column)
                    <div class="form-group">
                        <label for="{{ $column }}" class="col-form-label">{{ $column }}</label>
                        <input type="text" class="form-control actionEdit"
                            id="{{ $column }}-{{ $input_id_suffix }}" @if (in_array($column, $uneditable)) disabled="true" @endif>
                    </div>
                @endforeach
            </div>
            <div class="modal-footer">
                <!--button id="dismissModal" type="button" class="btn btn-secondary" data-dismiss="modal">Exit</button-->
                <button id="{{ $confirm_button_id }}" type="button"
                    class="btn {{ $confirm_button_type }} actionEdit">{{ $confirm_button_name }}</button>
            </div>
        </div>
    </div>
</div>
