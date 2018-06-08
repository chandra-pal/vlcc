@extends('admin::layouts.master')

@section('page-level-styles')
@parent
{!! HTML::style( asset('global/css/plugins-md.css') ) !!}
@stop

@section('page-level-scripts')
@parent
<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyD2Fto0stqy8KeO2nWlaISKvgshfuXWlFM&libraries=places" type="text/javascript"></script>
{!! HTML::script( URL::asset('global/plugins/gmaps/gmaps.min.js') ) !!}
@stop

@section('template-level-scripts')
@parent
{!! HTML::script( URL::asset('js/admin/centers.js') ) !!}
@stop

@section('scripts')
@parent
<script>
    jQuery(document).ready(function () {
        siteObjJs.admin.centersJs.init();
        siteObjJs.admin.commonJs.boxExpandBtnClick();
    });
    var c_latitude = '{!! config("settings.LATITUDE") !!}';
    var c_longitude = '{!! config("settings.LONGITUDE") !!}';
</script>
@stop

@section('content')
@include('admin::partials.breadcrumb')
<div id="ajax-response-text"></div>

@if(!empty(Auth::guard('admin')->user()->hasAdd))
@include('admin::center.create')
@endif
{{--*/ $linkIcon = \Modules\Admin\Services\Helper\MenuHelper::getSelectedPageLinkIcon() /*--}}
<div id="edit_form">

</div>

<div class="portlet light col-lg-12">
    <div class="portlet-title">
        <div class="caption">
            <i class="fa {{$linkIcon}} font-blue-sharp"></i>
            <span class="caption-subject font-blue-sharp bold uppercase">{!! trans('admin::messages.view-name',['name'=>trans('admin::controller/center.center')]) !!}</span>
        </div>
        @if(!empty(Auth::guard('admin')->user()->hasAdd))
        <div class="actions">
            <a href="javascript:;" class="btn blue btn-add-big btn-expand-form"><i class="fa fa-plus"></i><span class="hidden-480">{!! trans('admin::messages.add-name',['name'=>trans('admin::controller/center.center')]) !!} </span></a>
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
            <table class="table table-striped table-bordered table-hover" id="center-table">
                <thead>
                    <tr role="row" class="heading">
                        <th width='1%'>#</th>
                        <th>{!! trans('admin::controller/center.center-id') !!}</th>
                        <th width="10%">{!! trans('admin::controller/center.center_name') !!}</th>
                        <th width="10%">{!! trans('admin::controller/center.address') !!}</th>
                        <th width="10%">{!! trans('admin::controller/center.area') !!}</th>
                        <th width="20%">{!! trans('admin::controller/center.city').' , '. trans('admin::controller/center.state').' '.' ('. trans('admin::controller/center.country').') ' !!}</th>
                        <th width="10%">{!! trans('admin::controller/center.phone') !!}</th>
                        <th width="5%">{!! trans('admin::controller/center.status') !!}</th>
                        <th width="5%">{!! trans('admin::controller/center.action') !!}</th>
                    </tr>
                    {{--@include('admin::center.search')--}}
                    <tr role="row" class="filter">
                        <td></td>
                        <td></td>
                        <td>{!! Form::text('center_name', null, ['class'=>'form-control form-filter']) !!}</td>
                        <td>

                        </td>
                        <td>
                            {!! Form::text('area', null, ['class'=>'form-control form-filter']) !!}
                        </td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td>
                            {!! Form::button('<i class="fa fa-search"></i>', ['title' => trans('admin::messages.search'), 'class' => 'btn btn-sm yellow filter-submit margin-bottom-5']) !!}
                            {!! Form::button('<i class="fa fa-times"></i>', ['title' => trans('admin::messages.reset'), 'class' => 'btn btn-sm red blue filter-cancel margin-bottom-5']) !!}
                        </td>
                    </tr>
                </thead>
                <tbody>

                </tbody>
            </table>
        </div>
    </div>
</div>
@stop
