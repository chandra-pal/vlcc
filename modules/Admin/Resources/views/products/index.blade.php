@extends('admin::layouts.master')
@section('page-level-styles')
@parent
{!! HTML::style( URL::asset('global/plugins/bootstrap-fileinput/bootstrap-fileinput.css') ) !!}
@stop

@section('page-level-scripts')
@parent
{!! HTML::script( URL::asset('global/plugins/bootstrap-fileinput/bootstrap-fileinput.js') ) !!}
@stop

@section('template-level-scripts')
@parent
{!! HTML::script( URL::asset('js/admin/products.js') ) !!}
@stop

@section('scripts')
@parent
<script>
    jQuery(document).ready(function () {
        siteObjJs.admin.productsJs.init();
        siteObjJs.admin.commonJs.boxExpandBtnClick();
        siteObjJs.admin.productsJs.confirmRemoveProductImage = "{!! trans('admin::messages.confirm-remove-product-image') !!}";
        siteObjJs.admin.productsJs.maxFileSize = "{!! trans('admin::messages.max-file-size') !!}";
        siteObjJs.admin.productsJs.mimes = "{!! trans('admin::messages.mimes') !!}";
    });
</script>
@stop

@section('content')
@include('admin::partials.breadcrumb')
<div id="ajax-response-text"></div>

@if(!empty(Auth::guard('admin')->user()->hasAdd))
@include('admin::products.create')
@endif
{{--*/ $linkIcon = \Modules\Admin\Services\Helper\MenuHelper::getSelectedPageLinkIcon() /*--}}
<div id="edit_form">

</div>

<div class="portlet light col-lg-12">
    <div class="portlet-title">
        <div class="caption">
            <i class="fa {{$linkIcon}} font-blue-sharp"></i>
            <span class="caption-subject font-blue-sharp bold uppercase">{!! trans('admin::messages.view-name',['name'=>trans('admin::controller/products.products')]) !!}</span>
        </div>
        @if(!empty(Auth::guard('admin')->user()->hasAdd))
        <div class="actions">
            <a href="javascript:;" class="btn blue btn-add-big btn-expand-form"><i class="fa fa-plus"></i><span class="hidden-480">{!! trans('admin::messages.add-name',['name'=>trans('admin::controller/products.products')]) !!} </span></a>
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
            <table class="table table-striped table-bordered table-hover" id="products-table">
                <thead>
                    <tr role="row" class="heading">
                        <th width='1%'>#</th>
                        <th>{!! trans('admin::controller/products.products-id') !!}</th>
                        <th width='54%'>{!! trans('admin::controller/products.products-title') !!}</th>
                        <th width='54%'>{!! trans('admin::controller/products.products-description') !!}</th>
                        <th width='15%'>{!! trans('admin::controller/products.product-image') !!}</th>
                        <th width='15%'>{!! trans('admin::controller/products.product-detail-page-url') !!}</th>
                        <th width='15%'>{!! trans('admin::controller/products.status') !!}</th>
                        <th width='15%'>{!! trans('admin::controller/products.action') !!}</th>
                    </tr>
                </thead>
                <tbody>

                </tbody>
            </table>
        </div>
    </div>
</div>
@stop
