<div class="form-body">
    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                <label class="control-label col-md-4">{!! trans('admin::controller/config-category.name') !!}<span class="required" aria-required="true">*</span></label>
                <div class="col-md-8">
                    {!! Form::text('category', null, ['minlength'=>2,'maxlength'=>50,'class'=>'form-control', 'id'=>'category', 'data-rule-required'=>'true', 'data-msg-required'=> trans('admin::messages.required-enter', ['name' => trans('admin::controller/config-category.name') ] ), 'data-rule-maxlength'=>'50', 'data-msg-maxlength'=>'Category name can not have more than {0} letters.', 'data-msg-minlength'=>trans('admin::messages.error-minlength', ['name' => trans('admin::controller/config-category.name')]) ])!!}
                    {!! Form::hidden('position', null, ['class'=>'form-control', 'id'=>'position']) !!}
                    <span class="help-block">{!! trans('admin::controller/config-category.name-help') !!}</span>
                </div>
            </div>
        </div>
    </div>
</div>