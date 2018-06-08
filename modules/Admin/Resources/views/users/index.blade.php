@extends('admin::layouts.master')

@section('template-level-scripts')
@parent
{!! HTML::script( URL::asset('js/admin/users.js') ) !!}
@stop

@section('scripts')
@parent
<script>
    jQuery(document).ready(function () {
        siteObjJs.admin.usersJs.init();
        siteObjJs.admin.usersJs.deleteMessage = "{!! trans('admin::messages.delete-message') !!}";
        siteObjJs.admin.usersJs.restoreMessage = "{!! trans('admin::messages.restore-message') !!}";
        siteObjJs.admin.commonJs.boxExpandBtnClick();
    });
</script>
@stop

@section('content')

{{--*/ $menus = [
    ['label' => trans('admin::controller/user.user-management'), 'link' => 'admin/user'],
    ['label' => trans('admin::controller/user.manage-users'), 'link' => 'admin/user']];
/*--}}
@include('admin::partials.breadcrumb')
@include('admin::partials.error', ['type' => 'success', 'message' => session('ok'), 'errors' => $errors])
{{--*/ $linkIcon = \Modules\Admin\Services\Helper\MenuHelper::getSelectedPageLinkIcon() /*--}}
<div class="loading_message"></div>
<div class="row">
    <div class="col-md-12">
        <div class="portlet light">
            <div class="portlet-title">
                <div class="caption">
                    <i class="fa {{ $linkIcon }} font-blue-sharp"></i>
                    <span class="caption-subject font-blue-sharp bold uppercase">{!! trans('admin::controller/user.view-users') !!}</span>
                </div>
                <div class="actions">
                    @if(!empty(Auth::guard('admin')->user()->hasAdd))
                    <a href="{{ URL::to('/admin/user/create')}}" class="btn blue"><i class="fa fa-plus"></i>{!! trans('admin::messages.add-name', ['name' => trans('admin::messages.user') ]) !!}</a>
                    @endif
                    @if(!empty(Auth::guard('admin')->user()->hasOwnView))
                    <a href="{{ URL::to('/admin/user/trashed')}}" class="btn red">{!! trans('admin::controller/user.deleted-users') !!}</a>
                    @endif
                </div>
            </div>
            <div class="portlet-body">
                <div class="table-container">
                    <div class="table-actions-wrapper">
                        <input type="hidden" name="assets_url" value="{!! URL::asset('images') !!}" class="assets_url">
                        {!!  Form::select('bulk_action', $bulkAction, null, ['required', 'class'=>'table-group-action-input form-control input-inline input-small input-sm'])!!}
                        <button class="btn btn-sm yellow table-group-action-submit"><i class="fa fa-check"></i> {!! trans('admin::messages.submit') !!}</button>
                    </div>
                    <table class="table table-striped table-bordered table-hover" id="users_datatable_ajax">
                        <thead>
                            <tr role="row" class="heading">
                                <th><input type="checkbox" class="group-checkable"></th>
                                <th>#</th>
                                <th width="10%">{!! trans('admin::controller/user.avatar') !!}</th>
                                <th width="10%">{!! trans('admin::controller/user.username') !!} ({!! trans('admin::controller/user.usertype') !!})<br/> {!! trans('admin::controller/user.userid') !!}</th>
                                <th width="10%">{!! trans('admin::controller/user.fullname').' ('. trans('admin::controller/user.gender').') ' !!} <br/> {!! trans('admin::controller/user.email') !!}<br/> {!! trans('admin::controller/user.contact') !!}</th>
                                <th width="10%">Center</th>
                                <th width="10%">{!! trans('admin::controller/user.links-assigned') !!}</th>
                                <th width="5%">{!! trans('admin::messages.create-date') !!}</th>
                                <th width="3%">{!! trans('admin::messages.status') !!}</th>
                                <th width="3%">{!! trans('admin::messages.options') !!}</th>
                            </tr>
                            @include('admin::users.search')
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@stop