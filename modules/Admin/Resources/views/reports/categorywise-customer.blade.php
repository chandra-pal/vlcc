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

<div class="portlet light col-lg-12">

    <input type="hidden" id="logged_in_user_type" name="logged_in_user_type"  value="{!! $logged_in_by_user_type !!}">
    <input type="hidden" id="logged_in_user_id" name="logged_in_user_id" value="{!! $logged_in_user_id !!}">

    <table class="table table-striped table-bordered table-hover" id="">
        <thead>

        </thead>
        <tbody>
            <tr>
                <td>Select City</td>
                <td style="width:60%;">
                    {!! Form::select('city_id', [''=> 'Select City'] + $cityList, $city_id, ['class'=>'select2me form-control form-filter', 'autocomplete'=>'off', 'id' => 'city_id', 'data-rule-required'=>'true', 'data-msg-required'=>'Please select City.']) !!}
                </td>
            </tr>
            <tr>
                <td>Select Center</td>
                <td class="select_center" style="width:60%;">
                    {!! Form::select('center_id', [''=> 'Select Center'] + $centerList, $center_id, ['class'=>'select2me form-control form-filter', 'autocomplete'=>'off',  'id' => 'center_id', 'data-rule-required'=>'true', 'data-msg-required'=>'Please select Center.']) !!}
                </td>
            </tr>
        </tbody>
    </table>
    <div class="portlet-title" id='download_btn_cust'>

        <div class="actions" style="margin-right: 10px;" >
            <a href="{{ route('admin.reports.download-categorywise-customer') }}" class="btn blue btn-add-big" ><i class="fa fa-download"></i><span class="hidden-480">Download Excel </span></a>
        </div>
    </div>

    <div class="portlet-body">
        <div class="table-container">
            <div class="table-actions-wrapper">
                <span></span>
                <input type="hidden" name="_token" value="{!! csrf_token() !!}">
                <input id="data-search" type="search" class="form-control" placeholder="Search">
            </div>

            <table class="table table-striped table-bordered table-hover" id="category-customer-table">
                <thead>
                    <tr role="row" class="heading">
                        <th width=''>#</th>
                        <th width=''>City</th>
                        <th width=''>Center</th>
                        <th width=''>Customer Name</th>
                        <th width=''>Mobile Number</th>
                        <th width=''>Category</th>
                        <th width=''>View</th>
                    </tr>
                    @include('admin::reports.search', $customerCategory)
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>
</div>
@stop
