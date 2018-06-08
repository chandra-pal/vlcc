<div class="form-body">
    <div class="form-group">
        <label class="control-label col-md-3">{!! trans('admin::controller/diet-plan-detail.diet-plan-type') !!}<span class="required" aria-required="true">*</span></label>
        <div class="col-md-4">
            {!! Form::select('diet_plan_id', [''=> trans('admin::messages.select-name', [ 'name' => trans('admin::controller/diet-plan-detail.diet-plan-type') ])] + $dietPlanTypeList, null,['class'=>'select2me form-control', 'id' => 'diet_plan_id', 'data-rule-required'=>'true', 'data-msg-required'=>'Please select Diet Plan Type.']) !!}
        </div>
    </div>
    <div class="form-group">
        <label class="control-label col-md-3">{!! trans('admin::controller/diet-plan-detail.diet-schedule-type') !!}<span class="required" aria-required="true">*</span></label>
        <div class="col-md-4">
            {!! Form::select('diet_schedule_type_id', [''=> trans('admin::messages.select-name', [ 'name' => trans('admin::controller/diet-plan-detail.diet-schedule-type') ])] + $dietScheduleType, null,['class'=>'select2me form-control', 'id' => 'diet_schedule_type_id', 'data-rule-required'=>'true', 'data-msg-required'=>'Please select Diet Schedule Type.']) !!}
        </div>
    </div>

    <div class="form-group">
        <label class="control-label col-md-3">{!! trans('admin::controller/diet-plan-detail.food-type-list') !!}<span class="required" aria-required="true">*</span></label>
        <div class="col-md-4">
            {!! Form::select('food_type_id', [''=> trans('admin::messages.select-name', [ 'name' => trans('admin::controller/diet-plan-detail.food-type-list') ])] + $foodTypeLists, $selectedFoodTypeId,['class'=>'select2me form-control', 'id' => 'food_type_id', 'data-rule-required'=>'true', 'data-msg-required'=>'Please select Food Type.']) !!}
        </div>
    </div>

    <div class="form-group">
        <label class="control-label col-md-3">{!! trans('admin::controller/diet-plan-detail.food-type') !!}<span class="required" aria-required="true">*</span></label>
        <div class="col-md-4" id="food-list-container">
            @include('admin::diet-plan-detail.dropdown')
        </div>
    </div>
    <div class="form-group">
        <label class="col-md-3 control-label">{!! trans('admin::controller/diet-plan-detail.serving-recommended') !!}<span class="required" aria-required="true">*</span></label>
        <div class="col-md-4">
            {!! Form::text('servings_recommended', null, ['minlength'=>1,'maxlength'=>2,'class'=>'form-control', 'id'=>'servings_recommended', 'data-rule-number' => '10', 'data-rule-required'=>'true', 'data-msg-number'=>'Please enter numbers only.', 'data-msg-required'=>trans('admin::messages.required-enter', ['name' => trans('admin::controller/diet-plan-detail.serving-recommended')]) ]) !!}
        </div>
    </div>
    <!--    <div class="form-group">
            <label class="col-md-3 control-label">{!! trans('admin::controller/diet-plan-detail.status') !!}<span class="required" aria-required="true">*</span> </label>
            <div class="col-md-4">
                <div class="radio-list">
                    <label class="radio-inline">{!! Form::radio('status', '1', true) !!} {!! trans('admin::messages.active') !!}</label>
                    <label class="radio-inline">{!! Form::radio('status', '0') !!} {!! trans('admin::messages.inactive') !!}</label>
                </div>
            </div>
        </div>-->
</div>