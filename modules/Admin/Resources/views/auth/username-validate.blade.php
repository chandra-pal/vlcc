@extends('admin::layouts.auth')

@section('title')
{!!trans('admin::controller/login.page-title')!!}
@stop

@section('page-level-scripts')
@parent
{!! HTML::script( URL::asset('global/plugins/bootbox/bootbox.min.js') ) !!}
{!! HTML::script( URL::asset('global/plugins/jquery-validation/js/jquery.validate.min.js') ) !!}
{!! HTML::script( URL::asset('global/plugins/jquery-validation/js/additional-methods.min.js') ) !!}
@stop

@section('template-level-scripts')
@parent
{!! HTML::script( URL::asset('js/validation.js') ) !!}
@stop

@section('scripts')
@parent
{!! HTML::script( URL::asset('js/admin/auth.js') ) !!}
<script>
    $(function () {
        siteObjJs.admin.authJs.init();
        $("#buttonSelector").click(function (){
            $(this).button('loading');
            // Long waiting operation here
             $(this).button('reset');
        });
    });
</script>
@stop

@section('content')
<h3 class="form-title">{!!trans('admin::controller/login.signin')!!}</h3>
<div class="form-group">
    <div id="response-error" class="help-block help-block-error"></div>
    <div id="ip_address-error" class="help-block help-block-error"></div>
</div>
<div id="login-form">
    {!! Form::open(['url' => ['admin/auth/authenticate'], 'method' => 'post', 'id'=>'usernameValidateForm']) !!}
    <div class="form-group">
        <div class="input-group">
            {!! Form::text('username', null, ['class'=>'form-control placeholder-no-fix','placeholder'=>trans('admin::controller/login.username-email'), 'maxlength' => 50, 'minlength'=>2,'id'=>'username', 'data-rule-required'=>'true', 'data-msg-required'=>trans('admin::messages.required-enter', ['name' => trans('admin::controller/login.username-email')]) , 'data-msg-minlength'=>trans('admin::messages.error-minlength', ['name' => trans('admin::controller/login.username-email')]) ])!!}
            <span class="input-group-addon">
                <i class="fa fa-user"></i>
            </span>
        </div>        
    </div>
    <div class="form-group captcha-form-group">
        {!! Recaptcha::render() !!}
        <div id="g-recaptcha-response-error" class="help-block help-block-error"></div>
    </div>
    <div class="form-action">
        {!! Form::submit(trans('admin::controller/login.continue'), ['class'=>'btn btn-success uppercase', 'id'=>'login-continue','data-label'=>trans('admin::controller/login.continue')]) !!}
        <span class="pull-right" id="progress"></span>
    </div>
    {!! Form::close() !!}
</div>
@stop

