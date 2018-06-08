<div class="form-body">
    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                <label class="col-md-4 control-label">{!! trans('admin::controller/config-setting.config-cat') !!}<span class="required" aria-required="true">*</span></label>
                <div class="col-md-8">
                    {!! Form::select('config_category_id', [''=> trans('admin::messages.select-name', ['name' => trans('admin::controller/config-setting.config-cat')])] + $categoryList, null,['class'=>'select2me form-control', 'id' => 'config_category_id', 'data-rule-required'=>'true', 'data-msg-required'=> trans('admin::messages.required-select', ['name' => trans('admin::controller/config-category.config-cat') ]) ]) !!}
                    <span class="help-block">{!! trans('admin::controller/config-setting.name-help') !!}</span>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label class="control-label col-md-5">{!! trans('admin::controller/config-setting.label-desc') !!}<span class="required" aria-required="true">*</span></label>
                <div class="col-md-7">
                    {!! Form::text('description', null, ['minlength'=>2,'maxlength'=>255,'class'=>'form-control', 'data-rule-required'=>'true', 'data-msg-required'=> trans('admin::messages.required-enter', ['name' => trans('admin::controller/config-setting.label-desc') ]), 'data-rule-maxlength'=>'255', 'data-msg-maxlength'=> trans('admin::messages.error-maxlength', ['name' => trans('admin::controller/config-setting.label-desc')]) , 'data-msg-minlength'=>trans('admin::messages.error-minlength', ['name' => trans('admin::controller/config-setting.label-desc')]) ])!!}
                    <span class="help-block">{!! trans('admin::controller/config-setting.label-desc-help') !!}</span>

                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                <label class="control-label col-md-4">{!! trans('admin::controller/config-setting.const-name') !!}<span class="required" aria-required="true">*</span></label>
                <div class="col-md-8">
                    {!! Form::text('config_constant', null, ['minlength'=>2,'maxlength'=>100,'class'=>'form-control', 'data-rule-required'=>'true', 'data-msg-required'=> trans('admin::messages.required-enter', ['name' => trans('admin::controller/config-setting.const-name') ]), 'data-rule-maxlength'=>'100', 'data-msg-maxlength'=> trans('admin::messages.error-maxlength', ['name' => trans('admin::controller/config-setting.const-name')]) , 'data-msg-minlength'=>trans('admin::messages.error-minlength', ['name' => trans('admin::controller/config-setting.const-name')]) ])!!}
                    <span class="help-block">{!! trans('admin::controller/config-setting.const-name-help') !!}</span>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label class="control-label col-md-5">{!! trans('admin::controller/config-setting.const-value') !!}<span class="required" aria-required="true">*</span></label>
                <div class="col-md-7">
                    {!! Form::textarea('config_value', null, ['size' => '30x2','class'=>'form-control text-noresize', 'data-rule-required'=>'true', 'data-msg-required'=> trans('admin::messages.required-enter', ['name' => trans('admin::controller/config-setting.const-value')]) ])!!}
                    <span class="help-block">{!! trans('admin::controller/config-setting.const-value-help') !!}</span>
                </div>
            </div>
        </div>
    </div>
</div>
