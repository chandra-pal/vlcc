{!! Form::open(['url' => ['admin/auth/login'], 'method' => 'post', 'id'=>'loginForm']) !!}
<div id="login-error-msg"></div>
<div class="form-group">
    <label class="control-label visible-ie8 visible-ie9">{!! trans('admin::controller/login.username') !!}</label>
    <div class="input-group">
        {!! Form::text('username', null, ['class'=>'form-control placeholder-no-fix','maxlength' => 50, 'minlength'=>2,'id'=>'username', 'data-rule-required'=>'true', 'data-msg-required'=>trans('admin::messages.required-enter', ['name' => trans('admin::controller/login.username-email')]) , 'data-msg-minlength'=>trans('admin::messages.error-minlength', ['name' => trans('admin::controller/login.username-email')]),'disabled'])!!}
        <span class="input-group-addon">
            <i class="fa fa-user"></i>
        </span>
    </div>
</div>
<div class="form-group">
    <div class="input-group">
        {!! Form::password('password', ['maxlength'=>50,'class'=>'form-control placeholder-no-fix','placeholder'=>trans('admin::controller/login.password'),'id'=>'password','data-rule-required'=>'true', 'data-msg-required'=>trans('admin::messages.required-enter', ['name' => trans('admin::controller/login.password')])]) !!}
        <span class="input-group-addon">
            <i class="fa fa-lock"></i>
        </span>
    </div>
</div>
<div class="form-action">
    {!! Form::submit(trans('admin::controller/login.login'), ['class' => 'btn btn-success uppercase', 'id'=>'login-submit-btn','data-label'=>trans('admin::controller/login.login')]) !!}
    <span class="pull-right" id="progress"></span>
</div>
{!! Form::close() !!}