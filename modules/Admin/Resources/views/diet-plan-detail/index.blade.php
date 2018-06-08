@extends('admin::layouts.master')

@section('page-level-styles')
@parent
{!! HTML::style( URL::asset('global/plugins/bootstrap-select/css/bootstrap-select.min.css') ) !!}
@stop

@section('page-level-scripts')
@parent
{!! HTML::script( URL::asset('global/plugins/bootstrap-select/js/bootstrap-select.min.js') ) !!}
@stop

@section('template-level-scripts')
@parent
{!! HTML::script( URL::asset('js/admin/diet-plan-detail.js') ) !!}
@stop

@section('scripts')
@parent
<script>
    jQuery(document).ready(function () {
        siteObjJs.admin.dietPlanDetailJs.init();
        siteObjJs.admin.commonJs.boxExpandBtnClick();
    });
</script>
@stop

@section('content')
@include('admin::partials.breadcrumb')
<div id="ajax-response-text"></div>

@if(!empty(Auth::guard('admin')->user()->hasAdd))
@include('admin::diet-plan-detail.create')
@endif
{{--*/ $linkIcon = \Modules\Admin\Services\Helper\MenuHelper::getSelectedPageLinkIcon() /*--}}
<div id="edit_form">

</div>

<div class="portlet light col-lg-12">
    <div class="portlet-title">
        <div class="caption">
            <i class="fa {{$linkIcon}} font-blue-sharp"></i>
            <span class="caption-subject font-blue-sharp bold uppercase">{!! trans('admin::messages.view-name',['name'=>trans('admin::controller/diet-plan-detail.diet-plan-detail')]) !!}</span>
        </div>
        @if(!empty(Auth::guard('admin')->user()->hasAdd))
        <div class="actions">
            <a href="javascript:;" class="btn blue btn-add-big btn-expand-form"><i class="fa fa-plus"></i><span class="hidden-480">{!! trans('admin::messages.add-name',['name'=>trans('admin::controller/diet-plan-detail.diet-plan-detail')]) !!} </span></a>
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
            <table class="table table-striped table-bordered table-hover" id="diet-plan-detail-table">
                <thead>
                    <tr role="row" class="heading">
                        <th>#</th>
                        <th>{!! trans('admin::controller/diet-plan-detail.diet-plan-detail-id') !!}</th>
                        <th>{!! trans('admin::controller/diet-plan-detail.diet-plan-type') !!}</th>
                        <th>{!! trans('admin::controller/diet-plan-detail.diet-schedule-type') !!}</th>
                        <th>{!! trans('admin::controller/diet-plan-detail.food-type') !!}</th>
                        <th>{!! trans('admin::controller/diet-plan-detail.serving-recommended') !!}</th>
                        <!--<th width='15%'>{!! trans('admin::controller/diet-plan-detail.status') !!}</th>-->
                        <th width='15%'>{!! trans('admin::controller/diet-plan-detail.action') !!}</th>
                    </tr>
                    <tr role="row" class="filter">
                        <td></td>
                        <td></td>
                        <td> <select name="diet_plan_name" class="form-control form-filter input-sm width-auto select2me">
                                <option value="">Select Diet Plan</option>
                                @foreach ($dietPlanTypeListDropdown as $dietPlan)
                                <option value="{{ $dietPlan }}">{{ $dietPlan }}</option>
                                @endforeach
                            </select></td>
                        <td>
                            <select name="schedule_type" class="form-control form-filter input-sm width-auto select2me">
                                <option value="">Select Diet Schedule type</option>
                                @foreach ($scheduleTypeList as $schedule)
                                <option value="{{ $schedule }}">{{ $schedule }}</option>
                                @endforeach
                            </select>

                        </td>
                        <td>{!! Form::text('food', null, ['class'=>'form-control form-filter']) !!}</td>
                        <td>{!! Form::text('servings_recommended', null, ['class'=>'form-control form-filter']) !!}</td>
<!--                        <td>
                            <select name="status" class="form-control form-filter input-sm width-auto">
                                <option value="">Select</option>
                                <option value="1"> {!! trans('admin::messages.active') !!}</option>
                                <option value="0"> {!! trans('admin::messages.inactive') !!}</option>
                            </select>
                        </td>-->
                        <td>
                            {!! Form::button('<i class="fa fa-search"></i>', ['title' => trans('admin::messages.search'), 'class' => 'btn btn-sm yellow filter-submit margin-bottom-5']) !!}
                            {!! Form::button('<i class="fa fa-times"></i>', ['title' => trans('admin::messages.reset'), 'class' => 'btn btn-sm red blue filter-cancel margin-bottom-5']) !!}
                        </td>
                    </tr>
                </thead>
                <tbody>

                </tbody>
            </table>
        </div>
    </div>
</div>
@stop
