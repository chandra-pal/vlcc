{{--*/ $userinfo = \Modules\Admin\Services\Helper\UserInfoHelper::getAuthUserInfo() /*--}}
<div id="change-password">
    {!! Form::model($userinfo, ['route' => ['admin.myprofile.change-password', $userinfo->id], 'method' => 'put', 'class' => 'profile-form','id'=>'change-password-form', 'msg' => trans('admin::messages.changed', ['name' => trans('admin::controller/user.password')]), 'files' => 'true']) !!}
    {!! Form::hidden('user_type_id', null, ['class'=>'form-control', 'id'=>'user_type_id']) !!}
    <div class="form-body">
        <div class="form-group">
            <label class="control-label font-weight-bold">{!! trans('admin::controller/user.current-password') !!}</label>
            {!! Form::password('current_password', ['id' => 'current_password', 'class' => 'form-control placeholder-no-fix', 'data-rule-required' => 'true', 'data-msg-required' => trans('admin::messages.required-enter', ['name' => 'Current Password']), 'data-rule-minlength'=>'8', 'data-msg-minlength'=>trans('admin::messages.error-minlength',['name'=> trans('admin::controller/user.current-password') ]) ]) !!}
        </div>
        <div class="form-group">
            <label class="control-label font-weight-bold">{!! trans('admin::controller/user.new-password') !!}</label>
            {!! Form::password('password', ['id' => 'password_strength', 'class' => 'form-control placeholder-no-fix', 'data-rule-required' => 'true', 'data-msg-required' => trans('admin::messages.required-enter', ['name' => trans('admin::controller/user.new-password')]), 'data-rule-minlength'=>'8', 'data-msg-minlength'=>trans('admin::messages.error-minlength',['name'=> trans('admin::controller/user.new-password')]), 'maxlength' => '100', 'minlength' => '8']) !!}
        </div>
        <div class="form-group">
            <label class="control-label font-weight-bold">{!! trans('admin::messages.re-type', ['name'=> trans('admin::controller/user.new-password')]) !!}</label>
            {!! Form::password('password_confirmation', ['class' => 'form-control placeholder-no-fix', 'data-rule-required' => 'true', 'data-msg-required' => trans('admin::messages.error-required-re-enter', ['name' => trans('admin::controller/user.new-password')]), 'data-msg-equalTo' => trans('admin::controller/user.password-confirmed'), 'data-rule-equalTo' => '#password_strength', 'maxlength' => '100']) !!}
        </div>
    </div>
    <div class="form-actions">
        <div class="row">
            <div class="col-md-12 form-btns">
                <button class="btn green profile-btn" name="tab_1_3" type="submit">{!! trans('admin::messages.change-name',['name'=>trans('admin::controller/user.password')]) !!}</button>
            </div>
        </div>
    </div>
</div>