@extends('admin::layouts.master')

@section('title')
{{ $data['page_title'] }}
@stop

@section('page-level-scripts')
@parent
{!! HTML::script( URL::asset('global/plugins/jquery.input-ip-address-control-1.0.min.js') ) !!}
@stop

@section('template-level-scripts')
@parent
{!! HTML::script( URL::asset('js/admin/ipaddress.js') ) !!}
@stop

@section('scripts')
@parent

<script>
    jQuery(document).ready(function () {
        siteObjJs.admin.ipIpaddressJs.init();
        siteObjJs.admin.commonJs.boxExpandBtnClick();
    });
</script>
@stop

@section('content')

{{--*/ $menus = [
    ['label' => 'Manage IP Addresses', 'link' => 'admin/ipaddress']];
/*--}}
@include('admin::partials.breadcrumb', ['menus' => $menus ])
@include('admin::partials.error', ['type' => 'success', 'message' => session('ok'), 'errors' => $errors])

@if(!empty(Auth::guard('admin')->user()->hasAdd))
@include('admin::ipaddress.create')
@endif
{{--*/ $linkIcon = \Modules\Admin\Services\Helper\MenuHelper::getSelectedPageLinkIcon() /*--}}
<div id="edit_form">

</div>

<div class="portlet light col-lg-12">
    <div class="portlet-title">
        <div class="caption">
            <i class="fa {{ $linkIcon }} font-blue-sharp"></i>
            <span class="caption-subject font-blue-sharp bold uppercase">View IP Addresses</span>
        </div>
        <div class="actions">
            @if(!empty(Auth::guard('admin')->user()->hasAdd))
            <a href="javascript:;" class="btn blue btn-add-big btn-expand-form"><i class="fa fa-plus"></i><span class="hidden-480">Add New IP Address </span></a>
            @endif
        </div>
    </div>
    <div class="portlet-body">
        <div class="table-container">
            <div class="table-actions-wrapper">
                <span>
                </span>
                @if(!empty(Auth::guard('admin')->user()->hasEdit) || (!empty(Auth::guard('admin')->user()->hasOwnEdit)))
                {!!  Form::select('bulk_action', ['' => 'Select',0 => 'Pending', 1 =>'Accepted', 2 => 'Rejected'], null, ['required', 'class'=>'table-group-action-input form-control input-inline input-small input-sm'])!!}
                
                @else
                
                {!!  Form::select('bulk_action', ['' => 'Select'], null, ['required', 'class'=>'table-group-action-input form-control input-inline input-small input-sm'])!!}
                @endif

                <button class="btn btn-sm yellow table-group-action-submit"><i class="fa fa-check"></i> {!! trans('admin::messages.submit') !!}</button>
            </div>
            <table class="table table-striped table-bordered table-hover" id="grid-table">
                <thead>
                    <tr role="row" class="heading">
                        <th><input type="checkbox" class="group-checkable"></th>
                        <th>#</th>
                        <th>IP Address</th>
                        <th>Login Details</th>
                        <th width="7%">{!! trans('admin::messages.status') !!}</th>
                        <th width="7%">{!! trans('admin::messages.create-date') !!}</th>
                        <th width="3%">{!! trans('admin::messages.options') !!}</th>
                    </tr>
                    @include('admin::ipaddress.search')
                </thead>
                <tbody>

                </tbody>
            </table>
        </div>
    </div>
</div>
@stop

