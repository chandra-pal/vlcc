@extends('admin::layouts.master')

@section('template-level-scripts')
@parent
{!! HTML::script( URL::asset('js/admin/member-diet-plan.js') ) !!}
@stop

@section('scripts')
@parent
<script>
    jQuery(document).ready(function () {
        siteObjJs.admin.memberDietPlanJs.init();
        siteObjJs.admin.commonJs.boxExpandBtnClick();
//        var errorMessage = "something";
//
//        jQuery.validator.addMethod("checkValue", function (value, element, errorMessage) {
//            //var response = ((value > 0) && (value <= 100)) || ((value == 'test1') || (value == 'test2'));
//            return errorMessage;
//            console.log("Custom Add Method!");
//        });
    });
</script>
@stop

@section('content')
@include('admin::partials.breadcrumb')
<div id="ajax-response-text"></div>

{{--*/ $linkIcon = \Modules\Admin\Services\Helper\MenuHelper::getSelectedPageLinkIcon() /*--}}
<div id="edit_form" style="align: center; margin-bottom: 10px;">

</div>

<div class="portlet light col-lg-12">
    <div class="portlet-title">
        <div class="caption">
            <i class="fa {{$linkIcon}} font-blue-sharp"></i>
            <span class="caption-subject font-blue-sharp bold uppercase">{!! trans('admin::controller/member-diet-plan.view-change-diet-plan') !!}</span>
        </div>

    </div>
    <div class="add-form-main">

        <div class="portlet-body form ">
            {!! Form::open(['route' => ['admin.member-diet-plan.store'], 'method' => 'post', 'class' => 'form-horizontal config-category-form',  'id' => 'create-member-diet-plan', 'msg' => trans('admin::messages.added',['name'=>trans('admin::controller/member-diet-plan.member-diet-plan')]) ]) !!}
            <div class="form-group" id="plan-drop-down">
                @include('admin::member-diet-plan.plandropdown')
            </div>

            <input type="hidden" name="diet_plan_calories" value="" class="diet_plan_calories" id="diet_plan_calories">

            <div class="portlet light col-lg-12">
                <div class="portlet-title">
                    <div class="caption">
                        <i class="fa {{$linkIcon}} font-blue-sharp"></i>
                        <span class="caption-subject font-blue-sharp bold uppercase">{!! trans('admin::messages.view-name',['name'=>trans('admin::controller/member-diet-plan.diet-plan-details')]) !!}</span>
                    </div>
                    @if(!empty(Auth::guard('admin')->user()->hasAdd))
                    <div class="actions">

                    </div>
                    @endif
                </div>
                <div class="portlet-body">
                    <div class="table-container">
                        <div class="">
                            <span></span>
                            <input type="hidden" name="_token" value="{!! csrf_token() !!}">
                            <input type="hidden" name="assets_url" value="{!! URL::asset('images') !!}" class="assets_url">
                            <input type="hidden" name="acl_flag"  id ="acl_flag" value="{!! $acl_flag !!}">
                        </div>
                        <table class="table table-striped table-bordered table-hover" id="member-diet-plan-table">
                            <thead>
                                <tr role="row" class="heading">
                                    <th width='1%'>#</th>
                                    <th style="display:none;"></th>
                                    <th width='25%'>{!! trans('admin::controller/member-diet-plan.food-type') !!}</th>
                                    <th width='35%'>{!! trans('admin::controller/member-diet-plan.food-name') !!}</th>
                                    <th width='5%'>{!! trans('admin::controller/member-diet-plan.recommended-servings') !!}</th>
                                    <th width='15%'>{!! trans('admin::controller/member-diet-plan.view-measure') !!}</th>
                                    <th width='15%'>{!! trans('admin::controller/member-diet-plan.view-calories') !!}</th>
                                    <th width='15%'>{!! trans('admin::controller/member-diet-plan.total-calories') !!}</th>
                                    <th width='25%'>Action</th>

<!--                                    <th width='15%'>{!! trans('admin::controller/member-diet-plan.view-serving-size') !!}</th>-->
<!--                                    <th width='15%'>{!! trans('admin::controller/member-diet-plan.view-serving-unit') !!}</th>-->

                                </tr>
            <!--                    <tr role="row" class="filter">
                                    <td></td>
                                    <td></td>
                                    <td>
                                        <select name="schedule_dropdown" class="form-control form-filter input-sm width-auto select2me">
                                            <option value="">Select Schedule Name</option>
                                            @foreach ($scheduleTypeList as $scheduleName)
                                            <option value="{{ $scheduleName }}">{{ $scheduleName }}</option>
                                            @endforeach
                                            <option value="">All</option>
                                        </select>
                                    </td>
                                    <td>{!! Form::text('food_name', null, ['class'=>'form-control form-filter']) !!}</td>
                                    <td>{!! Form::text('servings_recommended', null, ['class'=>'form-control form-filter']) !!}</td>
                                    <td>
                                        {!! Form::button('<i class="fa fa-search"></i>', ['title' => trans('admin::messages.search'), 'class' => 'btn btn-sm yellow filter-submit margin-bottom-5']) !!}
                                        {!! Form::button('<i class="fa fa-times"></i>', ['title' => trans('admin::messages.reset'), 'class' => 'btn btn-sm red blue filter-cancel margin-bottom-5']) !!}
                                    </td>
                                </tr>-->
                            </thead>
                            <tbody id="member-diet-plan-table-body">

                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            {{-- @if($userTypeId==4 || $userTypeId==8) --}}
            @if(!empty(Auth::guard('admin')->user()->hasAdd))
            <div class="form-actions" id="action-button-box">
                <div class="col-md-6">
                    <div class="col-md-offset-6 col-md-9">
                        <button type="submit" class="btn green save-member-diet-plan">{!! trans('admin::messages.save') !!}</button>
                        <button type="button" class="btn default btn-collapse btn-collapse-form">{!! trans('admin::messages.cancel') !!}</button>
                    </div>
                </div>
            </div>
            @endif
            {{-- @endif --}}
            {!! Form::close() !!}
        </div>
    </div>

</div>
@stop
