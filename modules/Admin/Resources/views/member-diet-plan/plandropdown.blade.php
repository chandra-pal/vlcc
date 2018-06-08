<label class="col-md-3 control-label">{!! trans('admin::controller/member-diet-plan.select-plan') !!} <span class="required" aria-required="true">*</span></label>
<div class="col-md-4" id='plan-listing-content'>
    {!! Form::select('diet_plan_id', [''=>trans('admin::controller/member-diet-plan.select-plan')] + $dietPlanTypeList, $dietPlanId,['class'=>'select2me form-control form-filter', 'id' => 'diet_plan_id', 'data-rule-required'=>'true', 'data-msg-required'=>'Please select Diet Plan.']) !!}
    <span class="help-block help-block-error"></span>
</div>