<div class="row">
    <div class="col-md-6">
        <div class="form-group">
            <label class="control-label col-md-4">Category Name<span class="required" aria-required="true">*</span></label>
            <div class="col-md-8">
                {!! Form::select('link_category_id',[''=>'Select Category Name'] +$categoryNames, null,['class'=>'select2me form-control', 'id' => 'link_category_id', 'data-rule-required'=>'true', 'data-msg-required'=>trans('admin::messages.required-select', ['name' => trans('admin::controller/links.category')])]) !!}            
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="form-group">
            <label class="control-label col-md-3">Page Name <span class="required" aria-required="true">*</span></label>
            <div class="col-md-9">

                {!! Form::text('link_url', null, ['minlength'=>2,'class'=>'form-control','id'=>'link_url', 'maxlength'=>100 ,'data-rule-required'=>'true', 'data-msg-required'=>trans('admin::messages.required-enter', ['name' => trans('admin::controller/links.link_url')]), 'data-rule-maxlength'=>'100', 'data-msg-maxlength'=>trans('admin::messages.error-maxlength', ['name'=>trans('admin::controller/links.link_url')]) , 'data-msg-minlength'=>trans('admin::messages.error-minlength', ['name' => trans('admin::controller/links.link_url')]) ])!!}

                <span class="help-block">Name to uniquely identify the page. Please use route alias. Eg. admin.user.index etc.</span>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-6">
        <div class="form-group">
            <label class="control-label col-md-4">Link Display Name <span class="required" aria-required="true">*</span></label>
            <div class="col-md-8">

                {!! Form::text('link_name',null, ['minlength'=>2,'class'=>'form-control','id'=>'link_name', 'maxlength'=>50 ,'data-rule-required'=>'true', 'data-msg-required'=>trans('admin::messages.required-enter', ['name' => trans('admin::controller/links.link_name')]), 'data-rule-maxlength'=>'100', 'data-msg-maxlength'=>trans('admin::messages.error-maxlength', ['name'=>trans('admin::controller/links.link_name')]) , 'data-msg-minlength'=>trans('admin::messages.error-minlength', ['name' => trans('admin::controller/links.link_name')]) ])!!}
                <span class="help-block">The link name to appear on the left hand menu under each category. Eg. Manage Admin Users, Manage IP Addresses etc.</span>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="form-group">
            <label class="col-md-3 control-label">Link Icon<span class="required" aria-required="true">*</span></label>
            <div class="col-md-9">
                {!! Form::text('link_icon', null, ['minlength'=>2, 'class'=>'form-control','id'=>'link_icon', 'maxlength'=>50 ,'data-rule-required'=>'true', 'data-msg-required'=>trans('admin::messages.required-select', ['name' => trans('admin::controller/links.link_icon')]), 'data-rule-maxlength'=>'50', 'data-msg-maxlength'=>trans('admin::messages.error-maxlength', ['name'=>trans('admin::controller/links.link_icon')]) , 'data-msg-minlength'=>trans('admin::messages.error-minlength', ['name' => trans('admin::controller/links.link_icon')]) ])!!}

                {{--*/ $linkIcon = ($action=='update')?$links->link_icon:''; /*--}}
                <p id="showLinkIconsPopup">Selected Icon: &nbsp;&nbsp;<span class="link-icon"><i class="{{$linkIcon}}"></i></span></p>
                <span class="help-block">Bootstrap icon tag to display before the link name.</span>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-6">
        <div class="form-group">
            <label class="control-label col-md-4">Page Title <span class="required" aria-required="true">*</span></label>
            <div class="col-md-8">
                {!! Form::text('page_header', null, ['minlength'=>2,'class'=>'form-control','id'=>'page_header', 'maxlength'=>100 ,'data-rule-required'=>'true', 'data-msg-required'=>trans('admin::messages.required-enter', ['name' => trans('admin::controller/links.page_header')]), 'data-rule-maxlength'=>'100','data-msg-maxlength'=>trans('admin::messages.error-maxlength', ['name'=>trans('admin::controller/links.page_header')]) , 'data-msg-minlength'=>trans('admin::messages.error-minlength', ['name' => trans('admin::controller/links.page_header')]) ])!!}
                <span class="help-block">The title to appear above the bread crumbs when the page loads.</span>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="form-group">
            <label class="control-label col-md-3">Page Description</label>
            <div class="col-md-9">
                {!! Form::textArea('page_text', null, ['minlength'=>2, 'rows'=>5, 'cols'=>50, 'class'=>'form-control','id'=>'page_text' , 'data-msg-minlength'=>trans('admin::messages.error-minlength', ['name' => trans('admin::controller/links.page_text')]) ])!!}
                <span class="help-block">Brief description explaining what this page does.</span>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-6">
        <div class="form-group">
            <label class="col-md-4 control-label">Assign Links to User Types<span class="required" aria-required="true">*</span></label>
            <div class="col-md-8">
                {!! Form::select('links_assign[]',[''=>'Select Links to User Types'] +$userTypes, $selectedUserTypes,['multiple'=>'multiple','class'=>'select2me form-control', 'id' => 'links_assign', 'data-rule-required'=>'true', 'data-msg-required'=>trans('admin::messages.required-select', ['name' => trans('admin::controller/links.links_assign')])]) !!}                            
                <span class="help-block">Assign links to User Types.</span>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="form-group">
            <label class="control-label col-md-3">Records Per Page<span class="required" aria-required="true">*</span></label>
            <div class="col-md-9">
                {!! Form::select('pagination', $paginationArray, null,['class'=>'select2me form-control', 'id' => 'pagination', 'data-rule-required'=>'true', 'data-msg-required'=>trans('admin::messages.required-select', ['name' => trans('admin::controller/links.pagination')])]) !!}
                <span class="help-block">The number of records to display by default for pagination.</span>
            </div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-md-6">
        <div class="form-group">
            <label class="control-label col-md-4">Display Order <span class="required" aria-required="true">*</span></label>
            <div class="col-md-8">
                {!! Form::text('position', null, ['class'=>'form-control','id'=>'position','data-rule-required'=>'true', 'data-msg-required'=>trans('admin::messages.required-enter', ['name' => trans('admin::controller/links.position')]), 'data-rule-maxlength'=>'10', 'data-msg-maxlength'=>trans('admin::messages.error-maxlength', ['name'=>trans('admin::controller/link-category.position')]), 'data-rule-number'=>'10', 'data-msg-number'=>'Please enter numbers only.']) !!}
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="form-group">
            <label class="col-md-3 control-label">Status<span class="required" aria-required="true">*</span></label>
            <div class="col-md-9">
                @if($action === 'create')
                {!! Form::radio('status', '1', true) !!} {!! trans('admin::messages.active') !!}
                @else
                {!! Form::radio('status', '1') !!} {!! trans('admin::messages.active') !!}
                @endif
                {!! Form::radio('status', '0') !!} {!! trans('admin::messages.inactive') !!}
            </div>
        </div>
    </div>
</div>
<div id="showLinkIcons" class="modal fade" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                <h4 class="modal-title">Select Link Icon</h4>
            </div>
            <div class="modal-body">
                @include('admin::link-category.categoryicons')
            </div>
            <div class="modal-footer">
                <button type="button" data-dismiss="modal" class="btn default">Close</button>
            </div>
        </div>
    </div>
</div>
