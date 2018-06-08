@extends('admin::layouts.master')

@section('global-level-styles')
@parent
{!! HTML::style( URL::asset('global/plugins/uniform/css/uniform.default.min.css') ) !!}
@stop

@section('page-level-styles')
@parent
{!! HTML::style( URL::asset('global/plugins/select2/select2.css') ) !!}
@stop

@section('page-level-scripts')
@parent
{!! HTML::script( URL::asset('global/plugins/select2/select2.min.js') ) !!}
@stop

@section('template-level-scripts')
@parent
{!! HTML::script( URL::asset('js/admin/usertypelinks.js') ) !!}
@stop

@section('scripts')
@parent
<script>
    jQuery(document).ready(function() {
        siteObjJs.admin.usertypelinks.init();
    });
</script>
@stop

@section('content')
<div class="page-head">
    <div class="page-title">
        <h1>Default User Type Links</h1>
    </div>
</div>
<ul class="page-breadcrumb breadcrumb">
    <li><a href="{!! URL::to('/admin') !!}">Admin</a><i class="fa fa-circle"></i></li>
    <li><a href="#">Admin User Management</a><i class="fa fa-circle"></i></li>
    <li><a href="#">Default User Type Links</a></li>
</ul>

@if ($errors->count())
<div class="alert alert-danger">
    <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
    {!! HTML::ul($errors->all()) !!}
</div>
@endif
{!! Form::model($userTypeLinks, ['route' => ['admin.usertype-links.update', $userTypeLinks->id], 'method' => 'put', 'class' => 'roles-permissions-form form-horizontal panel','id'=>'roles-permissions-form']) !!}
<div class="portlet box green">
    <div class="portlet-title togglelable">
        <div class="caption">
            <i class="fa fa-plus"></i>User Type
        </div>
    </div>
    <div class="portlet-body form">
        <div class="form-body">
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="control-label col-md-3">User Type</label>
                        <div class="col-md-9">
                            {!! Form::select('type_id', [''=>'Select User Type'] +$userTypes, null, ['id' => 'select-role','class'=>'select2me form-control' , 'data-rule-required'=>'true', 'data-msg-required'=>trans('admin::messages.required-enter', ['name' => trans('admin::controller/usertypelinks.type_id-required')]) !!}                            
                            <span class="help-block">Select a user type to see the default admin links assigned.</span>
                            <div id="message"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div id="edit_form"></div>
{!! Form::close() !!}

@stop

