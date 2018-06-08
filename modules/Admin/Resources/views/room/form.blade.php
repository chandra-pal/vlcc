<div class="form-body">
    <div class="form-group">
        <label class="col-md-3 control-label">Select Center <span class="required" aria-required="true">*</span></label>
        <div class="col-md-4">
            {!! Form::select('center_id', [''=>'Select Center'] +$centerList, null,['autocomplete' => 'off', 'class'=>'select2me form-control', 'id' => 'center_id', 'data-rule-required'=>'true', 'data-msg-required'=>trans('admin::messages.required-enter', ['name' => trans('admin::controller/room.center')])]) !!}
        </div>
    </div>
    <div class="form-group">
        <label class="col-md-3 control-label">Room Name <span class="required" aria-required="true">*</span></label>
        <div class="col-md-4">
            {!! Form::text('name', null, ['maxlength'=>'200', 'class'=>'form-control', 'data-rule-required'=>'true', 'data-msg-required'=>trans('admin::messages.required-enter', ['name' => trans('admin::controller/room.name')]), 'minlength'=>2, 'data-msg-minlength'=>trans('admin::messages.error-minlength', ['name' => trans('admin::controller/room.name')]) ])!!}
        </div>
    </div>
    <div class="form-group">
        <label class="col-md-3 control-label">Room Type <span class="required" aria-required="true">*</span> </label>
        <div class="col-md-4">
            <div class="radio-list">
                <label class="radio-inline">{!! Form::radio('room_type', '1',true) !!} {!! trans('admin::controller/room.male') !!}</label>
                <label class="radio-inline">{!! Form::radio('room_type', '2') !!} {!! trans('admin::controller/room.female') !!}</label>
                <label class="radio-inline">{!! Form::radio('room_type', '3') !!} {!! trans('admin::controller/room.common') !!}</label>
            </div>
        </div>
    </div>
    <div class="form-group">
        <label class="col-md-3 control-label">Status </label>
        <div class="col-md-4">
            <div class="radio-list">
                <label class="radio-inline">{!! Form::radio('status', '1', true) !!} {!! trans('admin::messages.active') !!}</label>
                <label class="radio-inline">{!! Form::radio('status', '0') !!} {!! trans('admin::messages.inactive') !!}</label>
            </div>
        </div>
    </div>
</div>
