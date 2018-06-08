@extends('admin::layouts.master')

@section('content')
@include('admin::partials.breadcrumb')
<div id="ajax-response-text"></div>

@if(!empty(Auth::guard('admin')->user()->hasAdd))
@include('admin::city.create')
@endif

{{--*/ $linkIcon = \Modules\Admin\Services\Helper\MenuHelper::getSelectedPageLinkIcon() /*--}}

<div id="edit_form">

</div>
<div class="portlet light col-lg-12">
    <div class="portlet-title">
        <div class="caption">
            <i class="fa {{ $linkIcon }} font-blue-sharp"></i>
            <span class="caption-subject font-blue-sharp bold uppercase">View Cities</span>
        </div>
        @if(!empty(Auth::guard('admin')->user()->hasAdd))
        <div class="actions">
            <a href="javascript:;" class="btn blue btn-add-big btn-expand-form"><i class="fa fa-plus"></i><span class="hidden-480">Add New City </span></a>
        </div>
        @endif
    </div>
    <div class="portlet-body">
        <div class="table-container">
            <table class="table table-striped table-bordered table-hover" id="CityList">
                <thead>
                    <tr role="row" class="heading">
                        <th>#</th>
                        <th width='5%'>ID</th>
                        <th width='20%'>Country</th>
                        <th width='20%'>State</th>
                        <th>City Name</th>
                        <th width='20%'>Status</th>
                        <th width='10%'>Options</th>
                    </tr>
                    @include('admin::city.search')
                </thead>
                <tbody>

                </tbody>
            </table>
        </div>
    </div>
</div>
@stop

@section('template-level-scripts')
@parent
{!! HTML::script( URL::asset('js/admin/city.js') ) !!}
@stop

@section('scripts')
@parent
<script>
    jQuery(document).ready(function () {
        siteObjJs.admin.cityJs.init();
        siteObjJs.admin.commonJs.boxExpandBtnClick();
        siteObjJs.admin.cityJs.selectState = "{!! trans('admin::messages.select-name',['name'=>trans('admin::controller/city.state')]) !!}";
    });
</script>
@stop