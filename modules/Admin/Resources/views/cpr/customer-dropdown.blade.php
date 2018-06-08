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
</style>
@stop

@section('scripts')
@parent
<script>
    var availableDates = new Array();
    jQuery(document).ready(function () {
        siteObjJs.admin.cprJs.init();
        siteObjJs.admin.commonJs.boxExpandBtnClick();
        siteObjJs.admin.cprJs.maxFileSize = "{!! trans('admin::messages.max-file-size') !!}";
        siteObjJs.admin.cprJs.mimes = "{!! trans('admin::messages.mimes') !!}";
        siteObjJs.admin.cprJs.csvMimes = "{!! trans('admin::messages.csv-mimes') !!}";
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
        <input type="hidden" id="view_cpr_flag" name="view_cpr_flag" class="view_cpr_flag" value="0">
    </div>
</div>
@stop
