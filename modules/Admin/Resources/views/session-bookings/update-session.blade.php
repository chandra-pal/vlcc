@extends('admin::layouts.master')

@section('template-level-scripts')
@parent
<script src="//cdnjs.cloudflare.com/ajax/libs/moment.js/2.9.0/moment.min.js"></script>
<script src="//cdnjs.cloudflare.com/ajax/libs/fullcalendar/2.2.7/fullcalendar.min.js"></script>
<link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/fullcalendar/2.2.7/fullcalendar.min.css"/>
{!! HTML::script( URL::asset('js/admin/session-bookings.js') ) !!}
@stop

@section('page-level-styles')
@parent
{!! HTML::style( URL::asset('global/plugins/bootstrap-datepicker/css/bootstrap-datepicker3.min.css') ) !!}
<link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/timepicker/1.3.5/jquery.timepicker.min.css">
{!! HTML::style( URL::asset('css/admin/admin-user.css') ) !!}
@stop

@section('scripts')
@parent
{!! HTML::script( URL::asset('global/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js') ) !!}
<script src="//cdnjs.cloudflare.com/ajax/libs/timepicker/1.3.5/jquery.timepicker.min.js"></script>
<script>
    var session_page_type = "notification";
    jQuery(document).ready(function () {
        siteObjJs.admin.sessionBookingsJs.init();
        siteObjJs.admin.commonJs.boxExpandBtnClick();
        //siteObjJs.validation.formValidateInit('#edit-session-bookings', handleAjaxRequest());
    });
</script>
@stop

@section('content')
<?php
$update_session = 1;
$flag = 'add-session';
?>
@include('admin::partials.breadcrumb',['curRoute'=>'admin.session-bookings.index'])
<div id="ajax-response-text">

</div>

@if(!empty(Auth::guard('admin')->user()->hasAdd))
@include('admin::session-bookings.create',['update_session' => $update_session, 'flag' => $flag])
@endif

{{--*/ $linkIcon = \Modules\Admin\Services\Helper\MenuHelper::getSelectedPageLinkIcon() /*--}}

<div id="edit_form">
    <input type="hidden" id="session_notification_flag" value="1" name="session_notification_flag">
    @include('admin::session-bookings.edit', ['update_session' => $update_session, 'flag' => 'update-session'])
</div>

<div class="portlet light col-lg-12">
    <div class="portlet-title">
        <div class="caption">
            <i class="fa {{$linkIcon}} font-blue-sharp"></i>
            <span class="caption-subject font-blue-sharp bold uppercase">{!! trans('admin::messages.view-name',['name'=>trans('admin::controller/session-bookings.session-bookings')]) !!}</span>
        </div>
        @if(!empty(Auth::guard('admin')->user()->hasAdd))
        <div class="actions">
            <a href="javascript:;" class="btn blue btn-add-big btn-expand-form"><i class="fa fa-plus"></i><span class="hidden-480">Add Slimming Appointment</span></a>
        </div>
        @endif

        <div class="actions" style="margin-right: 35px;">
            <input type="hidden" id="logged_in_user_type_id" name="logged_in_user_type_id" value="{{Auth::guard('admin')->user()->userType->id}}">
            <a href="../view-todays-sessions" class="btn blue btn-add-big">
                <span class="hidden-480">View Todays Slimming Appointments</span></a>
        </div>

    </div>
    <div class="portlet-body">
        <div class="table-container">
            <div class="table-actions-wrapper">
                <span></span>
                <input type="hidden" name="_token" value="{!! csrf_token() !!}">
                <input id="data-search" type="search" class="form-control" placeholder="Search">
            </div>
            <input type="hidden" id="minTime" value="{!! $arrTimes['start_time'] !!}"/>
            <input type="hidden" id="maxTime" value="{!! $arrTimes['end_time'] !!}"/>
            <input type="hidden" id="maxTime1" value="{!! $arrTimes['end_time_session_calendar'] !!}"/>
            <div id="fullcalendar"></div>

            <div class="session-color-codes">
                <div class="colordvcon">
                    <div class="colordva" style="background-color:#E87E04;"> </div>
                    <p>Requested</p>
                </div>
                <div class="colordvcon">
                    <div class="colordva" style="background-color:#2AB4C0;"> </div>
                    <p>Booked</p>
                </div>
                <div class="colordvcon">
                    <div class="colordva" style="background-color:#E43A45;"> </div>
                    <p> Rejected</p>
                </div>
                <div class="colordvcon">
                    <div class="colordva" style="background-color:#525E64;"> </div>
                    <p>Cancelled</p>
                </div>
                <div class="colordvcon">
                    <div class="colordva" style="background-color:#3598DC;"> </div>
                    <p>Completed</p>
                </div>
            </div>

        </div>
    </div>
</div>
@stop
