<div class="form-body">
    <div class="form-group">
        <label class="col-md-3 control-label">{!! trans('admin::controller/food.food-type') !!}<span class="required" aria-required="true">*</span></label>
        <div class="col-md-4">
            {!! Form::select('food_type_id', [''=>'Select Food Type'] + $foodTypeList, null,['class'=>'select2me form-control', 'id' => 'food_type_id', 'data-rule-required'=>'true', 'data-msg-required'=>trans('admin::messages.required-select', ['name' => trans('admin::controller/food.food-type')])]) !!}
            <span class="help-block">eg: Maharashtrian</span>
        </div>
    </div>
    <div class="form-group">
        <label class="col-md-3 control-label">{!! trans('admin::controller/food.name') !!}<span class="required" aria-required="true">*</span></label>
        <div class="col-md-4">
            {!! Form::text('food_name', null, ['minlength'=>2,'maxlength'=>50,'class'=>'form-control', 'id'=>'name', 'data-rule-required'=>'true', 'data-msg-required'=>trans('admin::messages.required-enter', ['name' => trans('admin::controller/food.name')]), 'data-rule-maxlength'=>'50', 'data-msg-maxlength'=>trans('admin::messages.error-maxlength', ['name'=>trans('admin::controller/food.name')]) , 'data-msg-minlength'=>trans('admin::messages.error-minlength', ['name' => trans('admin::controller/food.name')]) ])!!}
            <span class="help-block">eg: Ice cream</span>
        </div>
    </div>
    <div class="form-group">
        <label class="col-md-3 control-label">{!! trans('admin::controller/food.measure') !!}<span class="required" aria-required="true">*</span></label>
        <div class="col-md-4">
            {!! Form::text('measure', null, ['minlength'=>1,'maxlength'=>50,'class'=>'form-control', 'id'=>'measure', 'data-rule-required'=>'true', 'data-msg-required'=>trans('admin::messages.required-enter', ['name' => trans('admin::controller/food.measure')]), 'data-rule-maxlength'=>'50', 'data-msg-maxlength'=>trans('admin::messages.error-maxlength', ['name'=>trans('admin::controller/food.measure')]) , 'data-msg-minlength'=>trans('admin::messages.error-minlength', ['name' => trans('admin::controller/food.measure')]) ])!!}
            <span class="help-block">eg: 1 scoop</span>
        </div>
    </div>
    <div class="form-group">
        <label class="col-md-3 control-label">{!! trans('admin::controller/food.calories') !!}<span class="required" aria-required="true">*</span></label>
        <div class="col-md-4">
            {!! Form::text('calories', null, ['minlength'=>1,'maxlength'=>5,'class'=>'form-control', 'id'=>'calories', 'data-rule-number' => '10', 'data-rule-required'=>'true', 'data-msg-number'=>'Please enter valid calories.', 'data-msg-required'=>trans('admin::messages.required-enter', ['name' => trans('admin::controller/food.calories')]) ]) !!}
            <span class="help-block">eg: 500</span>
        </div>
    </div>
    {!! Form::hidden('created_by_user_type', $created_by_user_type) !!}
    
    
<!--    <div class="form-group">
        <label class="col-md-3 control-label">{!! trans('admin::controller/food.serving-size') !!}<span class="required" aria-required="true">*</span></label>
        <div class="col-md-4">
            {!! Form::text('serving_size', null, ['minlength'=>1,'maxlength'=>5, 'class'=>'form-control', 'id'=>'serving_size', 'data-rule-number' => '10', 'data-rule-required'=>'true', 'data-msg-number'=>'Please enter valid serving size.', 'data-msg-required'=>trans('admin::messages.required-enter', ['name' => trans('admin::controller/food.serving-size')]) ]) !!}
            <span class="help-block">eg: 100</span>
        </div>
    </div>-->

<!--    <div class="form-group">
        <label class="col-md-3 control-label">{!! trans('admin::controller/food.serving-unit') !!}<span class="required" aria-required="true">*</span></label>
        <div class="col-md-4">
            {!! Form::text('serving_unit', null, ['minlength'=>2,'maxlength'=>20,'class'=>'form-control', 'id'=>'serving_unit', 'data-rule-required'=>'true', 'data-msg-required'=>trans('admin::messages.required-enter', ['name' => trans('admin::controller/food.serving-unit')]), 'data-rule-maxlength'=>'20', 'data-msg-maxlength'=>trans('admin::messages.error-maxlength', ['name'=>trans('admin::controller/food.serving-unit')]) , 'data-msg-minlength'=>trans('admin::messages.error-minlength', ['name' => trans('admin::controller/food.serving-unit')]) ])!!}
            {!! Form::hidden('status',1) !!}
            {!! Form::hidden('created_by_user_type', $created_by_user_type) !!}
            <span class="help-block">eg: grams</span>
        </div>
    </div>-->
</div>