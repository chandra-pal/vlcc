@extends('admin::layouts.master')

@section('page-level-styles')
@parent
{!! HTML::style( URL::asset('global/plugins/bootstrap-datepicker/css/bootstrap-datepicker3.min.css') ) !!}
{!! HTML::style( URL::asset('global/plugins/bootstrap-timepicker/css/bootstrap-timepicker.min.css') ) !!}
{!! HTML::style( URL::asset('global/plugins/bootstrap-datetimepicker/css/bootstrap-datetimepicker.min.css') ) !!}
@stop


@section('page-level-scripts')
@parent
{!! HTML::script( URL::asset('global/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js') ) !!}
{!! HTML::script( URL::asset('global/plugins/bootstrap-timepicker/js/bootstrap-timepicker.min.js') ) !!}
{!! HTML::script( URL::asset('global/plugins/bootstrap-datetimepicker/js/bootstrap-datetimepicker.min.js') ) !!}
@stop

@section('template-level-scripts')
@parent
{!! HTML::script( URL::asset('js/admin/member-activity-log.js') ) !!}
@stop

@section('scripts')
@parent
<script>
    jQuery(document).ready(function () {
        siteObjJs.admin.memberActivityLogJs.init();
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
            <span class="caption-subject font-blue-sharp bold uppercase">{!! trans('admin::controller/member-diet-log.view-change-diet-plan') !!}</span>
        </div>

    </div>
    <div class="add-form-main">

        <div class="portlet-body">

            <div class="form-group">
                <label class="col-md-3 control-label">{!! trans('admin::controller/member-activity-log.activity-date') !!} <span class="required" aria-required="true">*</span></label>
                <div class="col-md-4">
                    {!! Form::text('activity_date', $date , ['class'=>'form-control activity-date', 'id'=>'activity_date' ])!!}
                </div>
            </div>

        </div>
    </div>

</div>

<div class="portlet light col-lg-12">
    <div class="portlet-title">
        <div class="caption">
            <i class="fa {{$linkIcon}} font-blue-sharp"></i>
            <span class="caption-subject font-blue-sharp bold uppercase">{!! trans('admin::messages.view-name',['name'=>trans('admin::controller/member-activity-log.member-activity-log')]) !!}</span>
        </div>
        @if(!empty(Auth::guard('admin')->user()->hasAdd))
        <div class="actions">

        </div>
        @endif
    </div>

    <div class="portlet-title">
        <div class="caption">
            <span class="caption-subject font-blue-sharp bold uppercase">Deviation : <span id="deviation_span"></span></span>
        </div>

    </div>

    <div class="portlet-body">
        <div class="table-container">
            <div class="table-actions-wrapper">
                <span></span>
                <input type="hidden" name="_token" value="{!! csrf_token() !!}">
                <input id="data-search" type="search" class="form-control" placeholder="Search">
            </div>
            <table class="table table-striped table-bordered table-hover" id="member-activity-log-table">
                <thead>
                    <tr role="row" class="heading">
                        <th width='1%'>#</th>
                        <th width='20%'>{!! trans('admin::controller/member-activity-log.activity') !!}</th>
                        <th width='10%'>{!! trans('admin::controller/member-activity-log.duration') !!}</th>
                        <th width='40%'>{!! trans('admin::controller/member-activity-log.activity-date') !!}</th>
                        <!--th width='10%'>{!! trans('admin::controller/member-activity-log.activity-date') !!}</th-->
                        <th width='10%'>{!! trans('admin::controller/member-activity-log.action') !!}</th>
                    </tr>
                    <tr role="row" class="filter" id="filter">
                        <td></td>
                        <td><input type="text" class="form-control form-filter input-sm" name="activity"></td>
                        <td><input type="text" class="form-control form-filter input-sm" name="duration"></td>
                        <td>
                            <div class="input-group date form_datetime from-date margin-bottom-5" data-date="{{date('Y-m-d h:i:s')}}">
                                {!! Form::text('login_in_time_from', null, ['class'=>'form-control form-filter input-sm','placeholder'=>'From','disabled'=>'disabled']) !!}
                                <span class="input-group-btn">
                                    <button class="btn from-btn default date-reset btn-sm" type="button"><i class="fa fa-times"></i></button>
                                    <button class="btn default date-set btn-sm" type="button"><i class="fa fa-calendar"></i></button>
                                </span>
                            </div>
                            <div class="input-group date form_datetime to-date" data-date="{{date('Y-m-d h:i:s')}}">
                                {!! Form::text('login_in_time_to', null, ['class'=>'form-control form-filter input-sm','placeholder'=>'To','disabled'=>'disabled']) !!}
                                <span class="input-group-btn">
                                    <button class="btn to-btn default date-reset btn-sm" type="button"><i class="fa fa-times"></i></button>
                                    <button class="btn default date-set btn-sm" type="button"><i class="fa fa-calendar"></i></button>
                                </span>
                            </div>
                        </td>
<!--td><input type="text" class="form-control form-filter input-sm" name="activty_date"></td-->
                        <td>
                            <button class="btn btn-sm yellow filter-submit margin-bottom-5" title="Search"><i class="fa fa-search"></i></button>
                            <button class="btn btn-sm red blue filter-cancel margin-bottom-5" title="Reset"><i class="fa fa-times"></i></button>
                        </td>
                    </tr>
                </thead>
                <tbody id="member-activity-log-table-body">

                </tbody>
            </table>
        </div>
    </div>
</div>
@stop
