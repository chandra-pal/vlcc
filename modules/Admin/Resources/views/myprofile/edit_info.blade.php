{{--*/ $userinfo = \Modules\Admin\Services\Helper\UserInfoHelper::getAuthUserInfo() /*--}}
<div id="user-info">
    {!! Form::model($userinfo, ['route' => ['admin.myprofile.update', $userinfo->id], 'method' => 'put', 'class' => 'profile-form','id'=>'edit-info-form', 'msg' => trans('admin::messages.updated', ['name' => trans('admin::controller/user.profile')]), 'files' => 'true']) !!}
    {!! Form::hidden('user_type_id', null, ['class'=>'form-control', 'id'=>'user_type_id']) !!}
    <div class="form-group">
        <label class="control-label font-weight-bold">{!! trans('admin::controller/user.first-name') !!}<span class="required" aria-required="true">*</span></label>
        {!! Form::text('first_name', null, ['minlength'=>2,'maxlength'=>60,'class'=>'form-control', 'id'=>'first_name', 'data-rule-required'=>'true', 'data-msg-required'=> trans('admin::messages.required-enter', ['name' => trans('admin::controller/user.first-name')]), 'data-rule-maxlength'=>'60', 'data-msg-maxlength'=>trans('admin::messages.error-maxlength', ['name'=>trans('admin::controller/user.first-name')]) , 'data-msg-minlength'=>trans('admin::messages.error-minlength', ['name' => trans('admin::controller/user.first-name')]) ])!!}
    </div>
    <div class="form-group">
        <label class="control-label font-weight-bold">{!! trans('admin::controller/user.last-name') !!}<span class="required" aria-required="true">*</span></label>
        {!! Form::text('last_name', null, ['minlength'=>2,'maxlength'=>60,'class'=>'form-control', 'id'=>'last_name', 'data-rule-required'=>'true', 'data-msg-required'=> trans('admin::messages.required-enter', ['name' => trans('admin::controller/user.last-name')]), 'data-rule-maxlength'=>'60', 'data-msg-maxlength'=> trans('admin::messages.error-maxlength',['name'=> trans('admin::controller/user.last-name')]) , 'data-msg-minlength'=>trans('admin::messages.error-minlength', ['name' => trans('admin::controller/user.last-name')]) ])!!}
    </div>
    <div class="form-group">
        <label class="control-label font-weight-bold">{!! trans('admin::controller/user.email') !!}<span class="required" aria-required="true">*</span></label>
        {!! Form::text('email', null, ['maxlength'=>100,'class'=>'form-control', 'id'=>'email', 'data-rule-required'=>'true', 'data-msg-required'=> trans('admin::messages.required-enter', ['name' => trans('admin::controller/user.email')]), 'data-rule-maxlength'=>'100', 'data-msg-maxlength'=> trans('admin::messages.error-maxlength',['name'=> trans('admin::controller/user.email')]), 'data-rule-email'=>'true', 'data-msg-email'=>trans('admin::messages.error-valid',['name' => trans('admin::controller/user.email')]) ]) !!}
    </div>
    <div class="form-group">
        <label class="control-label font-weight-bold">{!! trans('admin::controller/user.contact') !!}<span class="required" aria-required="true">*</span></label>
        {!! Form::text('contact', null, ['minlength'=>10,'class'=>'form-control', 'id'=>'contact', 'data-rule-required'=>'true', 'data-msg-required'=> trans('admin::messages.required-enter', ['name' => trans('admin::controller/user.contact')]), 'maxlength' => '10', 'data-rule-number'=>'10', 'data-msg-number'=>'Please enter numbers only.', 'data-msg-minlength'=>trans('admin::messages.error-minlength', ['name' => trans('admin::controller/user.contact')]) ])!!}
    </div>
    <div class="form-group">
        <label class="control-label font-weight-bold">{!! trans('admin::controller/user.gender') !!}<span class="required" aria-required="true">*</span></label>
        <div class="radio-list">
            <label class="radio-inline">{!! Form::radio('gender', '1', true) !!} {!! trans('admin::controller/user.gender-male') !!}</label>
            <label class="radio-inline">{!! Form::radio('gender', '0') !!} {!! trans('admin::controller/user.gender-female') !!}</label>
        </div>
    </div>
    <div class="margin-top-10">
        <button class="btn green profile-btn" name="tab_1_1" type="submit">{!! trans('admin::messages.save-changes') !!}</button>
    </div>
    {!! Form::close() !!}
</div>
