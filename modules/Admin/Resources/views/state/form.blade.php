<div class="form-body">
    <div class="form-group">
        <label class="col-md-3 control-label">Country<span class="required" aria-required="true">*</span></label>
        <div class="col-md-4">
            {!! Form::select('country_id', [''=>'Select Country'] + $countryList, null,['class'=>'select2me form-control', 'id' => 'country_id', 'data-rule-required'=>'true', 'data-msg-required'=>trans('admin::messages.required-enter', ['name' => trans('admin::controller/state.country')])]) !!}
            <span class="help-block">Select name of the country.</span>
        </div>
    </div>
    <div class="form-group">
        <label class="col-md-3 control-label">State Name <span class="required" aria-required="true">*</span></label>
        <div class="col-md-4">
            {!! Form::text('name', null, ['class'=>'form-control', 'maxlength' => 255, 'minlength'=>2,'id'=>'state_name', 'data-rule-required'=>'true', 'data-msg-required'=>trans('admin::messages.required-enter', ['name' => trans('admin::controller/state.name')]) , 'data-msg-minlength'=>trans('admin::messages.error-minlength', ['name' => trans('admin::controller/state.name')]) ])!!}
        </div>
    </div>
    <div class="form-group">
        <label class="col-md-3 control-label">State Code <span class="required" aria-required="true">*</span></label>
        <div class="col-md-4">
            {!! Form::text('state_code', null, ['class'=>'form-control upper input-inline input-small', 'maxlength' => 2, 'id'=>'state_code', 'data-rule-required'=>'true', 'data-msg-required'=>trans('admin::messages.required-enter', ['name' => trans('admin::controller/state.code')])]) !!}
            <span class="help-block">State Code.</span>
        </div>
    </div>

    <div class="form-group">
        <label class="col-md-3 control-label">Status </label>
        <div class="col-md-4">
            <div class="radio-list">
                <label class="radio-inline">{!! Form::radio('status', '1', true) !!}  {!! trans('admin::messages.active') !!}</label>
                <label class="radio-inline">{!! Form::radio('status', '0') !!}  {!! trans('admin::messages.inactive') !!}</label>
            </div>
        </div>
    </div>
</div>