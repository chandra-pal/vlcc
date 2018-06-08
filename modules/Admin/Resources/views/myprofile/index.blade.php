@extends('admin::layouts.master')

@section('page-level-styles')
@parent
{!! HTML::style( URL::asset('global/plugins/select2/select2.css') ) !!}
{!! HTML::style( URL::asset('global/plugins/bootstrap-fileinput/bootstrap-fileinput.css') ) !!}
{!! HTML::style( URL::asset('admintheme/pages/css/profile.css') ) !!}
@stop

@section('page-level-scripts')
@parent
{!! HTML::script( URL::asset('global/plugins/select2/select2.min.js') ) !!}
{!! HTML::script( URL::asset('global/plugins/bootstrap-fileinput/bootstrap-fileinput.js') ) !!}
@stop

@section('template-level-scripts')
@parent
{!! HTML::script( URL::asset('js/admin/my-profile.js') ) !!}
@stop

@section('scripts')
@parent
<script>
    jQuery(document).ready(function () {
        siteObjJs.admin.myProfileJs.init();
        siteObjJs.admin.myProfileJs.confirmRemoveImage = "{!! trans('admin::messages.confirm-remove-image') !!}";
        siteObjJs.admin.myProfileJs.maxFileSize = "{!! trans('admin::messages.max-file-size') !!}";
        siteObjJs.admin.myProfileJs.mimes = "{!! trans('admin::messages.mimes') !!}";
    });
</script>
@stop

@section('content')
<div class="page-head">
    <div class="page-title">
        <h1>My Profile</h1>
    </div>
</div>
<ul class="page-breadcrumb breadcrumb">
    <li><a href="{!! URL::to('/admin') !!}">{!! trans('admin::controller/myprofile.admin') !!}</a><i class="fa fa-circle"></i></li>
    <li><span class="text-muted">{!! trans('admin::controller/myprofile.my-profile') !!}</span></li>
</ul>

<div id="ajax-response-text">

</div>


<div class="row">
    <div class="col-md-12">
        <div class="profile-sidebar" style="width:250px;" id="username-avatar">
            @include('admin::myprofile.username_avatar')
        </div>

        <div class="profile-content profile-form-container">
            <div class="row">
                <div class="col-md-12">
                    <div class="portlet light">
                        <div class="portlet-title tabbable-line">
                            <div class="caption caption-md">
                                <i class="icon-globe theme-font hide"></i>
                                <span class="caption-subject font-blue-madison bold uppercase">{!! trans('admin::controller/myprofile.profile-account') !!}</span>
                            </div>
                            <ul class="nav nav-tabs">
                                <li class="tab personal-info active">
                                    <a href="#personal-info" data-toggle="tab">{!! trans('admin::controller/myprofile.personal-info') !!}</a>
                                </li>
                                <li class="tab change-avatar">
                                    <a href="#change-avatar" data-toggle="tab">{!! trans('admin::controller/myprofile.change-picture') !!}</a>
                                </li>
                                <li class="tab change-password">
                                    <a href="#change-password" data-toggle="tab">{!! trans('admin::controller/myprofile.change-password') !!}</a>
                                </li>
                            </ul>
                        </div>
                        <div class="portlet-body form-body">
                            <div class="tab-content">
                                <!-- PERSONAL INFO TAB -->
                                <div class="tab-pane active" id="personal-info">
                                    @include('admin::myprofile.edit_info')
                                </div>
                                <!-- END PERSONAL INFO TAB -->
                                <!-- CHANGE AVATAR TAB -->
                                <div class="tab-pane" id="change-avatar">
                                    @include('admin::myprofile.change_picture')
                                </div>
                                <!-- END CHANGE AVATAR TAB -->
                                <!-- CHANGE PASSWORD TAB -->
                                <div class="tab-pane" id="change-password">
                                    @include('admin::myprofile.change_password')
                                </div>
                                <!-- END CHANGE PASSWORD TAB -->
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>
@stop