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
@include('admin::partials.breadcrumb')

<div id="errorMessage"></div>

{!! Form::open(['route' => ['admin.usertype-links.store'], 'method' => 'post', 'class' => 'form-horizontal panel roles-permissions-form','id'=>'roles-permissions-form','class'=>'']) !!}
<div class="portlet box blue">
    <div class="portlet-title togglelable">
        <div class="caption">
            <i class="fa fa-plus"></i>{!! trans('admin::controller/usertypelinks.user_type')!!}
        </div>
    </div>
    <div class="portlet-body form">
        <div class="form-body">
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="control-label col-md-3">User Type</label>
                        <div class="col-md-9">
                            {!! Form::select('type_id', [''=>'Select User Type'] +$userTypes, null, ['id' => 'select-role','class'=>'select2me form-control' , 'data-rule-required'=>'true', 'data-msg-required'=>trans('admin::messages.required-select', ['name' => trans('admin::controller/usertypelinks.type_id-required')]) ]) !!}                            
                            <span class="help-block">{!! trans('admin::controller/usertypelinks.user_type_help_block')!!}</span>
                            <div id="message"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@include('admin::usertype-links.form')
{!! Form::close() !!}

@stop

