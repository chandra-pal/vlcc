<div class="row">
    <div class="col-md-6">
        <div class="form-group">
            <label class="control-label col-md-4">Group Name<span class="required" aria-required="true">*</span></label>
            <div class="col-md-8">
                {!! Form::select('menu_group_id',[''=>'Select Menu Group Name'] +$menuGroupNames, null,['class'=>'select2me form-control', 'id' => 'menu_group_id', 'data-rule-required'=>'true', 'data-msg-required'=>trans('admin::messages.required-select', ['name' => trans('admin::controller/link-category.menu_group')])]) !!}
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="form-group">
            <label class="control-label col-md-4">Category Name<span class="required" aria-required="true">*</span></label>
            <div class="col-md-8">
                {!! Form::text('category', null, ['class'=>'form-control','id'=>'category', 'maxlength'=>50 ,'minlength'=>2 ,'data-rule-required'=>'true', 'data-msg-required'=>trans('admin::messages.required-enter', ['name' => trans('admin::controller/link-category.category')]), 'data-rule-maxlength'=>'50', 'data-msg-maxlength'=>trans('admin::messages.error-maxlength', ['name'=>trans('admin::controller/link-category.category')]) , 'data-msg-minlength'=>trans('admin::messages.error-minlength', ['name' => trans('admin::controller/link-category.category')]) ])!!}
                <span class="help-block">E.g. Link Management, Admin User Management</span>
            </div>
        </div>
    </div>

</div>
<div class="row">
    <div class="col-md-6">
        <div class="form-group">
            <label class="col-md-4 control-label">Category Icon<span class="required" aria-required="true">*</span></label>
            <div class="col-md-8">
                {!! Form::text('category_icon', null, ['minlength'=>2,'maxlength'=>50,'class'=>'form-control','id'=>'category_icon', 'data-rule-required'=>'true', 'data-msg-required'=>trans('admin::messages.required-select', ['name' => trans('admin::controller/link-category.category_icon')]) , 'data-msg-minlength'=>trans('admin::messages.error-minlength', ['name' => trans('admin::controller/link-category.category_icon')]) ])!!}
                {{--*/ $categoryIcon = ($action=='update')?$linkCategory->category_icon:''; /*--}}
                <p id="showLinkIconsPopup">Selected Icon: &nbsp;&nbsp;<span class="category-icon"><i class="{{$categoryIcon}}"></i></span></p>
                <span class="help-block">Bootstrap icon tag to display before the category name.</span>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="form-group">
            <label class="control-label col-md-4">Category Description <span class="required" aria-required="true">*</span></label>
            <div class="col-md-8">
                {!! Form::textArea('header_text', null, ['minlength'=>2,'class'=>'form-control','rows'=>3,'id'=>'header_text', 'maxlength'=>255, 'data-rule-required'=>'true', 'data-msg-required'=>trans('admin::messages.required-enter', ['name' => trans('admin::controller/link-category.header_text')]), 'data-rule-maxlength'=>'255', 'data-msg-maxlength'=>trans('admin::messages.error-maxlength', ['name'=>trans('admin::controller/link-category.header_text')]) , 'data-msg-minlength'=>trans('admin::messages.error-minlength', ['name' => trans('admin::controller/link-category.header_text')]) ])!!}
                <span class="help-block pull-right">Maximum length is 255 characters.</span>
            </div>
        </div>
    </div>

</div>
<div class="row">
    <div class="col-md-6">
        <div class="form-group">
            <label class="control-label col-md-4">Display Order <span class="required" aria-required="true">*</span></label>
            <div class="col-md-8">
                {!! Form::text('position', null, ['class'=>'form-control','id'=>'position','data-rule-required'=>'true', 'data-msg-required'=>trans('admin::messages.required-enter', ['name' => trans('admin::controller/link-category.position')]), 'data-rule-maxlength'=>'10', 'data-msg-maxlength'=>trans('admin::messages.error-maxlength', ['name'=>trans('admin::controller/link-category.position')]), 'data-rule-number'=>'10', 'data-msg-number'=>'Please enter numbers only.']) !!}
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="form-group">
            <label class="col-md-4 control-label">Status<span class="required" aria-required="true">*</span></label>
            <div class="col-md-8">
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
<div id="showCategoryIcons" class="modal fade" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                <h4 class="modal-title">Select Category Icon</h4>
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
