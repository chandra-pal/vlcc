
        <?php $selectedMember = Session::get('member_id') ? Session::get('member_id') : null; ?>
        @if(isset($sessionBookings->member_id))
        <input type="hidden" id="member_id" name="member_id" value="{{$sessionBookings->member_id}}">
        <input type="hidden" id="session_id" name="session_id" value="{{$sessionBookings->id}}">
        @endif
    <tr role="row" class="heading">
        <td>
            {!! trans('admin::controller/session-bookings.select-package') !!}<span class="required" aria-required="true" style="color: #e02222;">*</span>
        </td>
        <td colspan="2">
            <div class="package_information">
                {!! Form::select('package_id', [''=> trans('admin::messages.select-name', [ 'name' => trans('admin::controller/session-bookings.select-package') ])] + $packageList, null,['class'=>'select2me form-control form-filter select-package', 'id' => 'package_id', 'data-rule-required'=>'true', 'data-msg-required'=>'Please select Package.']) !!}
                <span class="help-block help-block-error customer_error"></span>
            </div>
        </td>
        <td>
            {!! trans('admin::controller/session-bookings.select-service') !!}<span class="required" aria-required="true" style="color: #e02222;">*</span>
        </td>
        <td colspan="2">
            <div class="service_information">
                {!! Form::select('service_id[]', [''=> trans('admin::messages.select-name', [ 'name' => trans('admin::controller/session-bookings.select-service') ])] + $serviceList, $selectedSessionServices,['multiple'=>'multiple', 'class'=>'select2me form-control form-filter select-service', 'id' => 'service_id', 'data-rule-required'=>'true', 'data-msg-required'=>'Please select Service.']) !!}
                <span class="help-block help-block-error customer_error"></span>
            </div>
        </td>
    </tr>

    <tr>
        <td>{!! trans('admin::controller/session-bookings.session-date') !!}
            <span class="required" style="color: #e02222;"> * </span></td>
        <td>
            <div class="input-group date form_datetime margin-bottom-5" data-date-start-date="+0d"  data-date="{{date('Y-m-d h:i:s')}}">
                {!! Form::text('session_date', null, ['id' => 'session_date', 'class'=>'form-control session_date','readonly'=>'true', 'data-rule-required'=>'true', 'data-msg-required'=>'Please select Session Date.']) !!}
                <span class="input-group-btn">
                    <button class="btn default date-set btn-sm" type="button" style="display: none"><i class="fa fa-calendar"></i></button>
                </span>

            </div>
            <p id="session_date_error" class="help-block help-block-error"></p>
        </td>

        <td>{!! trans('admin::controller/session-bookings.session-start-time') !!}
            <span class="required" style="color: #e02222;"> * </span></td>
        <td>{!! Form::text('start_time', null, ['class'=>'form-control session_start_time', 'id'=>'session_start_time', 'data-rule-required'=>'true', 'data-msg-required'=>trans('admin::messages.required-select', ['name' => trans('admin::controller/session-bookings.session-start-time')])])!!}
            <p id="start_time_display" class="help-block help-block-error session-validation"></p>
        </td>
        <td>
            {!! trans('admin::controller/session-bookings.session-end-time') !!}
            <span class="required" style="color: #e02222;"> * </span>
        </td>
        <td>
            {!! Form::text('end_time', null, ['class'=>'form-control', 'id'=>'session_end_time', 'data-rule-required'=>'true','data-msg-required'=>trans('admin::messages.required-select', ['name' => trans('admin::controller/session-bookings.session-end-time')])])!!}
            <p id="end_time_display" class="help-block help-block-error session-validation"></p>
            {!! Form::hidden('dietician_id', $dieticianId, ['class'=>'form-control', 'id'=>'dietician_id']) !!}
            {!! Form::hidden('session_booking_start_time', date('h:i A', strtotime($arrTimes['start_time'])), ['class'=>'form-control', 'id' => 'config_session_start_time']) !!}
            {!! Form::hidden('session_booking_end_time', date('h:i A', strtotime($arrTimes['end_time'])), ['class'=>'form-control', 'id' => 'config_session_end_time']) !!}
            {!! Form::hidden('session_booking_end_time_for_start_time', date('h:i A', strtotime($arrTimes['end_time'])-3600), ['class'=>'form-control', 'id' => 'session_booking_end_time_for_start_time']) !!}

            {{--@if($flag == "add-session")
            {!! Form::hidden('status', 2, ['class'=>'form-control', 'id'=>'status']) !!}
            @endif--}}

            {!! Form::hidden('doctor_id', 1, ['class'=>'form-control', 'id'=>'doctor_id']) !!}
            {!! Form::hidden('physiotherpist_id', 1, ['class'=>'form-control', 'id'=>'physiotherpist_id']) !!}
            {!! Form::hidden('ola_cab_required', 0, ['id'=>'ola_cab_required']) !!}
        </td>
    </tr>



    <tr>
        <td>{!! trans('admin::controller/session-bookings.session-status') !!}<span class="required" aria-required="true" style="color: #e02222;">*</span></label></td>
        <td colspan="5">{!! Form::select('status', [''=> trans('admin::messages.select-name', [ 'name' => trans('admin::controller/session-bookings.session-status') ])] + $sessionStatus, null,['class'=>'select2me form-control form-filter session_status', 'id' => 'status', 'data-rule-required'=>'true', 'data-msg-required'=>'Please select Session status.']) !!}
            @if($flag == "update-session")
            <span class="help-block help-block-error customer_error"></span>
            {!! Form::hidden('previous_session_date', $sessionBookings->session_date) !!}
            {!! Form::hidden('previous_start_time', $sessionBookings->start_time) !!}
            {!! Form::hidden('previous_end_time', $sessionBookings->end_time) !!}
            {!! Form::hidden('previous_status', $sessionBookings->status) !!}
            @endif
        </td>

    </tr>

    @if($flag == "update-session" && $sessionBookings->status == "4" && !empty($sessionBookings->cancellation_comment))
    <tr class="session_cancellatiom_comment">
        <td>{!! trans('admin::controller/session-bookings.session-cancellation-comment') !!}</td>
        <td>{!! Form::textarea('cancellation_comment', null, ['rows'=>5, 'class'=>'form-control cancellation_comment','id'=>'cancellation_comment','data-rule-required'=>'false','data-msg-required'=>trans('admin::messages.required-enter', ['name' => trans('admin::controller/session-bookings.session-cancellation-comment')])]) !!}</td>
        <td colspan="4"><div></div></td>
    </tr>
    @else
    <tr class="session_cancellatiom_comment" style="display:none">
        <td>{!! trans('admin::controller/session-bookings.session-cancellation-comment') !!}</td>
        <td>{!! Form::textarea('cancellation_comment', null, ['rows'=>5, 'class'=>'form-control cancellation_comment','id'=>'cancellation_comment','data-rule-required'=>'false','data-msg-required'=>trans('admin::messages.required-enter', ['name' => trans('admin::controller/session-bookings.session-cancellation-comment')])]) !!}</td>
        <td ><div></div></td>
    </tr>
    @endif

    <tr>
        <td>{!! trans('admin::controller/session-bookings.staff') !!}<span class="required" aria-required="true" style="color: #e02222;">*</span></td>
        <td id="staff_list" width="200"> @include('admin::session-bookings.staff') </td>
        <td colspan="4"><div id="staff_calender"></div></td>
    </tr>
    <tr class="session_tr_machine">
        <td>{!! trans('admin::controller/session-bookings.machine') !!}<span class="required" aria-required="true" style="color: #e02222;">*</span></td>
        <td id="machine_list"> @include('admin::session-bookings.machine') </td>
        <td colspan="4"><div id="machine_calender"></div></td>
    </tr>
    <tr class="session_tr_room">
        <td>{!! trans('admin::controller/session-bookings.room') !!}<span class="required" aria-required="true" style="color: #e02222;">*</span></td>
        <td id="room_list"> @include('admin::session-bookings.room') </td>
        <td colspan="4"><div id="room_calender"></div></td>
    </tr>
    <tr>
        <td>{!! trans('admin::controller/session-bookings.session-comment') !!}</td>
        <td id="room_list">{!! Form::textarea('session_comment', null, ['rows'=>'3','minlength'=>2,'maxlength'=>350,'class'=>'form-control min-one-required', 'id'=>'session_comment', 'data-rule-maxlength'=>'350', 'data-msg-maxlength'=>trans('admin::messages.error-maxlength', ['name'=>trans('admin::controller/session-bookings.session-comment')]) , 'data-msg-minlength'=>trans('admin::messages.error-minlength', ['name' => trans('admin::controller/session-bookings.session-comment')]) ])!!}</td>
        <td colspan="4"><div></div></td>
    </tr>

    <tr>

        <td>{!! Form::checkbox('sms_send', 1, true) !!}</td>
        <td>{!! trans('admin::controller/session-bookings.session-send-sms') !!}</td>
        <td colspan="4"><div></div></td>
    </tr>
