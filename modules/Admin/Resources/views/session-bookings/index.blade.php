@extends('admin::layouts.master')

@section('template-level-styles')
@parent
{!! HTML::style( URL::asset('global/plugins/bootstrap-datepicker/css/bootstrap-datepicker3.min.css') ) !!}
{!! HTML::style( URL::asset('css/admin/admin-user.css') ) !!}
{!! HTML::style( URL::asset('global/plugins/fullcalender/fullcalendar.min.css') ) !!}
{!! HTML::style( URL::asset('global/plugins/fullcalender/scheduler.min.css') ) !!}
@stop

@section('template-level-scripts')
@parent
{!! HTML::script( URL::asset('global/plugins/fullcalender/moment.min.js') ) !!}
{!! HTML::script( URL::asset('global/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js') ) !!}
{!! HTML::script( URL::asset('global/plugins/fullcalender/fullcalendar.min.js') ) !!}
{!! HTML::script( URL::asset('global/plugins/fullcalender/scheduler.min.js') ) !!}
{!! HTML::script( URL::asset('js/admin/session-bookings.js') ) !!}
@stop

@section('page-level-styles')
@parent
{!! HTML::style( URL::asset('global/plugins/bootstrap-datepicker/css/bootstrap-datepicker3.min.css') ) !!}
<link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/timepicker/1.3.5/jquery.timepicker.min.css">
{!! HTML::style( URL::asset('css/admin/admin-user.css') ) !!}
@stop

@section('styles')
<style>
    .fc-time-grid-event.fc-short .fc-time:before, .fc-time-grid-event.fc-short .fc-time:after {
        display: none !important;
    }

    .fc-time-grid-event.fc-short .fc-time span {
        display: block;
    }
</style>
@stop
@section('scripts')
@parent
{!! HTML::script( URL::asset('global/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js') ) !!}
<script src="//cdnjs.cloudflare.com/ajax/libs/timepicker/1.3.5/jquery.timepicker.min.js"></script>
<script>
    var session_page_type = "index";
    jQuery(document).ready(function () {
        siteObjJs.admin.sessionBookingsJs.init();
        siteObjJs.admin.commonJs.boxExpandBtnClick();
    });
</script>
@stop

@section('content')
@include('admin::partials.breadcrumb')
<div id="ajax-response-text">

</div>

@if(!empty(Auth::guard('admin')->user()->hasAdd))
@include('admin::session-bookings.create')
@include('admin::session-bookings.history')
@endif
{{--*/ $linkIcon = \Modules\Admin\Services\Helper\MenuHelper::getSelectedPageLinkIcon() /*--}}

<div id="edit_form" style="text-align: center;margin-bottom: 10px;"></div>
<div> <input type="hidden" id="center_id" name="center_id"> </div>
<!--<div id="myModal1" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
            </div>
            <div class="modal-body">
                <div id="availability_calendar"></div>
            </div>
            <div class="modal-footer">
                                <button class="btn default" data-dismiss="modal" aria-hidden="true">Close</button>
                                <button class="btn yellow">Save</button>
            </div>
        </div>
    </div>
</div>-->
<a href="#myModal1" role="button" class="btn blue" data-toggle="modal" style="display: none;" id="modelbtn"> Modal Dialog </a>


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
            <input type="hidden" id="logged_in_user_id" name="logged_in_user_id" value="{{Auth::guard('admin')->user()->id}}">

            <a href="./view-todays-sessions" class="btn blue btn-add-big"><span class="hidden-480">View Todays Appointments</span></a>
        </div>
        <div class="actions" style="margin-right: 35px; ">
             <a href="javascript:;" class="btn blue btn-add-big btn-expand-history"><span class="hidden-480">Previous Booking History</span></a>
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
                <div class="colordvcon">
                    <div class="colordva" style="background-color:#f4df41;"> </div>
                    <p>Waiting List</p>
                </div>
                <div class="colordvcon">
                    <div class="colordva" style="background-color:#83f442;"> </div>
                    <p>Confirmed</p>
                </div>
                <div class="colordvcon">
                    <div class="colordva" style="background-color:#f441c4;"> </div>
                    <p>No Response</p>
                </div>
            </div>

        </div>
    </div>
</div>
@stop
