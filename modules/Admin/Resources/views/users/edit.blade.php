@extends('admin::layouts.master')

@section('template-level-scripts')
@parent
{!! HTML::script( URL::asset('js/admin/users.js') ) !!}
@stop

@section('scripts')
@parent
<script>
    jQuery(document).ready(function () {
        siteObjJs.admin.usersJs.initEdit();
    });
</script>
@stop

@section('content')

{{--*/ $menus = [
    ['label' => trans('admin::controller/user.user-management'), 'link' => 'javascript:;'],
    ['label' => trans('admin::controller/user.edit-admin-user'), 'link' => '']];
/*--}}
@include('admin::partials.breadcrumb', ['title' => trans('admin::controller/user.manage-users'), 'menus' => $menus ])
@include('admin::partials.error', ['type' => 'success', 'message' => session('ok'), 'errors' => $errors])

<div class="portlet box yellow-gold">
    <div class="portlet-title">
        <div class="caption">
            <i class="fa fa-pencil"></i>{!! trans('admin::controller/user.edit-admin-user') !!}
        </div>
    </div>
    <div class="portlet-body form">
        {!! Form::model($user, ['route' => ['admin.user.update', $user->id], 'data-user-id' => $user->id, 'id' => 'admin-user-form', 'method' => 'put', 'class' => 'form-horizontal admin-user-form', 'files' => 'true']) !!}
        @include('admin::users.form',['from'=>'update'])
        {!! Form::close() !!}
    </div>
</div>

@stop