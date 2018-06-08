<div class="form-body">
    <!--    <div class="form-group">
            <label class="col-md-3 control-label">{!! trans('admin::controller/session-bookings.select-customer') !!}<span class="required" aria-required="true">*</span></label>
            <div class="col-md-4">
    <?php $selectedMember = Session::get('member_id') ? Session::get('member_id') : null; ?>
                @if(isset($membersList))
                {!! Form::select('member_id', [''=> trans('admin::messages.select-name', [ 'name' => trans('admin::controller/member.member') ])] + $membersList, null,['class'=>'select2me form-control select-customer', 'id' => 'customer_select', 'data-rule-required'=>'true', 'data-msg-required'=>trans('admin::messages.required-select', ['name' => trans('admin::controller/session-bookings.select-customer')])]) !!}
                <span class="help-block help-block-error customer_error"></span>
                @endif
            </div>
        </div>-->

    @if(isset($sessionBookings->member_id))
    <input type="hidden" id="member_id" name="member_id" value="{{$sessionBookings->member_id}}">
    @endif

    <div class="form-group package_information">
        <label class="col-md-3 control-label">{!! trans('admin::controller/session-bookings.select-package') !!}<span class="required" aria-required="true">*</span></label>
        <div class="col-md-4">
            {!! Form::select('package_id', [''=> trans('admin::messages.select-name', [ 'name' => trans('admin::controller/session-bookings.select-package') ])] + $packageList, null,['class'=>'select2me form-control form-filter select-package', 'id' => 'package_id', 'data-rule-required'=>'true', 'data-msg-required'=>'Please select Package.']) !!}

            <span class="help-block help-block-error customer_error"></span>
        </div>
    </div>
    <div class="form-group service_information">
        <label class="col-md-3 control-label">{!! trans('admin::controller/session-bookings.select-service') !!}<span class="required" aria-required="true">*</span></label>
        <div class="col-md-4">
            {!! Form::select('service_id[]', [''=> trans('admin::messages.select-name', [ 'name' => trans('admin::controller/session-bookings.select-service') ])] + $serviceList, $selectedSessionServices,['class'=>'select2me form-control form-filter select-service', 'id' => 'service_id', 'data-rule-required'=>'true', 'data-msg-required'=>'Please select Service.']) !!}
            <span class="help-block help-block-error customer_error"></span>
        </div>
    </div>

    <div class="form-group">
        <label class="col-md-3 control-label">{!! trans('admin::controller/session-bookings.session-date') !!}
            <span class="required"> * </span>
        </label>
        <div class="col-md-4">
            <div class="input-group date form_datetime margin-bottom-5" data-date-start-date="+0d"  data-date="{{date('Y-m-d h:i:s')}}">
                {!! Form::text('session_date', null, ['id' => 'session_date', 'class'=>'form-control session_date','readonly'=>'true', 'data-rule-required'=>'true', 'data-msg-required'=>'Please select Session Date.']) !!}
                <span class="input-group-btn">
                    <button class="btn default date-set btn-sm" type="button" style="display: none"><i class="fa fa-calendar"></i></button>
                </span>
            </div>
        </div>
    </div>

    <div class="form-group">
        <label class="col-md-3 control-label">{!! trans('admin::controller/session-bookings.session-start-time') !!}
            <span class="required"> * </span>
        </label>
        <div class="col-md-4">
            {!! Form::text('start_time', null, ['class'=>'form-control session_start_time', 'id'=>'session_start_time', 'data-rule-required'=>'true', 'data-msg-required'=>trans('admin::messages.required-select', ['name' => trans('admin::controller/session-bookings.session-start-time')])])!!}
            <p id="start_time_display" class="help-block help-block-error session-validation"></p>
        </div>
    </div>

    <div class="form-group">
        <label class="col-md-3 control-label">{!! trans('admin::controller/session-bookings.session-end-time') !!}
            <span class="required"> * </span>
        </label>

        <div class="col-md-4">
            {!! Form::text('end_time', null, ['class'=>'form-control', 'id'=>'session_end_time', 'data-rule-required'=>'true','data-msg-required'=>trans('admin::messages.required-select', ['name' => trans('admin::controller/session-bookings.session-end-time')])])!!}
            <p id="end_time_display" class="help-block help-block-error session-validation"></p>
            {!! Form::hidden('dietician_id', $dieticianId, ['class'=>'form-control', 'id'=>'dietician_id']) !!}
            {!! Form::hidden('session_booking_start_time', date('h:i A', strtotime($arrTimes['start_time'])), ['class'=>'form-control', 'id' => 'config_session_start_time']) !!}
            {!! Form::hidden('session_booking_end_time', date('h:i A', strtotime($arrTimes['end_time'])), ['class'=>'form-control', 'id' => 'config_session_end_time']) !!}
            {!! Form::hidden('session_booking_end_time_for_start_time', date('h:i A', strtotime($arrTimes['end_time'])-3600), ['class'=>'form-control', 'id' => 'session_booking_end_time_for_start_time']) !!}

            @if($flag == "add-session")
            {!! Form::hidden('status', 2, ['class'=>'form-control', 'id'=>'status']) !!}
            @endif

            {!! Form::hidden('doctor_id', 1, ['class'=>'form-control', 'id'=>'doctor_id']) !!}
            {!! Form::hidden('physiotherpist_id', 1, ['class'=>'form-control', 'id'=>'physiotherpist_id']) !!}
        </div>
    </div>


    {!! Form::hidden('ola_cab_required', 0, ['id'=>'ola_cab_required']) !!}

    <!--    <div class="form-group">
            <label class="col-md-3 control-label">{!! trans('admin::controller/session-bookings.ola-cab') !!}<span class="required" aria-required="true">*</span></label>
            <div class="col-md-4">
                {!! Form::select('ola_cab_required', [''=> trans('admin::messages.select-name', [ 'name' => trans('admin::controller/session-bookings.ola-cab') ])] + $olaCabRequired, null,['class'=>'select2me form-control form-filter', 'id' => 'ola_cab_required', 'data-rule-required'=>'true', 'data-msg-required'=>'Please select Ola Cab Required.']) !!}

                <span class="help-block help-block-error customer_error"></span>
            </div>
        </div>-->

    @if($flag == "update-session")
    <div class="form-group">
        <label class="col-md-3 control-label">{!! trans('admin::controller/session-bookings.session-status') !!}<span class="required" aria-required="true">*</span></label>
        <div class="col-md-4">
            {!! Form::select('status', [''=> trans('admin::messages.select-name', [ 'name' => trans('admin::controller/session-bookings.session-status') ])] + $sessionStatus, null,['class'=>'select2me form-control form-filter session_status', 'id' => 'status', 'data-rule-required'=>'true', 'data-msg-required'=>'Please select Session status.']) !!}
            <span class="help-block help-block-error customer_error"></span>
        </div>
    </div>
    {!! Form::hidden('previous_session_date', $sessionBookings->session_date) !!}
    {!! Form::hidden('previous_start_time', $sessionBookings->start_time) !!}
    {!! Form::hidden('previous_end_time', $sessionBookings->end_time) !!}
    {!! Form::hidden('previous_status', $sessionBookings->status) !!}
    @endif

    @if($flag == "update-session" && $sessionBookings->status == "4" && !empty($sessionBookings->cancellation_comment))
    <div class="form-group session_cancellatiom_comment">
        <label class="control-label col-md-3">{!! trans('admin::controller/session-bookings.session-cancellation-comment') !!}
        </label>
        <div class="col-md-4">
            {!! Form::textarea('cancellation_comment', null, ['rows'=>5, 'class'=>'form-control cancellation_comment','id'=>'cancellation_comment','data-rule-required'=>'false','data-msg-required'=>trans('admin::messages.required-enter', ['name' => trans('admin::controller/session-bookings.session-cancellation-comment')])]) !!}
        </div>
    </div>
    @else
    <div class="form-group session_cancellatiom_comment" style="display:none">
        <label class="control-label col-md-3">{!! trans('admin::controller/session-bookings.session-cancellation-comment') !!}
        </label>
        <div class="col-md-4">
            {!! Form::textarea('cancellation_comment', null, ['rows'=>5, 'class'=>'form-control cancellation_comment','id'=>'cancellation_comment','data-rule-required'=>'false','data-msg-required'=>trans('admin::messages.required-enter', ['name' => trans('admin::controller/session-bookings.session-cancellation-comment')])]) !!}
        </div>
    </div>
    @endif

    <div class="form-group" id="staff_list"> @include('admin::session-bookings.staff') </div>

    <div class="form-group" id="machine_list"> @include('admin::session-bookings.machine') </div>

    <div class="form-group" id="room_list"> @include('admin::session-bookings.room') </div>



</div>