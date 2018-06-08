@extends('admin::layouts.master')

@section('template-level-scripts')
@parent
{!! HTML::script( URL::asset('js/admin/diet-plan.js') ) !!}
@stop

@section('scripts')
@parent
<script>
    jQuery(document).ready(function () {
        siteObjJs.admin.dietPlanJs.init();
        siteObjJs.admin.commonJs.boxExpandBtnClick();
    });
</script>
@stop

@section('content')
@include('admin::partials.breadcrumb')
<div id="ajax-response-text"></div>

@if(!empty(Auth::guard('admin')->user()->hasAdd))
@include('admin::diet-plan.create')
@endif
{{--*/ $linkIcon = \Modules\Admin\Services\Helper\MenuHelper::getSelectedPageLinkIcon() /*--}}
<div id="edit_form">

</div>

<div class="portlet light col-lg-12">
    <div class="portlet-title">
        <div class="caption">
            <i class="fa {{$linkIcon}} font-blue-sharp"></i>
            <span class="caption-subject font-blue-sharp bold uppercase">{!! trans('admin::messages.view-name',['name'=>trans('admin::controller/diet-plan.diet-plan')]) !!}</span>
        </div>
        @if(!empty(Auth::guard('admin')->user()->hasAdd))
        <div class="actions">
            <a href="javascript:;" class="btn blue btn-add-big btn-expand-form"><i class="fa fa-plus"></i><span class="hidden-480">{!! trans('admin::messages.add-name',['name'=>trans('admin::controller/diet-plan.diet-plan')]) !!} </span></a>
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
            <table class="table table-striped table-bordered table-hover" id="diet-plan-table">
                <thead>
                    <tr role="row" class="heading">
                        <th width='1%'>#</th>
                        <th width='54%'>{!! trans('admin::controller/diet-plan.id') !!}</th>
                        <th width='54%'>{!! trans('admin::controller/diet-plan.plan-name') !!}</th>
                        <th width='15%'>{!! trans('admin::controller/diet-plan.plan-type') !!}</th>
                        <th width='15%'>{!! trans('admin::controller/diet-plan.calories') !!}</th>
                        <th width='15%'>{!! trans('admin::controller/diet-plan.status') !!}</th>
                        <th width='15%'>{!! trans('admin::controller/diet-plan.action') !!}</th>
                    </tr>
                    @include('admin::diet-plan.search')
                </thead>
                <tbody>

                </tbody>
            </table>
        </div>
    </div>
</div>
@stop
