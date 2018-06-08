@section('global-level-styles')
@parent
{!! HTML::style( URL::asset('global/plugins/uniform/css/uniform.default.min.css') ) !!}
@stop

@section('page-level-styles')
@parent
{!! HTML::style( URL::asset('global/plugins/bootstrap-fileinput/bootstrap-fileinput.css') ) !!}
@stop

@section('page-level-scripts')
@parent
{!! HTML::script( URL::asset('global/plugins/bootstrap-fileinput/bootstrap-fileinput.js') ) !!}
{!! HTML::script( URL::asset('global/plugins/jquery-inputmask/jquery.inputmask.bundle.min.js') ) !!}
@stop

@section('scripts')
@parent
<script>
    jQuery(document).ready(function () {
        siteObjJs.admin.memberProfileImageJs.maxFileSize = "{!! trans('admin::messages.max-file-size') !!}";
        siteObjJs.admin.memberProfileImageJs.mimes = "{!! trans('admin::messages.mimes') !!}";
    });
</script>
@stop


<style>
    .input-width{
        width: 150%;
    }
</style>
<div class="form-body">
    <div class="row">
        <div class="col-md-12">
            <div class="form-group">
                <label class="col-md-2 control-label">{!! trans('admin::controller/member-profile-image.package-name') !!}</label>
                <div class="col-md-8" id='package_list' style="margin-top: 8px;margin-left: -25px;">
                    @if(!empty($memberPackages))
                    <ol>
                        @foreach($memberPackages as $k=>$v)
                        <li>{!! $v !!}</li>
                        @endforeach
                    </ol>
                    @endif

                    {!! Form::hidden('id', $id, [ 'id'=>'id' ])!!}
                    <input type="hidden" value="{!!$before_image!!}" id="before_img_avatar" name="before_img_avatar">

                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-6">
            <div class="form-group ">
                <label class="control-label col-md-4">{!! trans('admin::controller/member-profile-image.before-avatar') !!}</label>
                <div class="col-md-8" style="margin-top: 8px;">
                    <p>{!! trans('admin::controller/member-profile-image.select-before-image-help') .' '.trans('admin::messages.mimes').' '.trans('admin::messages.max-file-size') !!}</p>
                    <div class="fileinput fileinput-new" data-provides="fileinput">
                        <div class="fileinput-new user-form-img margin-bottom-10" >
                            @if(!empty($before_image))
                            {!! \Modules\Admin\Services\Helper\ImageHelper::getUserBeforeImage($id, $before_image) !!}
                            @else
                            {!! HTML::image(URL::asset('images/default-user-icon-profile.png '), 'default-img', ['class' => 'img-thumbnail img-responsive', 'id'=>'before_img']); !!}
                            @endif
                        </div>
                        <div class="fileinput-preview fileinput-exists thumbnail" style="max-width: 200px; max-height: 150px;"></div>
                        <div id='before-file-error' class='text-danger margin-bottom-10 bold'></div>
                        <div class="inline">&nbsp;
                            <span class="btn default btn-file">
                                <span class="fileinput-new">
                                    {!! trans('admin::controller/member-profile-image.select-before-image') !!}
                                </span>
                                <span class="fileinput-exists">{!! trans('admin::messages.change') !!} </span>
                                {!! Form::file('before_image', ['id' => 'before_image', 'class' => 'field']) !!}
                            </span>
                            <span class="fileinput-new">&nbsp;
                                @if(!empty($memberProfileImage->before_image))
                                <!--                                <a href="javascript:;" class="btn default remove-image" >
                                                                    {!! trans('admin::controller/member-profile-image.remove-image') !!} </a>-->
                                @endif
                            </span>&nbsp;
                            <a href="javascript:;" class="btn default fileinput-exists before-fileinput-exists" data-dismiss="fileinput">
                                {!! trans('admin::messages.remove') !!} </a>
                        </div>
                    </div>
                    <div class="clearfix margin-top-15 margin-bottom-15">
                        <span class="label label-danger">{!! trans('admin::messages.note') !!} </span>
                        <span style="margin-left:10px;">{!! trans('admin::controller/member-profile-image.support-image-help') !!}</span>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group ">
                <label class="control-label col-md-4">{!! trans('admin::controller/member-profile-image.after-avatar') !!}</label>
                <div class="col-md-8" style="margin-top: 8px;">
                    <p>{!! trans('admin::controller/member-profile-image.select-after-image-help') .' '.trans('admin::messages.mimes').' '.trans('admin::messages.max-file-size') !!}</p>
                    <div class="fileinput fileinput-new" data-provides="fileinput">
                        <div class="fileinput-new user-form-img margin-bottom-10">
                            @if(!empty($after_image))
                            {!! \Modules\Admin\Services\Helper\ImageHelper::getUserAfterImage($id, $after_image) !!}
                            @else
                            {!! HTML::image(URL::asset('images/default-user-icon-profile.png '), 'default-img', ['class' => 'img-thumbnail img-responsive', 'id'=>'after_img']); !!}
                            @endif
                        </div>
                        <div class="fileinput-preview fileinput-exists thumbnail" style="max-width: 200px; max-height: 150px;"></div>
                        <div id='after-file-error' class='text-danger margin-bottom-10 bold'></div>
                        <div class="inline">&nbsp;
                            <span class="btn default btn-file">
                                <span class="fileinput-new">
                                    {!! trans('admin::controller/member-profile-image.select-after-image') !!}
                                </span>
                                <span class="fileinput-exists">{!! trans('admin::messages.change') !!} </span>
                                {!! Form::file('after_image', ['id' => 'after_image', 'class' => 'field']) !!}
                            </span>
                            <span class="fileinput-new">&nbsp;
                                @if(!empty($memberProfileImage->after_image))
                                <!--                                <a href="javascript:;" class="btn default remove-image" >
                                                                    {!! trans('admin::controller/member-profile-image.remove-image') !!} </a>-->
                                @endif
                            </span>&nbsp;
                            <a href="javascript:;" class="btn default fileinput-exists after-fileinput-exists" data-dismiss="fileinput">
                                {!! trans('admin::messages.remove') !!} </a>
                        </div>
                    </div>
                    <div class="clearfix margin-top-15 margin-bottom-15">
                        <span class="label label-danger">{!! trans('admin::messages.note') !!} </span>
                        <span style="margin-left:10px;">{!! trans('admin::controller/member-profile-image.support-image-help') !!}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>