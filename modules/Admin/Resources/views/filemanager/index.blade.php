@extends('admin::layouts.master')

@section('content')
{{--*/ $linkData = \Modules\Admin\Services\Helper\MenuHelper::getRouteByPage();/*--}}
@if(!empty($linkData))
<div class="page-head">
    <div class="page-title">
        <h1>{{$linkData['page_header']}}</h1>
        <input type="hidden" value="Page_Management" id="menu_name"/>
        <input type="hidden" value="{{str_replace(' ', '_', $linkData['link_name']).'_submenu'}}" id="submenu_name"/>
        <ul class="page-breadcrumb breadcrumb">
            <li><a href="{!! URL::to('/admin') !!}">Admin</a><i class="fa fa-circle"></i></li>
            <li><a href="javascript:;">Page Management</a><i class="fa fa-circle"></i></li>
            <li><a href="javascript:;">{{$linkData['page_header']}}</a></li>
        </ul>
        <h4>{{$linkData['page_text']}}</h4>
    </div>
</div>
@endif
<div class="portlet light col-lg-12 config-settings">
    <div class="portlet-title">
        <div class="caption">
            <i class="fa {{$linkData['link_icon']}} font-blue-sharp"></i>
            <span class="caption-subject font-blue-sharp bold uppercase">File Manager</span>
        </div>
    </div>
    <div class="portlet-body">
        <div class="iframe-responsive-wrapper">
            <img class="iframe-ratio" src="data:image/gif;base64,R0lGODlhEAAJAIAAAP///wAAACH5BAEAAAAALAAAAAAQAAkAAAIKhI+py+0Po5yUFQA7"/>
            <iframe scrolling="no" src="{!! url($url) !!}" style="width:100%; min-height: 450px;" frameborder="0" id="iframe-file">

            </iframe>
        </div>
    </div>
</div>

@stop