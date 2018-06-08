<div class="form-body">
    <div class="form-group">
        <label class="col-md-3 control-label">Country Name <span class="required" aria-required="true">*</span></label>
        <div class="col-md-4">
            {!! Form::text('name', null, ['maxlength'=>'200', 'class'=>'form-control', 'data-rule-required'=>'true', 'data-msg-required'=>trans('admin::messages.required-enter', ['name' => trans('admin::controller/country.name')]), 'minlength'=>2, 'data-msg-minlength'=>trans('admin::messages.error-minlength', ['name' => trans('admin::controller/country.name')]) ])!!}
        </div>
    </div>
    <div class="form-group">
        <label class="col-md-3 control-label">ISO Code 2 <span class="required" aria-required="true">*</span></label>
        <div class="col-md-4">
            {!! Form::text('iso_code_2', null, ['class'=>'form-control', 'data-rule-required'=>'true', 'data-msg-required'=>trans('admin::messages.required-enter', ['name' => trans('admin::controller/country.iso-code-2')]), 'maxlength'=>'2', 'minlength'=>'2', 'data-msg-minlength'=>trans('admin::messages.error-minlength', ['name' => trans('admin::controller/country.iso-code-2')]) ])!!}
            <span class="help-block">2 character ISO Code.</span>
        </div>
    </div>
    <div class="form-group">
        <label class="col-md-3 control-label">ISO Code 3 <span class="required" aria-required="true">*</span></label>
        <div class="col-md-4">
            {!! Form::text('iso_code_3', null, ['class'=>'form-control', 'data-rule-required'=>'true', 'data-msg-required'=>trans('admin::messages.required-enter', ['name' => trans('admin::controller/country.iso-code-3')]), 'maxlength'=>'3', 'minlength'=>'3', 'data-msg-minlength'=>trans('admin::messages.error-minlength', ['name' => trans('admin::controller/country.iso-code-3')]) ])!!}
            <span class="help-block">3 character ISO Code.</span>
        </div>
    </div>
    <div class="form-group">
        <label class="col-md-3 control-label">ISD Code <span class="required" aria-required="true">*</span></label>
        <div class="col-md-4">
            <div class="input-group">
                <span class="input-group-addon">
                    <i class="fa fa-plus"></i>
                </span>
                {!! Form::text('isd_code', null, ['class'=>'form-control', 'maxlength'=>'7', 'data-rule-number' => 'true', 'data-msg-number'=>'Please enter numbers only.', 'data-rule-required'=>'true', 'data-msg-required'=>trans('admin::messages.required-enter', ['name' => trans('admin::controller/country.isd-code')]), 'maxlength'=>'7']) !!}
            </div>
            <span class="help-block">ISD Code.</span>
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