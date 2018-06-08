<div class="form-body">
    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                <label class="col-md-4 control-label">{!! trans('admin::controller/availability.availability-date') !!}<span class="required" aria-required="true">*</span></label>
                <div class="col-md-8">
                    <div id="dates" type="text" class="datepicker"></div>
                    {!! Form::hidden('availability_date', null, ['id'=>'availability_date', 'data-rule-required'=>'true', 'data-msg-required'=>trans('admin::messages.required-select', ['name' => trans('admin::controller/availability.availability-date')])])!!}
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label class="col-md-3 control-label">{!! trans('admin::controller/availability.start-time') !!}<span class="required" aria-required="true">*</span> </label>
                <div class="col-md-4">
                    {!! Form::text('start_time', null, ['class'=>'form-control availability-time', 'id'=>'start_time', 'disabled'=>'disabled','data-rule-required'=>'true', 'data-msg-required'=>trans('admin::messages.required-enter', ['name' => trans('admin::controller/availability.start-time')])])!!}
                    <span id="start_time-error" class="help-block help-block-error"></span>
                </div>
            </div>
            <div class="form-group">
                <label class="col-md-3 control-label">{!! trans('admin::controller/availability.end-time') !!}<span class="required" aria-required="true">*</span> </label>
                <div class="col-md-4">
                    {!! Form::text('end_time', null, ['class'=>'form-control availability-time', 'id'=>'end_time', 'disabled'=>'disabled', 'data-rule-required'=>'true', 'data-msg-required'=>trans('admin::messages.required-enter', ['name' => trans('admin::controller/availability.end-time')])])!!}
                    <span id="end_time-error" class="help-block help-block-error"></span>
                </div>
            </div>
            <div class="form-group">
                <label class="col-md-3 control-label">{!! trans('admin::controller/availability.break-time') !!}<span class="required" aria-required="true">*</span> </label>
                <div class="col-md-4">
                    {!! Form::text('break_time', null, ['class'=>'form-control', 'id'=>'break_time', 'disabled'=>'disabled', 'data-rule-required'=>'true', 'data-msg-required'=>trans('admin::messages.required-enter', ['name' => trans('admin::controller/availability.break-time')])])!!}
                    <span id="break_time-error" class="help-block help-block-error"></span>
                </div>
            </div>
            <div id='carry_forward_block'>
                <div class="form-group">
                    <label class="col-md-3 control-label">{!! trans('admin::controller/availability.carry-forwarded') !!} </label>
                    <div class="col-md-4">
                        <div class="radio-list">
                            <label class="radio-inline">{!! Form::radio('carry_forward_availability', '1') !!} {!! trans('admin::controller/availability.yes') !!}</label>
                            <label class="radio-inline">{!! Form::radio('carry_forward_availability', '0', true) !!} {!! trans('admin::controller/availability.no') !!}</label>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-md-3 control-label">{!! trans('admin::controller/availability.carry-forwarded-days') !!}</label>
                    <div class="col-md-4">
                        {!! Form::text('carry_forward_availability_days', null, ['class'=>'form-control', 'id'=>'carry_forwarded_days', 'disabled'=>'disabled', 'data-rule-required'=>'true', 'data-msg-required'=>trans('admin::messages.required-enter', ['name' => trans('admin::controller/availability.carry-forwarded-days')])])!!}
                    </div>
                </div>
            </div>
        </div>
        <div id='output'></div>
    </div>
</div>