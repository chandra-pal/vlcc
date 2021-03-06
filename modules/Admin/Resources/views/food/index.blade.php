@extends('admin::layouts.master')

@section('template-level-scripts')
@parent
{!! HTML::script( URL::asset('js/admin/food.js') ) !!}
@stop

@section('scripts')
@parent
<script>
    jQuery(document).ready(function () {
        siteObjJs.admin.foodJs.init();
        siteObjJs.admin.commonJs.boxExpandBtnClick();
    });
</script>
@stop

@section('content')
@include('admin::partials.breadcrumb')
<div id="ajax-response-text"></div>

@if(!empty(Auth::guard('admin')->user()->hasAdd))
@include('admin::food.create')
@endif
{{--*/ $linkIcon = \Modules\Admin\Services\Helper\MenuHelper::getSelectedPageLinkIcon() /*--}}
<div id="edit_form">

</div>

<div class="portlet light col-lg-12">
    <div class="portlet-title">
        <div class="caption">
            <i class="fa {{$linkIcon}} font-blue-sharp"></i>
            <span class="caption-subject font-blue-sharp bold uppercase">{!! trans('admin::messages.view-name',['name'=>trans('admin::controller/food.food')]) !!}</span>
        </div>
        @if(!empty(Auth::guard('admin')->user()->hasAdd))
        <div class="actions">
            <a href="javascript:;" class="btn blue btn-add-big btn-expand-form"><i class="fa fa-plus"></i><span class="hidden-480">{!! trans('admin::messages.add-name',['name'=>trans('admin::controller/food.food')]) !!} </span></a>
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
            <table class="table table-striped table-bordered table-hover" id="food-table">
                <thead>
                    <tr role="row" class="heading">
                        <th width='1%'>#</th>
                        <th>{!! trans('admin::controller/food.food-id') !!}</th>
                        <th width='54%'>{!! trans('admin::controller/food.food-type') !!}</th>
                        <th width='54%'>{!! trans('admin::controller/food.food-name') !!}</th>
                        <th width='15%'>{!! trans('admin::controller/food.measure') !!}</th>
                        <th width='15%'>{!! trans('admin::controller/food.calories') !!}</th>
<!--                        <th width='15%'>{!! trans('admin::controller/food.serving-size') !!}</th>
                        <th width='15%'>{!! trans('admin::controller/food.serving-unit') !!}</th>-->
                        <th width='15%'>{!! trans('admin::controller/food.action') !!}</th>
                    </tr>
                </thead>
                <tbody>

                </tbody>
            </table>
        </div>
    </div>
</div>
@stop
