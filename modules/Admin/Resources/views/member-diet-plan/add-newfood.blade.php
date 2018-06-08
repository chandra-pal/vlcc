<td>
    {!! Form::checkbox('member_diet_plan['.$maxDietPlanRowId.']', 1, true, ['class' => 'member_diet_plan_items', 'id' => 'check_food_'.$maxDietPlanRowId.'']) !!}
</td>    
 
<td>
    {!! Form::select('food_type_id['.$maxDietPlanRowId.']', [''=>'Select Food Type'] + $foodTypeList, $selected_food_type_id,['class'=>'select2me form-control food_type_id',  'data-rule-required'=>'true', 'data-msg-required'=>'Please Select Food Type']) !!}</td>
    
<td> 
{!! Form::text('food_name['.$maxDietPlanRowId.']', null, ['class'=>'form-control custom-new-food food_name', 'maxlength' => 50, 'minlength'=>2, 'data-rule-required'=>'true', 'data-msg-required'=>trans('admin::messages.required-enter', ['name' => 'Food Name']) , 'data-msg-minlength'=>trans('admin::messages.error-minlength', ['name' => 'Food Name'])])!!}
</td>    

<td>
    {!! Form::text('servings_recommended['.$maxDietPlanRowId.']', "1", ['class'=>'form-control servings_recommended unit_servings_'.$maxDietPlanRowId.'', 'maxlength' =>5, 'minlength'=>1, 'data-rule-required'=>'true', 'data-msg-required'=>trans('admin::messages.required-enter', ['name' => 'Servings Recommended']) , 'data-msg-minlength'=>trans('admin::messages.error-minlength', ['name' => 'Servings Recommended'])])!!}
</td>  

<td>
    {!! Form::text('measure['.$maxDietPlanRowId.']', null, ['class'=>'form-control measure', 'maxlength' =>50, 'minlength'=>1, 'data-rule-required'=>'true', 'data-msg-required'=>trans('admin::messages.required-enter', ['name' => 'Measure']) , 'data-msg-minlength'=>trans('admin::messages.error-minlength', ['name' => 'Measure'])])!!}
</td>  

<td>
    {!! Form::text('calories['.$maxDietPlanRowId.']', null, ['class'=>'form-control calories unit_calories unit_calories_'.$maxDietPlanRowId.'', 'maxlength' =>5, 'minlength'=>1, 'data-rule-required'=>'true', 'data-msg-required'=>trans('admin::messages.required-enter', ['name' => 'Calories']) , 'data-msg-minlength'=>trans('admin::messages.error-minlength', ['name' => 'Calories'])])!!}
</td> 

<td> <span class='total_calories'></span> </td>
    

<!--<td>
    {!! Form::text('serving_size['.$maxDietPlanRowId.']', null, ['class'=>'form-control serving_size', 'maxlength' =>5, 'minlength'=>1, 'data-rule-required'=>'true', 'data-msg-required'=>trans('admin::messages.required-enter', ['name' => 'Serving Size']) , 'data-msg-minlength'=>trans('admin::messages.error-minlength', ['name' => 'Serving Size'])])!!}
</td>-->

<!--<td>
    {!! Form::text('serving_unit['.$maxDietPlanRowId.']', null, ['class'=>'form-control serving_unit', 'maxlength' =>20, 'minlength'=>2, 'data-rule-required'=>'true', 'data-msg-required'=>trans('admin::messages.required-enter', ['name' => 'Serving Unit']) , 'data-msg-minlength'=>trans('admin::messages.error-minlength', ['name' => 'Serving Unit'])])!!}
</td>    -->
    
    {!! Form::hidden('created_by', $dieticianId, array('class' => 'created_by'))  !!}
    
    {!! Form::hidden('diet_schedule_type_id['.$maxDietPlanRowId.']', $dietScheduleTypeId, array('class' => 'diet_schedule_type_id')) !!}
    {!! Form::hidden('food_id['.$maxDietPlanRowId.']', $foodId, array('class' => 'food_id')) !!}   
    
    {!! Form::hidden('diet_plan_row_id['.$maxDietPlanRowId.']', $maxDietPlanRowId, array('class' => 'unique_diet_plan_id')) !!}
<td style="width:100px">
    <a class="add-new-food btn green" style="padding: 8px;"><i class="fa fa-check"></i></a> 
    <a href='javascript:;' class="btn red default close-food-row-btn" style='padding: 8px;'><i class="fa fa-times"></i></a>
</td>
