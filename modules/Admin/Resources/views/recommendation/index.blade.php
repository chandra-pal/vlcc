@extends('admin::layouts.master')

@section('template-level-scripts')
@parent
{!! HTML::script( URL::asset('js/admin/recommendation.js') ) !!}
@stop

@section('scripts')
@parent
<script>
    jQuery(document).ready(function () {
        siteObjJs.admin.recommendationJs.init();
        siteObjJs.admin.commonJs.boxExpandBtnClick();
    });
</script>
@stop

@section('content')
@include('admin::partials.breadcrumb')

<div id="ajax-response-text"></div>


<div class="clearfix" style="margin-bottom: 10px;"></div>

@if(!empty(Auth::guard('admin')->user()->hasAdd))
@include('admin::recommendation.create')
@endif
{{--*/ $linkIcon = \Modules\Admin\Services\Helper\MenuHelper::getSelectedPageLinkIcon() /*--}}
<div id="edit_form">

</div>

<div class="portlet light col-lg-12">
    <div class="portlet-title">
        <div class="caption">
            <i class="fa {{$linkIcon}} font-blue-sharp"></i>
            <span class="caption-subject font-blue-sharp bold uppercase">{!! trans('admin::messages.view-name',['name'=>trans('admin::controller/recommendation.recommendations')]) !!}</span>
        </div>
        @if(!empty(Auth::guard('admin')->user()->hasAdd))
        <div class="actions">
            <a href="javascript:;" class="btn blue btn-add-big btn-expand-form"><i class="fa fa-plus"></i><span class="hidden-480">{!! trans('admin::messages.add-name',['name'=>trans('admin::controller/recommendation.recommendation')]) !!} </span></a>
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
            <table class="table table-striped table-bordered table-hover" id="recommendation-table">
                <thead>
                    <tr role="row" class="heading">
                        <th width='1%'>#</th>
                        <th style="display: none;">{!! trans('admin::controller/recommendation.recommendation-id') !!}</th>
                        <th width='30%'>{!! trans('admin::controller/recommendation.recommendation-type') !!}</th>
                        <th width='54%'>{!! trans('admin::controller/recommendation.recommendation-text') !!}</th>
                        <th>{!! trans('admin::controller/recommendation.status') !!}</th>
                        <th width='15%'>Action</th>
                    </tr>
                    <tr role="row" class="filter">
                        <th></th>
                        <th style="display: none;">{!! trans('admin::controller/recommendation.recommendation-id') !!}</th>
                        <th><select name="message_type_dropdown" id='message_type_dropdown' class="form-control form-filter input-sm width-auto select2me">
                                <option value="">Select Message Type</option>
                                <option value="1">General Notification</option>
                                <option value="2">Activity Notification</option>
                                <option value="3">Diet Notification</option>
                                <option value="4">Session Notification</option>
                            </select></th>
                        <th>{!! Form::text('message_text', null, ['class'=>'form-control form-filter']) !!}</th>
                        <th></th>
                        <th>{!! Form::button('<i class="fa fa-search"></i>', ['title' => trans('admin::messages.search'), 'class' => 'btn btn-sm yellow filter-submit margin-bottom-5']) !!}
                            {!! Form::button('<i class="fa fa-times"></i>', ['title' => trans('admin::messages.reset'), 'class' => 'btn btn-sm red blue filter-cancel margin-bottom-5']) !!}</th>
                    </tr>
                </thead>
                <tbody id="recommendation-table-body">

                </tbody>
            </table>
        </div>
    </div>
</div>
@stop
