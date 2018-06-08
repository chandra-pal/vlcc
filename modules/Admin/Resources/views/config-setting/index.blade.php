@extends('admin::layouts.master')

@section('template-level-scripts')
@parent
{!! HTML::script( URL::asset('js/admin/config-settings.js') ) !!}
@stop

@section('scripts')
@parent
<script>
    jQuery(document).ready(function () {
        siteObjJs.admin.configSettingsJs.init();
        siteObjJs.admin.commonJs.boxExpandBtnClick();

        // Uncomment the below line to overwrite the jquery constants
        // siteObjJs.admin.commonJs.constants.alertCloseSec = 2;
    });


</script>
@stop

@section('content')

@include('admin::partials.breadcrumb')
<div id="ajax-response-text"></div>

@if(!empty(Auth::guard('admin')->user()->hasAdd))
@include('admin::config-setting.create')
@endif

{{--*/ $linkIcon = \Modules\Admin\Services\Helper\MenuHelper::getSelectedPageLinkIcon() /*--}}

<div id="edit_form">

</div>

<div class="portlet light col-lg-12 config-settings">
    <div class="portlet-title">
        <div class="caption">
            <i class="fa {{ $linkIcon }} font-blue-sharp"></i>
            <span class="caption-subject font-blue-sharp bold uppercase">{!! trans('admin::messages.view-name', ['name' => trans('admin::controller/config-setting.conf-sets') ] ) !!}</span>
        </div>
        @if(!empty(Auth::guard('admin')->user()->hasAdd))
        <div class="actions">
            <a class="btn blue btn-add-big btn-expand-form" href="javascript:;">
                <i class="fa fa-plus"></i> {!! trans('admin::messages.add-name',['name' => trans('admin::controller/config-setting.conf-set')]) !!}
            </a>
        </div>
        @endif
    </div>
    <div class="portlet-body">
        <div class="table-container">
            <div class="table-actions-wrapper">
                <span></span>

                <table class="">
                    <tbody>
                    <td>
                        {!! Form::select('search_config_category', [''=>'All'] + $categoryList, '',['class'=>'form-control width-auto', 'id' => 'search_config_category', 'column-index' => '2']) !!}
                    </td>
                    <td>&nbsp;&nbsp;&nbsp;</td>
                    <td>
                        <input type="hidden" name="_token" value="{!! csrf_token() !!}">
                        <input id="data-search" type="search" class="form-control" placeholder="Search">
                    </td>
                    </tbody>
                </table>
            </div>
            <table class="table table-striped table-bordered table-hover" id="grid-table">
                <thead>
                    <tr role="row" class="heading">
                        <th>#</th>
                        <th>{!! trans('admin::controller/config-setting.name') !!}</th>
                        <th>{!! trans('admin::controller/config-setting.id') !!}</th>
                        <th>{!! trans('admin::controller/config-setting.label-desc') !!}</th>
                        <th>{!! trans('admin::controller/config-setting.const-name') !!}</th>
                        <th>{!! trans('admin::controller/config-setting.const-value') !!}</th>
                        <th>{!! trans('admin::controller/config-setting.action') !!}</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>
</div>
@stop