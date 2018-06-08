<div class="form-body">
    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                <label class="col-md-4 control-label">{!! trans('admin::controller/staff-availability.availability_date') !!}<span class="required" aria-required="true">*</span></label>
                <div class="col-md-8">
                    <div id="dates" type="text" class="datepicker"></div>
                    {!! Form::hidden('availability_date', null, ['id'=>'availability_date', 'data-rule-required'=>'true', 'data-msg-required'=>trans('admin::messages.required-select', ['name' => trans('admin::controller/staff-availability.availability_date')])])!!}
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group" id="center_drop_down">
                <label class="col-md-5 control-label" style="text-align:left !important;">{!! trans('admin::controller/staff-availability.center') !!}<span class="required" aria-required="true">*</span> </label>
                <div class="col-md-4">
                    {!! Form::select('center_id', [''=>'Select Center'] + $centerList, null,['autocomplete' => 'off','class'=>'select2me form-control center_id', 'id' => 'center_id', 'data-rule-required'=>'true', 'data-msg-required'=>trans('admin::messages.required-enter', ['name' => trans('admin::controller/staff-availability.center')])]) !!}
                    <!--<span id="start_time-error" class="help-block help-block-error">Select Center.</span>-->
                </div>
            </div>
            <div class="form-group" id="staff-drop-down">
                @include('admin::staff-availability.staffdropdown')
            </div>
            <div class="form-group">
                <label class="col-md-5 control-label" style="text-align:left !important;">{!! trans('admin::controller/staff-availability.start-time') !!}<span class="required">*</span> </label>
                <div class="col-md-4">
                    {!! Form::text('start_time', null, ['class'=>'form-control staff_start_time', 'readonly'=>'true', 'id'=>'staff_start_time','data-rule-required'=>'true', 'data-msg-required'=>trans('admin::messages.required-select', ['name' => trans('admin::controller/staff-availability.start-time')])])!!}
                    <span id="staff_start_time_error" class="help-block help-block-error"></span>
                </div>
            </div>
            <div class="form-group">
                <label class="col-md-5 control-label" style="text-align:left !important;">{!! trans('admin::controller/staff-availability.end-time') !!}<span class="required">*</span> </label>
                <div class="col-md-4">
                    {!! Form::text('end_time', null, ['class'=>'form-control staff_end_time', 'readonly'=>'true', 'id'=>'staff_end_time','data-rule-required'=>'true', 'data-msg-required'=>trans('admin::messages.required-select', ['name' => trans('admin::controller/staff-availability.end-time')])])!!}
                    <span id="staff_end_time_error" class="help-block help-block-error"></span>
                    {!! Form::hidden('session_booking_start_time', date('h:i A', strtotime($arrTimes['start_time'])), ['class'=>'form-control', 'id' => 'config_session_start_time']) !!}
                    {!! Form::hidden('session_booking_end_time', date('h:i A', strtotime($arrTimes['end_time'])), ['class'=>'form-control', 'id' => 'config_session_end_time']) !!}
                    {!! Form::hidden('session_booking_end_time_for_start_time', date('h:i A', strtotime($arrTimes['end_time'])-3600), ['class'=>'form-control', 'id' => 'session_booking_end_time_for_start_time']) !!}
                </div>
            </div>
            <div class="form-group">
                <label class="col-md-5 control-label" style="text-align:left !important;">{!! trans('admin::controller/staff-availability.break-time') !!}<!--<span class="required" aria-required="true">*</span>--> </label>
                <div class="col-md-4">
                    {!! Form::text('break_time', null, ['class'=>'form-control', 'id'=>'break_time', 'readonly'=>'true'])!!}
                    <span id="break_time_error" class="help-block help-block-error"></span>
                </div>
            </div>
            <div class="form-group">
                <label class="col-md-5 control-label" style="text-align:left !important;">{!! trans('admin::controller/staff-availability.carry-forwarded') !!} </label>
                <div class="col-md-4">
                    <div class="radio-list">
                        <label class="radio-inline radio-container">{!! Form::radio('carry_forward_availability', '1') !!} {!! trans('admin::controller/staff-availability.yes') !!}</label>
                        <label class="radio-inline radio-container">{!! Form::radio('carry_forward_availability', '0', true) !!} {!! trans('admin::controller/staff-availability.no') !!}</label>
                    </div>
                </div>
            </div>
            <div class="form-group">
                <label class="col-md-5 control-label" style="text-align:left !important;">{!! trans('admin::controller/staff-availability.carry-forwarded-days') !!}</label>
                <div class="col-md-4">
                    {!! Form::text('carry_forward_availability_days', null, ['class'=>'form-control', 'id'=>'carry_forwarded_days', 'disabled'=>'disabled', 'data-rule-required'=>'true', 'data-msg-required'=>trans('admin::messages.required-enter', ['name' => trans('admin::controller/staff-availability.carry-forwarded-days')])])!!}
                </div>
            </div>
        </div>
        <div id='output'></div>
    </div>
</div>