@extends('admin::layouts.master')


@section('template-level-scripts')
@parent
{!! HTML::script( URL::asset('js/admin/users.js') ) !!}
@stop

@section('scripts')
@parent
<script>
    jQuery(document).ready(function () {
        siteObjJs.admin.usersJs.initCreate();
    });
</script>
@stop

@section('content')

{{--*/ $menus = [
    ['label' => trans('admin::controller/user.user-management'), 'link' => 'admin/user'],
    ['label' => trans('admin::controller/user.create-user')]];
/*--}}
@include('admin::partials.breadcrumb', ['title' => trans('admin::controller/user.manage-users'), 'menus' => $menus ])
@include('admin::partials.error', ['type' => 'success', 'message' => session('ok'), 'errors' => $errors])

<div id="loader" style="margin-bottom: 20px;"></div>

<div class="portlet box blue">
    <div class="portlet-title">
        <div class="caption">
            <i class="fa fa-plus"></i>{!! trans('admin::messages.add-name', ['name' =>trans('admin::controller/user.user')]) !!}
        </div>
    </div>
    <div class="portlet-body form">      
        {!! Form::open(['route' => ['admin.user.store'], 'data-user-id' => '', 'id' => 'admin-user-form', 'method' => 'post', 'class' => 'form-horizontal admin-user-form', 'files' => 'true']) !!}
        @include('admin::users.form',['from'=>'create'])
        {!! Form::close() !!}
    </div>
</div>

@stop