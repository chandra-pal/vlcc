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
{!! HTML::script( URL::asset('js/admin/member-activity-recommendation.js') ) !!}
@stop

@section('scripts')
@parent
<script>
    jQuery(document).ready(function () {
        siteObjJs.admin.memberActivityRecommendationJs.init();
        siteObjJs.admin.commonJs.boxExpandBtnClick();
    });
</script>
@stop

@section('content')
@include('admin::partials.breadcrumb')

<div id="ajax-response-text"></div>


<div class="clearfix" style="margin-bottom: 10px;"></div>

@if(!empty(Auth::guard('admin')->user()->hasAdd))
@include('admin::member-activity-recommendation.create')
@endif
{{--*/ $linkIcon = \Modules\Admin\Services\Helper\MenuHelper::getSelectedPageLinkIcon() /*--}}
<div id="edit_form">

</div>

<div class="portlet light col-lg-12">
    <div class="portlet-title">
        <div class="caption">
            <i class="fa {{$linkIcon}} font-blue-sharp"></i>
            <span class="caption-subject font-blue-sharp bold uppercase">{!! trans('admin::messages.view-name',['name'=>trans('admin::controller/member-activity-recommendation.member-activity-recommendation')]) !!}</span>
        </div>
        @if(!empty(Auth::guard('admin')->user()->hasAdd))
        <div class="actions">
            <a href="javascript:;" class="btn blue btn-add-big btn-expand-form"><i class="fa fa-plus"></i><span class="hidden-480">{!! trans('admin::messages.add-name',['name'=>trans('admin::controller/member-activity-recommendation.member-activity-recommendation')]) !!} </span></a>
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
            <table class="table table-striped table-bordered table-hover" id="member-activity-recommendation-table">
                <thead>
                    <tr role="row" class="heading">
                        <th>#</th>
                        <th style="display: none;">{!! trans('admin::controller/member-activity-recommendation.recommendation-id') !!}</th>
                        <th>{!! trans('admin::controller/member-activity-recommendation.activity-type') !!}</th>
                        <th>{!! trans('admin::controller/member-activity-recommendation.recommendation-date-time') !!}</th>
                        <th>{!! trans('admin::controller/member-activity-recommendation.duration') !!}</th>
                        <th>{!! trans('admin::controller/member-activity-recommendation.calories-recommended') !!}</th>
                        <th>Action</th>
                    </tr>
                    <tr role="row" class="filter">
                        <th></th>
                        <th style="display: none;">{!! trans('admin::controller/recommendation.recommendation-id') !!}</th>
                        <th>
                            <select name="activity_type" class="form-control form-filter input-sm width-auto select2me">
                                <option value="">Select Activity Type</option>
                                @foreach ($activityList as $activity)
                                <option value="{{ $activity }}">{{ $activity }}</option>
                                @endforeach
                            </select>
                        </th>
                        <th>

                            <div class="input-group date form_datetime from-date margin-bottom-5" data-date="{{date('Y-m-d h:i:s')}}">
                                {!! Form::text('activity_time_from', null, ['class'=>'form-control form-filter input-sm','placeholder'=>'From','disabled'=>'disabled']) !!}
                                <span class="input-group-btn">
                                    <button class="btn from-btn default date-reset btn-sm" type="button"><i class="fa fa-times"></i></button>
                                    <button class="btn default date-set btn-sm" type="button"><i class="fa fa-calendar"></i></button>
                                </span>
                            </div>
                            <div class="input-group date form_datetime to-date" data-date="{{date('Y-m-d h:i:s')}}">
                                {!! Form::text('activity_time_to', null, ['class'=>'form-control form-filter input-sm','placeholder'=>'To','disabled'=>'disabled']) !!}
                                <span class="input-group-btn">
                                    <button class="btn to-btn default date-reset btn-sm" type="button"><i class="fa fa-times"></i></button>
                                    <button class="btn default date-set btn-sm" type="button"><i class="fa fa-calendar"></i></button>
                                </span>
                            </div>
                        </th>
                        <th>
                            {!! Form::text('activity_duration', null, ['class'=>'form-control form-filter input-sm','placeholder'=>'Activity Duration']) !!}
                        </th>
                        <th>
                            {!! Form::text('calories_recommended', null, ['class'=>'form-control form-filter input-sm','placeholder'=>'Calories Recommendation']) !!}
                        </th>
                        <th>
                            {!! Form::button('<i class="fa fa-search"></i>', ['title' => trans('admin::messages.search'), 'class' => 'btn btn-sm yellow filter-submit margin-bottom-5']) !!}
                            {!! Form::button('<i class="fa fa-times"></i>', ['title' => trans('admin::messages.reset'), 'class' => 'btn btn-sm red blue filter-cancel margin-bottom-5']) !!}
                        </th>
                    </tr>
                </thead>
                <tbody id="member-activity-recommendation-table-body">

                </tbody>
            </table>
        </div>
    </div>
</div>
@stop
