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
{!! HTML::script( URL::asset('js/admin/session-resources.js') ) !!}
@stop

@section('styles')
<style>
    /*    .fc-axis, .fc-axis.fc-widget-header {
            width: 44px !important;
        }*/
    .fc td, .fc th {
        white-space: pre-line;
        word-wrap: break-word;
    }
</style>
@stop

@section('scripts')
@parent
<script>
    jQuery(document).ready(function () {
        siteObjJs.admin.sessionResourcesJs.init();
        siteObjJs.admin.commonJs.boxExpandBtnClick();
    });
</script>
@stop

@section('content')
@include('admin::partials.breadcrumb')
<div id="ajax-response-text"></div>

{{--*/ $linkIcon = \Modules\Admin\Services\Helper\MenuHelper::getSelectedPageLinkIcon() /*--}}


<div class="portlet light col-lg-12">
    <div class="portlet-title">
        <div class="caption">
            <i class="fa {{$linkIcon}} font-blue-sharp"></i>
            <span class="caption-subject font-blue-sharp bold uppercase">{!! trans('admin::controller/session-resources.select-date') !!}</span>
        </div>

    </div>
    <div class="add-form-main">
        <div class="portlet-body">
            <div class="form-body">
                <span style="color: red; display: none;" id="date-range-error">Invalid Date Range</span>
                <div class="form-group col-md-12">
                    <label class="col-md-2 control-label">{!! trans('admin::controller/session-resources.from-date') !!} <span class="required" aria-required="true">*</span></label>
                    <div class="col-md-3">
                        {!! Form::text('from_date', $date , ['class'=>'form-control availability-date', 'id'=>'from_date' ])!!}
                    </div>
                    <label class="col-md-2 control-label">{!! trans('admin::controller/session-resources.to-date') !!} <span class="required" aria-required="true">*</span></label>
                    <div class="col-md-3">
                        {!! Form::text('to_date', $date , ['class'=>'form-control availability-date', 'id'=>'to_date' ])!!}
                    </div>
                    <div class="portlet-title col-md-2" id="">
                        <div class="actions" style="margin-right: 10px;">
                            <button class="btn blue btn-add-big download-resource-report"><i class="fa fa-download"></i><span class="hidden-480">Download Excel </span></button>
                        </div>
                    </div>
                </div>
            {!! Form::hidden('from_date', $date , ['class'=>'form-control availability-date', 'id'=>'availability_date' ])!!}



            <!--                <div class="form-group col-md-12">
                                    <label class="col-md-3 control-label">{!! trans('admin::controller/session-resources.select-center') !!} <span class="required" aria-required="true">*</span></label>
                                    <div class="col-md-4">
                                        {!! Form::select('center_id', [''=> trans('admin::messages.select-name', [ 'name' => trans('admin::controller/member.center') ])] + $centersList, null,['class'=>'select2me form-control', 'autocomplete' => 'off', 'id' => 'center_select', 'data-rule-required'=>'true', 'data-msg-required'=>'Please select Center.']) !!}
                                    </div>
                                </div>-->
            </div>
        </div>
    </div>

</div>

<div class="portlet light col-lg-12">
    <div class="portlet-title">
        <div class="caption">
            <i class="fa {{$linkIcon}} font-blue-sharp"></i>
            <span class="caption-subject font-blue-sharp bold uppercase">{!! trans('admin::messages.view-name',['name'=>trans('admin::controller/session-resources.session-resources')]) !!}</span>
        </div>
    </div>

    <div class="portlet-body">
        <div class="table-container">
            <div class="table-actions-wrapper">
                <span></span>
                <input type="hidden" name="_token" value="{!! csrf_token() !!}">
                <input id="data-search" type="search" class="form-control" placeholder="Search">
                <input type="hidden" id="minTime" value="{!! $arrTimes['start_time'] !!}"/>
                <input type="hidden" id="maxTime" value="{!! $arrTimes['end_time'] !!}"/>
                <input type="hidden" id="maxTime1" value="{!! $arrTimes['end_time_resource_calendar'] !!}"/>
            </div>

            <ul class="nav nav-tabs">
                <li class="active tab-click" id="1">
                    <a href="#tab_1_1" data-flag="1" data-toggle="tab"> {!!trans('admin::controller/session-resources.staff-resources')!!} </a>
                </li>
                <li id="2" class="tab-click">
                    <a href="#tab_1_2" data-flag="2" data-toggle="tab"> {!!trans('admin::controller/session-resources.machine-resources')!!} </a>
                </li>
                <li id="3" class="tab-click">
                    <a href="#tab_1_3" data-flag="3" data-toggle="tab"> {!!trans('admin::controller/session-resources.room-resources')!!} </a>
                </li>

            </ul>
            <div class="tab-content">
                <div class="tab-pane fade active in" id="tab_1_1">
                    <div id="resource_calender_1" class="resource-calender"></div>
                </div>
                <div class="tab-pane fade" id="tab_1_2">
                    <div id="resource_calender_2" class="resource-calender"></div>
                </div>
                <div class="tab-pane fade" id="tab_1_3">
                    <div id="resource_calender_3" class="resource-calender"></div>
                </div>

            </div>

        </div>
    </div>
</div>



@stop
