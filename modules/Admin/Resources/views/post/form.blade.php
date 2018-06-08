<div class="form-body">
    <div class="form-group">
        <label class="col-md-3 control-label">Post Name <span class="required" aria-required="true">*</span></label>
        <div class="col-md-4">
            {!! Form::text('name', null, ['maxlength'=>'200', 'class'=>'form-control', 'data-rule-required'=>'true', 'data-msg-required'=>trans('admin::messages.required-enter', ['name' => trans('admin::controller/post.name')]), 'minlength'=>2, 'data-msg-minlength'=>trans('admin::messages.error-minlength', ['name' => trans('admin::controller/post.name')]) ])!!}
        </div>
    </div>
    <div class="form-group">
        <label class="col-md-3 control-label">Description<span class="required" aria-required="true">*</span></label>
        <div class="col-md-4">
            {!! Form::textarea('description', null, ['minlength'=>2,'size' => '30x3','class'=>'form-control', 'data-rule-required'=>'true', 'data-msg-required'=>'Please enter description.','data-msg-minlength'=>trans('admin::messages.error-minlength', ['name' => trans('admin::controller/post.description')]) ])!!}
            <span class="help-block">Post Description.</span>
        </div>
    </div>
    <div class="form-group">
        <label class="col-md-3 control-label">Status </label>
        <div class="col-md-4">
            <div class="radio-list">
                <label class="radio-inline">{!! Form::radio('status', '1', true) !!} {!! trans('admin::messages.active') !!}</label>
                <label class="radio-inline">{!! Form::radio('status', '0') !!} {!! trans('admin::messages.inactive') !!}</label>
            </div>
        </div>
    </div>
</div>