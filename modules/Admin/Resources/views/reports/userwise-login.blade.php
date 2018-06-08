@extends('admin::layouts.master')

@section('template-level-scripts')
@parent
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

<div class="portlet light col-lg-12" id="logged_users">
    <table class="table table-striped table-bordered table-hover" id="users-logged">
        <thead>

        </thead>
        <tbody>
            <tr>
                <td>Select City</td>
                <td style="width:60%;">
                    {!! Form::select('city_id', ['-1'=> 'Select City'] + $cityList, null,['class'=>'select2me form-control form-filter', 'autocomplete'=>'off', 'id' => 'city_id', 'data-rule-required'=>'true', 'data-msg-required'=>'Please select City.']) !!}
                </td>
            </tr>
            <tr>
                <td>Select Center</td>
                <td class="select_center" style="width:60%;">
                    {!! Form::select('center_id', [''=> 'Select Center'] + $centerList, null,['class'=>'select2me form-control form-filter', 'autocomplete'=>'off', 'id' => 'center_id', 'data-rule-required'=>'true', 'data-msg-required'=>'Please select Center.']) !!}
                </td>
            </tr>
        </tbody>
    </table>
    <div class="portlet-title" id='download_btn_user_logged'>
        <div class="actions" style="margin-right: 10px;" >
            <a href="{{ route('admin.reports.download-centerwise-logged-users') }}" class="btn blue btn-add-big" ><i class="fa fa-download"></i><span class="hidden-480">Download Excel </span></a>
        </div>
    </div>

    <div class="portlet-body">
        <div class="table-container">
            <div class="table-actions-wrapper">
                <span></span>
                <input type="hidden" name="_token" value="{!! csrf_token() !!}">
                <input id="data-search" type="search" class="form-control" placeholder="Search">
            </div>
            <table class="table table-striped table-bordered table-hover" id="centerwise-users-logged-table">
                <thead>
                    <tr role="row" class="heading">
                        <th width=''>#</th>
                        <th width=''>City</th>
                        <th width=''>Center</th>
                        <th width=''>Username</th>
                        <th width=''>Name</th>
                        <th width=''>Mobile No.</th>
                        <th width=''>Designation</th>
<!--                        <th width=''>Employee ID</th>-->
                        <th width=''>Number of Logins</th>
                        <th width=''>Last Login</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>
</div>
@stop
