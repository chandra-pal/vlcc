@section('global-level-styles')
@parent
{!! HTML::style( URL::asset('global/plugins/uniform/css/uniform.default.min.css') ) !!}
@stop

@section('page-level-styles')
@parent
{!! HTML::style( URL::asset('global/plugins/bootstrap-fileinput/bootstrap-fileinput.css') ) !!}
{!! HTML::style( URL::asset('css/admin/admin-user.css') ) !!}

@stop

@section('page-level-scripts')
@parent
{!! HTML::script( URL::asset('global/plugins/bootstrap-fileinput/bootstrap-fileinput.js') ) !!}
{!! HTML::script( URL::asset('global/plugins/jquery-inputmask/jquery.inputmask.bundle.min.js') ) !!}
{!! HTML::script( URL::asset('global/plugins/bootstrap-pwstrength/pwstrength-bootstrap.min.js') ) !!}
{!! HTML::script( URL::asset('global/plugins/bootstrap-maxlength/bootstrap-maxlength.min.js') ) !!}
{!! HTML::script( URL::asset('js/admin/validate-users.js') ) !!}
@stop

@section('scripts')
@parent
<script>
    jQuery(document).ready(function () {
        siteObjJs.admin.validateUserJs.init();
        siteObjJs.admin.validateUserJs.whatIsIpTitle = "{!! trans('admin::controller/user.skip-ip-what') !!}";
        siteObjJs.admin.validateUserJs.whatIsIpDesc = "{!! trans('admin::controller/user.skip-ip-help') !!}";
        siteObjJs.admin.validateUserJs.confirmRemoveImage = "{!! trans('admin::messages.confirm-remove-image') !!}";
        siteObjJs.admin.validateUserJs.maxFileSize = "{!! trans('admin::messages.max-file-size') !!}";
        siteObjJs.admin.validateUserJs.mimes = "{!! trans('admin::messages.mimes') !!}";
    });
</script>
@stop

<div class="form-body">
    <h3 class="form-section">{!! trans('admin::controller/user.user-info') !!}</h3>
    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                <label class="control-label col-md-4">{!! trans('admin::controller/user.username') !!}<span class="required" aria-required="true">*</span></label>
                <div class="col-md-8">
                    <div class="input-group">
                        {!! Form::hidden('remove', '', ['class'=>'form-control', 'id'=>'remove']) !!}
                        @if($from == 'create')
                        {!! Form::text('username', null, ['id' => 'user_name', 'class' => 'form-control', 'data-rule-required' => 'true', 'data-msg-required' => trans('admin::messages.required-enter', ['name' => trans('admin::controller/user.username')]), 'maxlength' => '50', 'minlength' => '4']) !!}
                        <span class="input-group-btn">
                            <a href="javascript:;" class="btn blue" id="username1_checker">
                                <i class="fa fa-check"></i> {!! trans('admin::messages.check') !!}</a>
                        </span>
                        @else
                        {!! Form::text('username', null, ['disabled' => 'disabled', 'id' => 'user_name', 'class' => 'form-control', 'data-rule-required' => 'true', 'data-msg-required' => trans('admin::messages.required-enter', ['name' => trans('admin::controller/user.username')]), 'maxlength' => '50', 'minlength' => '4']) !!}
                        <span class="input-group-btn disabled">
                            <a href="javascript:;" class="btn blue disabled" id="username1_checker" disabled>
                                <i class="fa fa-check"></i> {!! trans('admin::messages.check') !!}</a>
                        </span>
                        @endif
                    </div>
                    @if($from == 'create')
                    <div class="help-block">{!! trans('admin::controller/user.username-help') !!}</div>
                    @endif
                </div>
            </div>
        </div>
        <div class="col-md-6">&nbsp;</div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="form-group last password-strength">
                <label class="control-label col-md-4">{!! trans('admin::controller/user.new-password') !!}<span class="required" aria-required="true">*</span></label>
                <div class="col-md-8">
                    <?php
                    $passwordRule = true;
                    if ($from == 'update') {
                        $passwordRule = false;
                    }
                    ?>
                    {!! Form::password('password', ['id' => 'password_strength', 'class' => 'form-control placeholder-no-fix', 'data-rule-required' => "$passwordRule", 'data-msg-required' => trans('admin::messages.required-enter', ['name' => trans('admin::controller/user.password')]), 'maxlength' => '100', 'minlength' => '8']) !!}

                    @if($from == 'update')
                    <span class="help-block">{!! trans('admin::controller/user.password-edit-help') !!}</span>
                    @endif
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label class="control-label col-md-5">{!! trans('admin::messages.re-type', ['name'=> trans('admin::controller/user.new-password')]) !!}<span class="required" aria-required="true">*</span></label>
                <div class="col-md-7">
                    {!! Form::password('password_confirmation', ['class' => 'form-control placeholder-no-fix', 'data-rule-required' => "$passwordRule", 'data-msg-required' => trans('admin::messages.required-enter', ['name' => trans('admin::controller/user.confirm-password')]), 'data-msg-equalTo' => trans('admin::controller/user.password-confirmed'), 'data-rule-equalTo' => '#password_strength', 'maxlength' => '100']) !!}
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                <label class="control-label col-md-4">{!! trans('admin::controller/user.first-name') !!}<span class="required" aria-required="true">*</span></label>
                <div class="col-md-8">
                    {!! Form::text('first_name', null, ['id' => 'first_name', 'class' => 'form-control', 'data-rule-required' => 'true', 'data-msg-required' => trans('admin::messages.required-enter', ['name' => trans('admin::controller/user.first-name')]), 'minlength' => '2', 'maxlength' => '60']) !!}
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label class="col-md-5 control-label">{!! trans('admin::controller/user.last-name') !!}<span class="required" aria-required="true">*</span></label>
                <div class="col-md-7">
                    {!! Form::text('last_name', null, ['id' => 'last_name', 'class' => 'form-control', 'minlength' => '2', 'maxlength' => '60']) !!}
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                <label class="control-label col-md-4">{!! trans('admin::controller/user.email') !!}</label>
                <div class="col-md-8">
                    {!! Form::text('email', null, ['id' => 'email', 'class' => 'form-control', 'data-msg-required' => trans('admin::messages.required-enter', ['name' => trans('admin::controller/user.email')]), 'data-rule-email' => 'true','data-msg-email' => trans('admin::messages.valid-enter', ['name' => trans('admin::controller/user.email')]), 'maxlength' => '100']) !!}
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label class="control-label col-md-5">{!! trans('admin::controller/user.contact') !!}<span class="required" aria-required="true">*</span></label>
                <div class="col-md-7">
                    {!! Form::text('contact', null, ['minlength'=>10,'class'=>'form-control', 'id'=>'contact', 'data-rule-required'=>'true', 'data-msg-required'=> trans('admin::messages.required-enter', ['name' => trans('admin::controller/user.contact')]), 'maxlength' => '10', 'data-rule-number'=>'10', 'data-msg-number'=>'Please enter numbers only.', 'data-msg-minlength'=>trans('admin::messages.error-minlength', ['name' => trans('admin::controller/user.contact')]) ])!!}
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="form-group ">
                <label class="control-label col-md-4">{!! trans('admin::controller/user.avatar') !!}</label>
                <div class="col-md-8">
                    <p>{!! trans('admin::controller/user.select-image-help') .' '.trans('admin::messages.mimes').' '.trans('admin::messages.max-file-size') !!}</p>
                    <div class="fileinput fileinput-new" data-provides="fileinput">
                        <div class="fileinput-new user-form-img margin-bottom-10">
                            @if($from == 'update' && !empty($user->avatar))
                            {!! \Modules\Admin\Services\Helper\ImageHelper::getUserAvatar($user->id, $user->avatar) !!}
                            @else
                            {!! HTML::image(URL::asset('images/default-user-icon-profile.png '), 'default-img', ['class' => 'img-thumbnail img-responsive']); !!}
                            @endif
                        </div>
                        <div class="fileinput-preview fileinput-exists thumbnail" style="max-width: 200px; max-height: 150px;"></div>
                        <div id='file-error' class='text-danger margin-bottom-10 bold'></div>
                        <div class="inline">&nbsp;
                            <span class="btn default btn-file">
                                <span class="fileinput-new">
                                    @if($from == 'update' && !empty($user->avatar))
                                    {!! trans('admin::controller/user.change-image') !!}
                                    @else
                                    {!! trans('admin::controller/user.select-image') !!}
                                    @endif
                                </span>
                                <span class="fileinput-exists">{!! trans('admin::messages.change') !!} </span>
                                {!! Form::file('thefile', ['id' => 'avatar', 'class' => 'field']) !!}
                            </span>
                            <span class="fileinput-new">&nbsp;
                                @if(!empty($user->avatar))
                                <a href="javascript:;" class="btn default remove-image" >
                                    {!! trans('admin::controller/user.remove-image') !!} </a>
                                @endif
                            </span>&nbsp;
                            <a href="javascript:;" class="btn default fileinput-exists" data-dismiss="fileinput">
                                {!! trans('admin::messages.remove') !!} </a>
                        </div>
                    </div>
                    <div class="clearfix margin-top-15 margin-bottom-15">
                        <span class="label label-danger">{!! trans('admin::messages.note') !!} </span>
                        <span style="margin-left:10px;">{!! trans('admin::controller/user.support-image-help') !!}</span>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label class="col-md-5 control-label">{!! trans('admin::controller/user.gender') !!}<span class="required" aria-required="true">*</span></label>
                <div class="col-md-7">
                    <div class="radio-list">
                        <label class="radio-inline">
                            {!! Form::radio('gender', 1, null, ['class' => 'form-control', 'data-rule-required' => 'true']) !!} {!! trans('admin::controller/user.male') !!}
                        </label>
                        <label class="radio-inline">
                            {!! Form::radio('gender', 0, null, ['class' => 'form-control']) !!} {!! trans('admin::controller/user.female') !!}
                        </label>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                <label class="col-md-4 control-label">{!! trans('admin::controller/user.user-type') !!}<span class="required" aria-required="true">*</span></label>
                <div class="col-md-8">
                    {!!  Form::select('user_type_id',['' => trans('admin::controller/user.select-user-type')]+ $userType, null, ['id' => 'select-user-type', 'class'=>'select2me form-control width-auto', 'data-rule-required' => 'true', 'data-msg-required' => trans('admin::messages.required-select', ['name' => trans('admin::controller/user.user-type')])]) !!}
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label class="col-md-5 control-label">{!! trans('admin::controller/user.skip-ip-check') !!}</label>
                <div class="col-md-7 checkbox-block">
                    {!! Form::checkbox('skip_ip_check', 1, null, ['id' => 'skipId', 'class' => 'form-control']) !!}
                    <a href="javascript:;" id="what_is_this">[{!! trans('admin::messages.what-is-this') !!}]</a>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                <label class="col-md-4 control-label">Centers </label>
                <div class="col-md-8">
                    {!! Form::select('center_id[]',[''=>'Select Center'] +$centerList, $selectedCenters,['autocomplete' => 'off','multiple'=>'multiple','class'=>'select2me form-control', 'id' => 'center_id', 'data-msg-required'=>trans('admin::messages.required-select', ['name' => trans('admin::controller/user.center')])]) !!}
                    <span class="help-block">Assign centers.</span>
                </div>
            </div>
        </div>
         <div class="col-md-6">
            <div class="form-group">
                <label class="control-label col-md-3">Status <span class="required" aria-required="true">*</span></label>
                <div class="col-md-9">
                    <div class="radio-list">
                        <label class="radio-inline">{!! Form::radio('status', '1', true) !!} {!! trans('admin::messages.active') !!}</label>
                        <label class="radio-inline">{!! Form::radio('status', '0') !!} {!! trans('admin::messages.inactive') !!}</label>
                    </div>
                </div>
            </div>
        </div>
    </div>

    
    <hr />
    <div class="row assignLinks-block">
        @include('admin::users.user-links')
    </div>
    <div class="form-actions">
        <div class="row">
            <div class="col-md-6">
                <div class="row">
                    <div class="col-md-offset-4 col-md-8">
                        <button type="submit" class="btn blue create-user-submit" name="submit">{!! trans('admin::messages.save') !!}</button>
                        @if($from == 'create')
                        <!--<button type="submit" name="submit_save" class="btn blue">{!! trans('admin::messages.save-add') !!}</button>-->
                        @endif
                        <a href='{{URL::to("/admin/user")}}' class="btn default btn-collapse-form">{!! trans('admin::messages.cancel') !!}</a>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
            </div>
        </div>
    </div>
</div>

