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
{!! HTML::script( URL::asset('js/admin/availability.js') ) !!}
@stop

@section('scripts')
@parent
<script>
    var availableDates = new Array();
    jQuery(document).ready(function () {
        siteObjJs.admin.availabilityJs.init();
        siteObjJs.admin.commonJs.boxExpandBtnClick();        
    });   
</script>
@stop

@section('content')
@include('admin::partials.breadcrumb')
<div id="ajax-response-text"></div>

@if(!empty(Auth::guard('admin')->user()->hasAdd))
@include('admin::availability.create')
@endif
{{--*/ $linkIcon = \Modules\Admin\Services\Helper\MenuHelper::getSelectedPageLinkIcon() /*--}}
<div id="edit_form">

</div>

<div class="portlet light col-lg-12">
    <div class="portlet-title">
        <div class="caption">
            <i class="fa {{$linkIcon}} font-blue-sharp"></i>
            <span class="caption-subject font-blue-sharp bold uppercase">{!! trans('admin::messages.view-name',['name'=>trans('admin::controller/availability.availability')]) !!}</span>
        </div>
        @if(!empty(Auth::guard('admin')->user()->hasAdd))
        <div class="actions">
            <a href="javascript:;" class="btn blue btn-add-big btn-expand-form"><i class="fa fa-plus"></i><span class="hidden-480">{!! trans('admin::messages.add-name',['name'=>trans('admin::controller/availability.availability')]) !!} </span></a>
        </div>
        @endif
    </div>
    <div class="portlet-body">
        <div class="table-container">
            <div class="table-actions-wrapper">
                <span></span>
                <input type="hidden" name="_token" value="{!! csrf_token() !!}">
                <input id="data-search" type="search" class="form-control" placeholder="Search">
            </div>
            <table class="table table-striped table-bordered table-hover" id="availability-table">
                <thead>
                    <tr role="row" class="heading">
                        <th width='1%'>#</th>
                        <th width='20%'>{!! trans('admin::controller/availability.availability-date') !!}</th>
                        <th width='15%'>{!! trans('admin::controller/availability.start-time') !!}</th>
                        <th width='15%'>{!! trans('admin::controller/availability.end-time') !!}</th>
                        <th width='15%'>{!! trans('admin::controller/availability.break-time') !!}</th>
                        <th width='15%'>{!! trans('admin::controller/availability.carry-forwarded') !!}</th>
                        <th width='15%'>{!! trans('admin::controller/availability.carry-forwarded-days') !!}</th>
                        <th width='15%'>{!! trans('admin::controller/availability.status') !!}</th>
                        <th width='15%'>{!! trans('admin::controller/availability.action') !!}</th>
                    </tr>
                </thead>
                <tbody>

                </tbody>
            </table>
        </div>
    </div>
</div>
@stop
