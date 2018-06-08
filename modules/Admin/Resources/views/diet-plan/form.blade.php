<div class="form-body">
    <div class="form-group">
        <label class="col-md-3 control-label">{!! trans('admin::controller/diet-plan.name') !!}<span class="required" aria-required="true">*</span></label>
        <div class="col-md-4">
            {!! Form::text('plan_name', null, ['minlength'=>2,'maxlength'=>20,'class'=>'form-control', 'id'=>'plan_name', 'data-rule-required'=>'true', 'data-msg-required'=>trans('admin::messages.required-enter', ['name' => trans('admin::controller/diet-plan.name')]), 'data-rule-maxlength'=>'20', 'data-msg-maxlength'=>trans('admin::messages.error-maxlength', ['name'=>trans('admin::controller/diet-plan.name')]) , 'data-msg-minlength'=>trans('admin::messages.error-minlength', ['name' => trans('admin::controller/diet-plan.name')]) ])!!}
            <span class="help-block">eg: Balanced Diet Plan</span>
        </div>
    </div>
    <div class="form-group">
        <label class="control-label col-md-3">{!! trans('admin::controller/diet-plan.plan-type') !!}<span class="required" aria-required="true">*</span></label>
        <div class="col-md-4">
            {!! Form::select('plan_type', [''=> trans('admin::messages.select-name', [ 'name' => trans('admin::controller/diet-plan.plan-type') ])] + $dietType, null,['class'=>'select2me form-control', 'id' => 'plan_type', 'data-rule-required'=>'true', 'data-msg-required'=>'Please select Plan Type.']) !!}
        </div>
    </div>
    <div class="form-group">
        <label class="col-md-3 control-label">{!! trans('admin::controller/diet-plan.calories') !!}<span class="required" aria-required="true">*</span></label>
        <div class="col-md-4">
            {!! Form::text('calories', null, ['minlength'=>2,'maxlength'=>5,'class'=>'form-control', 'id'=>'position', 'data-rule-number' => '10', 'data-rule-required'=>'true', 'data-msg-number'=>'Please enter numbers only.', 'data-msg-required'=>trans('admin::messages.required-enter', ['name' => trans('admin::controller/diet-plan.calories')]) ]) !!}
            <span class="help-block">eg: 1400</span>
        </div>
    </div>
    <div class="form-group">
        <label class="col-md-3 control-label">{!! trans('admin::controller/diet-plan.status') !!}<span class="required" aria-required="true">*</span> </label>
        <div class="col-md-4">
            <div class="radio-list">
                <label class="radio-inline">{!! Form::radio('status', '1', true) !!} {!! trans('admin::messages.active') !!}</label>
                <label class="radio-inline">{!! Form::radio('status', '0') !!} {!! trans('admin::messages.inactive') !!}</label>
            </div>
        </div>
    </div>
</div>