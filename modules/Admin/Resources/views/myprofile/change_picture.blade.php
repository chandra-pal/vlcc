{{--*/ $userinfo = \Modules\Admin\Services\Helper\UserInfoHelper::getAuthUserInfo() /*--}}
<div id="edit-avatar">
    {!! Form::model($userinfo, ['route' => ['admin.myprofile.update-avatar', $userinfo->id], 'method' => 'put', 'class' => 'profile-form','id'=>'change-avatar-form', 'msg' => trans('admin::messages.changed', ['name' => trans('admin::controller/user.picture')]), 'files' => 'true']) !!}
    {!! Form::hidden('user_type_id', null, ['class'=>'form-control', 'id'=>'user_type_id']) !!}
    {!! Form::hidden('remove', '', ['class'=>'form-control', 'id'=>'remove']) !!}
    <div class="form-body">
        <p>{!! trans('admin::controller/user.change-image-help') .' '.trans('admin::messages.mimes').' '.trans('admin::messages.max-file-size') !!}</p>
        <div class="fileinput fileinput-new" data-provides="fileinput">
            <div class="fileinput-new user-form-img margin-bottom-10">  
                @if(!empty($userinfo->avatar))
                {!! \Modules\Admin\Services\Helper\ImageHelper::getUserAvatar($userinfo->id, $userinfo->avatar) !!}
                @else
                {!! HTML::image(URL::asset('images/default-user-icon-profile.png '), 'default-img', ['class' => 'img-thumbnail img-responsive']); !!}
                @endif
            </div>
            <div class="fileinput-preview fileinput-exists thumbnail" style="max-width: 200px; max-height: 150px;"></div>
            <div id='file-error' class='text-danger margin-bottom-10 bold'></div>
            <div class="inline">&nbsp;
                <span class="btn default btn-file">
                    <span class="fileinput-new">
                        @if(!empty($userinfo->avatar))
                        {!! trans('admin::controller/user.change-image') !!}
                        @else 
                        {!! trans('admin::controller/user.select-image') !!}
                        @endif
                    </span>
                    <span class="fileinput-exists">{!! trans('admin::messages.change') !!} </span>
                    {!! Form::file('avatar', ['id' => 'avatar', 'class' => 'field']) !!}
                </span>
                <span class="fileinput-new">&nbsp;
                    @if(!empty($userinfo->avatar))
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
    <div class="form-actions margin-top-10">
        <div class="row">
            <div class="col-md-12 form-btns">
                <button class="btn green profile-btn" name="tab_1_2" type="submit">{!! trans('admin::messages.submit') !!}</button>
            </div>
        </div>
    </div>
    {!! Form::close() !!}
</div>
