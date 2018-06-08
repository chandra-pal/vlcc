<div class="form-body">
    <div class="form-group">
        <label class="col-md-3 control-label">{!! trans('admin::controller/diet-schedule-type.schedule_name') !!}<span class="required" aria-required="true">*</span></label>
        <div class="col-md-4">
            {!! Form::text('schedule_name', null, ['minlength'=>2,'maxlength'=>50,'class'=>'form-control', 'id'=>'schedule_name', 'data-rule-required'=>'true', 'data-msg-required'=>trans('admin::messages.required-enter', ['name' => trans('admin::controller/diet-schedule-type.schedule_name')]), 'data-rule-maxlength'=>'50', 'data-msg-maxlength'=>trans('admin::messages.error-maxlength', ['name'=>trans('admin::controller/diet-schedule-type.schedule_name')]) , 'data-msg-minlength'=>trans('admin::messages.error-minlength', ['name' => trans('admin::controller/diet-schedule-type.schedule_name')]) ])!!}
            <span class="help-block">eg: Morning</span>
        </div>
    </div>

    <div class="form-group">
        <label class="col-md-3 control-label">{!! trans('admin::controller/diet-schedule-type.start-time') !!}<span class="required" aria-required="true">*</span></label>
        <div class="col-md-4">
            {!! Form::text('start_time', null, ['class'=>'form-control', 'readonly'=>'true', 'id'=>'start_time', 'data-rule-required'=>'true', 'data-msg-required'=>trans('admin::messages.required-select', ['name' => trans('admin::controller/diet-schedule-type.start-time')])])!!}
            <span id="start_time-error" class="help-block help-block-error"></span>
        </div>
    </div>

    <div class="form-group">
        <label class="col-md-3 control-label">{!! trans('admin::controller/diet-schedule-type.end-time') !!}<span class="required" aria-required="true">*</span></label>
        <div class="col-md-4">
            {!! Form::text('end_time', null, ['class'=>'form-control', 'readonly'=>'true', 'id'=>'end_time', 'data-rule-required'=>'true', 'data-msg-required'=>trans('admin::messages.required-select', ['name' => trans('admin::controller/diet-schedule-type.end-time')])])!!}
            <span id="end_time-error" class="help-block help-block-error"></span>
        </div>
    </div>

    <div class="form-group">
        <label class="col-md-3 control-label">{!! trans('admin::controller/diet-schedule-type.status') !!}<span class="required" aria-required="true">*</span> </label>
        <div class="col-md-4">
            <div class="radio-list">
                <label class="radio-inline">{!! Form::radio('status', '1', true) !!} {!! trans('admin::messages.active') !!}</label>
                <label class="radio-inline">{!! Form::radio('status', '0') !!} {!! trans('admin::messages.inactive') !!}</label>
            </div>
        </div>
    </div>
</div>