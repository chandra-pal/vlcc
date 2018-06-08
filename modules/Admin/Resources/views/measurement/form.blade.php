<div class="form-body">
    <div class="form-group">
        <label class="col-md-3 control-label">{!! trans('admin::controller/measurement.measurement-title') !!}<span class="required" aria-required="true">*</span></label>
        <div class="col-md-4">
            {!! Form::text('title', null, ['minlength'=>2,'maxlength'=>50,'class'=>'form-control', 'id'=>'title', 'data-rule-required'=>'true', 'data-msg-required'=>trans('admin::messages.required-enter', ['name' => trans('admin::controller/measurement.measurement-title')]), 'data-rule-maxlength'=>'50', 'data-msg-maxlength'=>trans('admin::messages.error-maxlength', ['name'=>trans('admin::controller/measurement.title')]) , 'data-msg-minlength'=>trans('admin::messages.error-minlength', ['name' => trans('admin::controller/measurement.title')]) ])!!}
            <span class="help-block">eg: 1 Tbsp</span>
        </div>
    </div>
    <div class="form-group">
        <label class="col-md-3 control-label">{!! trans('admin::controller/measurement.meaning') !!}<span class="required" aria-required="true">*</span></label>
        <div class="col-md-4">
            {!! Form::text('meaning', null, ['minlength'=>2,'maxlength'=>20,'class'=>'form-control', 'id'=>'meaning', 'data-rule-required'=>'true', 'data-msg-required'=>trans('admin::messages.required-enter', ['name' => trans('admin::controller/measurement.meaning')]), 'data-rule-maxlength'=>'20', 'data-msg-maxlength'=>trans('admin::messages.error-maxlength', ['name'=>trans('admin::controller/measurement.meaning')]) , 'data-msg-minlength'=>trans('admin::messages.error-minlength', ['name' => trans('admin::controller/measurement.meaning')]) ])!!}
            <span class="help-block">eg: 5 g / 5 ml</span>
        </div>
    </div>
    <div class="form-group">
        <label class="col-md-3 control-label">{!! trans('admin::controller/measurement.status') !!}<span class="required" aria-required="true">*</span> </label>
        <div class="col-md-4">
            <div class="radio-list">
                <label class="radio-inline">{!! Form::radio('status', '1', true) !!} {!! trans('admin::messages.active') !!}</label>
                <label class="radio-inline">{!! Form::radio('status', '0') !!} {!! trans('admin::messages.inactive') !!}</label>
            </div>
        </div>
    </div>
</div>