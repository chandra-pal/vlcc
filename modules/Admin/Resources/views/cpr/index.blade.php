@extends('admin::layouts.master')

@section('page-level-styles')
@parent
{!! HTML::style( URL::asset('global/plugins/bootstrap-datepicker/css/bootstrap-datepicker3.min.css') ) !!}
{!! HTML::style( URL::asset('global/plugins/bootstrap-timepicker/css/bootstrap-timepicker.min.css') ) !!}
{!! HTML::style( URL::asset('global/plugins/bootstrap-datetimepicker/css/bootstrap-datetimepicker.min.css') ) !!}

@stop

@section('template-level-scripts')
@parent
{!! HTML::script( URL::asset('js/smartWizard/jquery.smartWizard.min.js') ) !!}
{!! HTML::script( URL::asset('global/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js') ) !!}
{!! HTML::script( URL::asset('global/plugins/bootstrap-timepicker/js/bootstrap-timepicker.min.js') ) !!}
{!! HTML::script( URL::asset('global/plugins/bootstrap-datetimepicker/js/bootstrap-datetimepicker.min.js') ) !!}

{!! HTML::script( URL::asset('js/admin/cpr.js') ) !!}
@stop

@section('styles')
@parent
<style>
    .navbar.btn-toolbar.sw-toolbar.sw-toolbar-bottom {
        clear:both;
    }

    table#bca-record-table tr>td:nth-child(3) {
        white-space: nowrap !important;
    }

    table#session-record-table tr>td:nth-child(3) {
        white-space: nowrap !important;
    }

    table#measurements-table tr>td:nth-child(2) {
        white-space: nowrap !important;
    }

</style>
@stop

@section('scripts')
@parent
<script>
    var availableDates = new Array();

    jQuery(document).ready(function () {
        siteObjJs.admin.cprJs.sessionId = '<?php echo $session_id ?>';
        siteObjJs.admin.cprJs.maxFileSize = "{!! trans('admin::messages.max-file-size') !!}";
        siteObjJs.admin.cprJs.mimes = "{!! trans('admin::messages.mimes') !!}";
        siteObjJs.admin.cprJs.csvMimes = "{!! trans('admin::messages.csv-mimes') !!}";
        siteObjJs.admin.cprJs.measurementRecordsTitle = JSON.parse('<?php echo $measurement_record_fields_str ?>');
        siteObjJs.admin.cprJs.init();
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
            <span class="caption-subject font-blue-sharp bold uppercase">{!! strtoupper(trans('admin::controller/cpr.client_programme_record')) !!}</span>
        </div>
    </div>
    <div class="portlet-body cpr-ajax-response">
        <div class="table-container">
            <div class="">
                <span></span>
                <input type="hidden" name="_token" value="{!! csrf_token() !!}">
                <input id="data-search" type="search" class="form-control" placeholder="Search">
                <input type="hidden" name="session_id"  id ='session_id' value="{!! $session_id !!}">
                <input type="hidden" name="member_id"  id ='member_id' value="{!! $member_id !!}">
                <input type="hidden" name="session_center_id"  id ='session_center_id' value="{!! $session_center_id !!}">
                <input type="hidden" name="package_id"  id ='package_id' value="{!! $package_id !!}">
                <input type="hidden" name="check_bca_data_flag"  id ='check_bca_data_flag' value="99">
                <input type="hidden" name="logged_in_user_id"  id ='logged_in_user_id' value="{!! $logged_in_user_id !!}">
                <input type="hidden" name="logged_in_user_type"  id ='logged_in_user_type' value="{!! $logged_in_by_user_type !!}">
                <input type="hidden" name="bca_alert_common"  id ='bca_alert_common' value="">
                <input type="hidden" name="acl_flag"  id ="acl_flag" value="{!! $acl_flag !!}">
            </div>

            <div id="smartwizard">
                <ul>
                    <li><a href="#step-1">{!! trans('admin::controller/cpr.personal-info') !!}</a></li>
                    <li><a href="#step-2">{!! trans('admin::controller/cpr.bca-data') !!}</a></li>
                    <li><a href="#step-3">{!! trans('admin::controller/cpr.measurements') !!}</a></li>
                    <li><a href="#step-4">{!! trans('admin::controller/cpr.session-records') !!}</a></li>
                    <li><a href="#step-5">{!! trans('admin::controller/cpr.dietary-assessment') !!}</a></li>
                    <li><a href="#step-6">{!! trans('admin::controller/cpr.fitness-assessment') !!}</a></li>
                    <li><a href="#step-7">{!! trans('admin::controller/cpr.medical-assessment') !!}</a></li>
                    <li><a href="#step-8">{!! trans('admin::controller/cpr.medical-review') !!}</a></li>
                    <li><a href="#step-9">{!! trans('admin::controller/cpr.skin-hair-analysis') !!}</a></li>
                    <li><a href="#step-10">{!! trans('admin::controller/cpr.review') !!}</a></li>
                    <li><a href="#step-11">{!! trans('admin::controller/cpr.measurements-records') !!}</a></li>
                </ul>
                <div>
                    <div id="step-1">
                        @include('admin::cpr.personal-info')
                    </div>
                    <div id="step-2">
                        @include('admin::cpr.bca-records')
                    </div>
                    <div id="step-3">
                        @include('admin::cpr.measurements')
                    </div>
                    <div id="step-4">
                        @include('admin::cpr.session-records')
                    </div>
                    <div id="step-5">
                        @include('admin::cpr.dietary-assessment')
                    </div>
                    <div id="step-6">
                        @include('admin::cpr.fitness-assessment')
                    </div>
                    <div id="step-7">
                        @include('admin::cpr.medical-assessment')
                    </div>
                    <div id="step-8">
                        @include('admin::cpr.medical-review')
                    </div>
                    <div id="step-9">
                        @include('admin::cpr.skin-hair-analysis')
                    </div>
                    <div id="step-10">
                        @include('admin::cpr.review-fitness-activity')
                    </div>
                    <div id="step-11">
                        @include('admin::cpr.measurement-record')
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
@stop
