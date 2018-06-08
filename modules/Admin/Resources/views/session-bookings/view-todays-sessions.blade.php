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
{!! HTML::style( URL::asset('css/admin/admin-user.css') ) !!}
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
<div id="ajax-response-text"></div>

{{--*/ $linkIcon = \Modules\Admin\Services\Helper\MenuHelper::getSelectedPageLinkIcon() /*--}}
<div id="edit_form">

</div>
<div class="portlet light col-lg-12">
    <div class="portlet-title">
        <div class="caption">
            <i class="fa {{$linkIcon}} font-blue-sharp"></i>
            <span class="caption-subject font-blue-sharp bold uppercase">{!! trans('admin::messages.view-name',['name'=>trans('admin::controller/session-bookings.view-sessions')]) !!}</span>
        </div>
        
    </div>
    <div class="portlet-body">
        <div class="table-container">
            <div class="table-actions-wrapper" id="View_Today's_Sessions_submenu">
                <span></span>
                <input type="hidden" name="_token" value="{!! csrf_token() !!}">
                <!--<input id="data-search" type="search" class="form-control" placeholder="Search">-->
            </div>
            <table class="table table-striped table-bordered table-hover" id="todays-sessions-table">
                <thead>
                    <tr role="row" class="heading">
                        <th width='1%'>#</th>
                        <th width='15%'>{!! trans('admin::controller/session-bookings.select-customer') !!}</th>
                        <th width='15%'>{!! trans('admin::controller/session-bookings.center-name') !!}</th>
                        <th width='15%'>{!! trans('admin::controller/session-bookings.session-date') !!}</th>
                        <th width='15%'>{!! trans('admin::controller/session-bookings.select-service') !!}</th>
                        <th width='15%'>{!! trans('admin::controller/session-bookings.session-start-time') !!}</th>
                        <th width='15%'>{!! trans('admin::controller/session-bookings.session-end-time') !!}</th>
                        <th width='15%'>{!! trans('admin::controller/session-bookings.session-comment') !!}</th>
                        <th width='15%'>{!! trans('admin::controller/session-bookings.session-status') !!}</th>
                        <th width='15%'>Action</th>
                    </tr>
                </thead>
                <tbody>

                </tbody>
            </table>
        </div>
    </div>
</div>
@stop
