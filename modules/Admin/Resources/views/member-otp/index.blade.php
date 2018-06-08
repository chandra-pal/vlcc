@extends('admin::layouts.master')

@section('template-level-scripts')
@parent
{!! HTML::script( URL::asset('js/admin/member-otp.js') ) !!}
@stop

@section('scripts')
@parent
<script>
    jQuery(document).ready(function () {
        siteObjJs.admin.memberOtpJs.init();
        siteObjJs.admin.commonJs.boxExpandBtnClick();
    });
</script>
@stop

@section('content')
@include('admin::partials.breadcrumb')
<div id="ajax-response-text"></div>

{{--*/ $linkIcon = \Modules\Admin\Services\Helper\MenuHelper::getSelectedPageLinkIcon() /*--}}
<div id="edit_form">

</div>

<div class="portlet light col-lg-12">
    <div class="portlet-title">
        <div class="caption">
            <i class="fa {{$linkIcon}} font-blue-sharp"></i>
            <span class="caption-subject font-blue-sharp bold uppercase">{!! trans('admin::messages.view-name',['name'=>trans('admin::controller/member-otp.member-otp')]) !!}</span>
        </div>
        @if(!empty(Auth::guard('admin')->user()->hasAdd))
        <div class="actions">
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
            <table class="table table-striped table-bordered table-hover" id="member-otp-table">
                <thead>
                    <tr role="row" class="heading">
                        <th width='1%'>#</th>
                        <th style="display: none;">{!! trans('admin::controller/member-otp.member-otp-id') !!}</th>
                        <th width='54%'>{!! trans('admin::controller/member-otp.mobile') !!}</th>
                        <th width='15%'>{!! trans('admin::controller/member-otp.otp') !!}</th>
                        <th width='15%'>{!! trans('admin::controller/member-otp.sms-deliver') !!}</th>
                        <th width='15%'>{!! trans('admin::controller/member-otp.otp-used') !!}</th>
                        <th width='15%'>{!! trans('admin::controller/member-otp.attemp-count') !!}</th>
                        <th width='15%'>{!! trans('admin::controller/member-otp.created-at') !!}</th>
                    </tr>
                </thead>
                <tbody id="member-otp-table-body">

                </tbody>
            </table>
        </div>
    </div>
</div>
@stop
