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
{!! HTML::script( URL::asset('js/admin/member-diet-log.js') ) !!}
@stop

@section('scripts')
@parent
<script>
    jQuery(document).ready(function () {
        siteObjJs.admin.memberDietLogJs.init();
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
                <label class="col-md-3 control-label">{!! trans('admin::controller/member-diet-log.diet-date') !!} <span class="required" aria-required="true">*</span></label>
                <div class="col-md-4">
                    {!! Form::text('diet_date', $date , ['minlength'=>2,'maxlength'=>50,'class'=>'form-control diet-date', 'id'=>'diet_date', 'data-rule-required'=>'true', 'data-msg-required'=>trans('admin::messages.required-enter', ['name' => trans('admin::controller/member-diet-log.diet-date')]), 'data-rule-maxlength'=>'50', 'data-msg-maxlength'=>trans('admin::messages.error-maxlength', ['name'=>trans('admin::controller/member-diet-log.diet-date')]) , 'data-msg-minlength'=>trans('admin::messages.error-minlength', ['name' => trans('admin::controller/member-diet-log.diet-date')]) ])!!}
                </div>
                <input type="hidden" name="logged_in_user_type"  id ='logged_in_user_type' value="{!! $logged_in_by_user_type !!}">
            </div>

        </div>
    </div>

</div>

<div class="portlet light col-lg-12">
    <div class="portlet-title">
        <div class="caption">
            <i class="fa {{$linkIcon}} font-blue-sharp"></i>
            <span class="caption-subject font-blue-sharp bold uppercase">{!! trans('admin::messages.view-name',['name'=>trans('admin::controller/member-diet-log.member-diet-log')]) !!}</span>
        </div>

        @if(!empty(Auth::guard('admin')->user()->hasAdd))
        <div class="actions" id="button_panle">
            @if($button==0 && $btn_text!=="")

            <a href="javascript:;" class="btn blue btn-add-big recommende-diet-btn"><i class="fa fa-plus"></i><span class="hidden-480 recommendation-text"> {{$btn_text}} </span></a>
            @else
            <a href="javascript:;" class="btn blue btn-add-big recommende-diet-btn" style="display: none;"><i class="fa fa-plus"></i><span class="hidden-480 recommendation-text"> {{$btn_text}} </span></a>

            @endif
        </div>
        @endif
    </div>

    <span style="color: #e02222;" id="warnig_message">{{$warnig_message}}</span>
    @include('admin::member-diet-log.member-diet-recommendation')
    <div class="portlet-body">
        <div class="table-container">
            <div class="table-actions-wrapper">
                <span></span>
                <input type="hidden" name="_token" value="{!! csrf_token() !!}">
                <input id="data-search" type="search" class="form-control" placeholder="Search">
                <input type="hidden" name="acl_flag"  id ="acl_flag" value="{!! $acl_flag !!}">
            </div>
            <table class="table table-striped table-bordered table-hover" id="member-diet-log-table">
                <thead>
                    <tr role="row" class="heading">
                        <th>#</th>
                        <th style="display: none;">ID</th>
                        <th style="display: none;">{!! trans('admin::controller/member-diet-log.schedule-name') !!}</th>
                        <th>{!! trans('admin::controller/member-diet-log.food-name') !!}</th>
                        <th>{!! trans('admin::controller/member-diet-log.calories') !!}</th>
                        <th>{!! trans('admin::controller/member-diet-log.servings-consumed') !!}</th>
                        <th>{!! trans('admin::controller/member-diet-log.measure') !!}</th>
                        <th style="display: none;">Deviation</th>
                    </tr>
                </thead>
                <tbody id="member-diet-log-table-body">

                </tbody>
            </table>
        </div>
    </div>
</div>
@stop
