@extends('admin::layouts.master')

@section('page-level-styles')
@parent
{!! HTML::style( URL::asset('global/plugins/bootstrap-datepicker/css/bootstrap-datepicker3.min.css') ) !!}
@stop

@section('template-level-scripts')
@parent
{!! HTML::script( URL::asset('js/smartWizard/jquery.smartWizard.min.js') ) !!}
{!! HTML::script( URL::asset('global/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js') ) !!}
{!! HTML::script( URL::asset('js/admin/reports.js') ) !!}
@stop

@section('scripts')
@parent
<script>
    jQuery(document).ready(function () {
        siteObjJs.admin.reportsJs.init();
        siteObjJs.admin.commonJs.boxExpandBtnClick();
    });
</script>
@stop

@section('content')
@include('admin::partials.breadcrumb')

{{--*/ $linkIcon = \Modules\Admin\Services\Helper\MenuHelper::getSelectedPageLinkIcon() /*--}}

<div class="portlet light col-lg-12">

    <table class="table table-striped table-bordered table-hover" id="">
        <thead>

        </thead>
        <tbody>
            <tr>
                <td>Select Date</td>
                <td style="width:60%;">
                    {!! Form::text('new_user_date', null, ['class'=>'form-control skip date', 'readonly' => 'true', 'id'=>'new_user_date', 'data-rule-required'=>'true', 'data-msg-required'=>'Please Select Date' ])!!}
                </td>
            </tr>

        </tbody>
    </table>
    <div class="portlet-title" id='download_new_users' style="display: none;">

        <div class="actions" style="margin-right: 10px;" >
            <a href="{{ route('admin.reports.download-new-users') }}" class="btn blue btn-add-big" ><i class="fa fa-download"></i><span class="hidden-480">Download Excel </span></a>
        </div>
    </div>

    <div class="portlet-body">
        <div class="table-container">
            <div class="table-actions-wrapper">
                <span></span>
                <input type="hidden" name="_token" value="{!! csrf_token() !!}">
                <input id="data-search" type="search" class="form-control" placeholder="Search">
            </div>

            <table class="table table-striped table-bordered table-hover" id="new-user-table">
                <thead>
                    <tr role="row" class="heading">
                        <th width=''>#</th>
                        <th width=''>Customer Name</th>
                        <th width=''>Mobile Number</th>
                        <th width=''>Created At</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>
</div>
@stop
