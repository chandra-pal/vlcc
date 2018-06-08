@extends('admin::layouts.master')

@section('template-level-scripts')
@parent
{!! HTML::script( URL::asset('js/admin/config-category.js') ) !!}
@stop

@section('scripts')
@parent
<script>
    jQuery(document).ready(function () {
        siteObjJs.admin.configCategoryJs.init();
        siteObjJs.admin.commonJs.boxExpandBtnClick();

        //Uncomment these to override constants
        //in this way using blade syntax as follows - 
        //siteObjJs.admin.commonJs.constants.recordsPerPage = {!! config('settings.C_RECORDS_PER_PAGE'); !!}
    });
</script>
@stop

@section('content')
@include('admin::partials.breadcrumb')
<div id="ajax-response-text"></div>

@if(!empty(Auth::guard('admin')->user()->hasAdd))
@include('admin::config-category.create')
@endif

{{--*/ $linkIcon = \Modules\Admin\Services\Helper\MenuHelper::getSelectedPageLinkIcon() /*--}}

<div id="edit_form">

</div>

<div class="portlet light col-lg-12">
    <div class="portlet-title">
        <div class="caption">
            <i class="fa {{ $linkIcon }} font-blue-sharp"></i>
            <span class="caption-subject font-blue-sharp bold uppercase">{!! trans('admin::messages.view-name',['name' => trans('admin::controller/config-category.config-cats') ]) !!}</span>
        </div>
        @if(!empty(Auth::guard('admin')->user()->hasAdd))
        <div class="actions">
            <a class="btn blue btn-add-big btn-expand-form" href="javascript:;">
                <i class="fa fa-plus"></i> {!! trans('admin::messages.add-name',['name' => trans('admin::controller/config-category.config-cat') ]) !!}
            </a>
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
            <table class="table table-striped table-bordered table-hover" id="grid-table">
                <thead>
                    <tr role="row" class="heading">
                        <th>#</th>
                        <th class="display-hide">{!! trans('admin::controller/config-category.id') !!}</th>
                        <th>{!! trans('admin::controller/config-category.name') !!}</th>
                        <th>{!! trans('admin::controller/config-category.action') !!}</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>
</div>
@stop
