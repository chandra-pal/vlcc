@extends('admin::layouts.master')

@section('page-level-styles')
@parent
{!! HTML::style( asset('global/css/plugins-md.css') ) !!}
@stop

@section('page-level-scripts')
@parent
<script src="http://maps.google.com/maps/api/js?" type="text/javascript"></script>
{!! HTML::script( URL::asset('global/plugins/gmaps/gmaps.min.js') ) !!}
@stop

@section('template-level-scripts')
@parent
{!! HTML::script( URL::asset('js/admin/locations.js') ) !!}
@stop

@section('scripts')
@parent
<script>
    jQuery(document).ready(function () {
        siteObjJs.admin.locationsJs.init();
        siteObjJs.admin.commonJs.boxExpandBtnClick();
        siteObjJs.admin.locationsJs.selectState = "{!! trans('admin::messages.select-name',['name'=>trans('admin::controller/locations.state')]) !!}";
        siteObjJs.admin.locationsJs.selectCity = "{!! trans('admin::messages.select-name',['name'=>trans('admin::controller/locations.city')]) !!}";
    });
</script>
@stop

@section('content')
@include('admin::partials.breadcrumb')
<div id="ajax-response-text"></div>

@if(!empty(Auth::guard('admin')->user()->hasAdd))
@include('admin::locations.create')
@endif


{{--*/ $linkIcon = \Modules\Admin\Services\Helper\MenuHelper::getSelectedPageLinkIcon() /*--}}

<div id="edit_form">

</div>
<div id="gmap_geocoding" class="gmaps" style="position: absolute;margin-top: -513px;margin-left: 271px;"></div>
<div class="portlet light col-lg-12">
    <div class="portlet-title">
        <div class="caption">
            <i class="fa {{ $linkIcon }} font-blue-sharp"></i>
            <span class="caption-subject font-blue-sharp bold uppercase">View Locations</span>
        </div>
        @if(!empty(Auth::guard('admin')->user()->hasAdd))
        <div class="actions">
            <a href="javascript:;" class="btn blue btn-add-big btn-expand-form"><i class="fa fa-plus"></i><span class="hidden-480">Add New Location </span></a>
        </div>
        @endif
    </div>

    <div class="portlet-body">
        <div class="table-container">
            <table class="table table-striped table-bordered table-hover" id="LocationsList">
                <thead>
                    <tr role="row" class="heading">
                        <th width='5%'>#</th>
                        <th width='5%'>ID</th>
                        <th width='20%'>Country</th>
                        <th width='20%'>State</th>
                        <th width='20%'>City Name</th>
                        <th width=''>Location Name</th>
                        <th width='20%'>Status</th>
                        <th width='10%'>Options</th>
                    </tr>
                    @include('admin::locations.search')
                </thead>
                <tbody>

                </tbody>
            </table>
        </div>
    </div>
</div>
@stop
