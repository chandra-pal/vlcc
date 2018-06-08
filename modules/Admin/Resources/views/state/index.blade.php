@extends('admin::layouts.master')

@section('template-level-scripts')
@parent
{!! HTML::script( URL::asset('js/admin/state.js') ) !!}
@stop

@section('styles')
<style>
    input.upper { text-transform: uppercase; }
</style>
@stop

@section('scripts')
@parent
<script>
    jQuery(document).ready(function () {
        siteObjJs.admin.ipStateJs.init();
        siteObjJs.admin.commonJs.boxExpandBtnClick();
    });
</script>
@stop

@section('content')

@include('admin::partials.breadcrumb')

<div id="ajax-response-text">
</div>

@if(!empty(Auth::guard('admin')->user()->hasAdd))
@include('admin::state.create')
@endif
{{--*/ $linkIcon = \Modules\Admin\Services\Helper\MenuHelper::getSelectedPageLinkIcon() /*--}}
<div id="edit_form">
</div>

<div class="portlet light col-lg-12">
    <div class="portlet-title">
        <div class="caption">
            <i class="fa {{ $linkIcon }} font-blue-sharp"></i>
            <span class="caption-subject font-blue-sharp bold uppercase">View States</span>
        </div>
        <div class="actions">
            @if(!empty(Auth::guard('admin')->user()->hasAdd))
            <a href="javascript:;" class="btn blue btn-add-big btn-expand-form"><i class="fa fa-plus"></i><span class="hidden-480">Add New State </span></a>
            @endif
        </div>
    </div>
    <div class="portlet-body">
        <div class="table-container">
            <table class="table table-striped table-bordered table-hover" id="grid-table">
                <thead>
                    <tr role="row" class="heading">
                        <th width='5%'>#</th>
                        <th>Country Name</th>
                        <th>State Name</th>
                        <th width='10%'>State Code</th>
                        <th width='20%'>Status</th>
                        <th>Status</th>
                        <th width='10%'>Options</th>
                    </tr>
                    @include('admin::state.search')
                </thead>
                <tbody>

                </tbody>
            </table>
        </div>
    </div>
</div>
@stop
