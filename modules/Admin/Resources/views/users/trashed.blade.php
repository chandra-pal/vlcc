@extends('admin::layouts.master')

@section('template-level-scripts')
@parent
{!! HTML::script( URL::asset('js/admin/users.js') ) !!}
@stop

@section('scripts')
@parent
<script>
    jQuery(document).ready(function () {
        siteObjJs.admin.usersJs.init('#trashed_users_datatable_ajax', adminUrl + '/user/trashed-data');
        siteObjJs.admin.usersJs.deleteMessage = "{!! trans('admin::messages.delete-message') !!}";
        siteObjJs.admin.usersJs.restoreMessage = "{!! trans('admin::messages.restore-message') !!}";
        siteObjJs.admin.commonJs.boxExpandBtnClick();
    });
</script>
@stop

@section('content')

{{--*/ $menus = [
    ['label' => trans('admin::controller/user.user-management'), 'link' => 'admin/user'],
    ['label' => trans('admin::controller/user.manage-users-trashed'), 'link' => 'admin/user/trashed']
];
/*--}}
@include('admin::partials.breadcrumb', ['title' => trans('admin::controller/user.manage-users-trashed'), 'menus' => $menus ])
@include('admin::partials.error', ['type' => 'success', 'message' => session('ok'), 'errors' => $errors])

<div class="row">
    <div class="col-md-12">
        <div class="portlet light">
            <div class="portlet-title">
                <div class="caption">
                    <i class="fa fa-list font-blue-sharp"></i>
                    <span class="caption-subject font-blue-sharp bold uppercase">{!! trans('admin::controller/user.view-trashed-users') !!}</span>
                </div>
                <div class="actions">
                    <a href="{{ URL::to('/admin/user')}}" class="btn green">{!! trans('admin::controller/user.show-active-users') !!}</a>
                </div>
            </div>
            <div class="portlet-body">
                <div class="table-container">
                    <div class="table-actions-wrapper">
                        {!!  Form::select('bulk_action', $bulkAction, Input::old('bulk_action'), ['required', 'class'=>'table-group-action-input form-control input-inline input-small input-sm'])!!}
                        <button class="btn btn-sm yellow table-group-action-submit"><i class="fa fa-check"></i> {!! trans('admin::messages.submit') !!}</button>
                    </div>
                    <table class="table table-striped table-bordered table-hover" id="trashed_users_datatable_ajax">
                        <thead>
                            <tr role="row" class="heading">
                                <th><input type="checkbox" class="group-checkable"></th>
                                <th>#</th>
                                <th width="10%">{!! trans('admin::controller/user.avatar') !!}</th>
                                <th width="10%">{!! trans('admin::controller/user.username') !!} ({!! trans('admin::controller/user.usertype') !!})<br/> {!! trans('admin::controller/user.userid') !!}</th>
                                <th width="10%">{!! trans('admin::controller/user.fullname').' ('. trans('admin::controller/user.gender').') ' !!} <br/> {!! trans('admin::controller/user.email') !!}<br/> {!! trans('admin::controller/user.contact') !!}</th>
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