<tr class="child-row-{!! $myRowId !!}">
    <td>
        {!! Form::checkbox('member_diet_plan['.$dietScheduleTypeId.']', 1, true, ['class' => 'member_diet_plan_items', 'id' => 'check_food_'.$dietScheduleTypeId.'']) !!}
    </td>

    <td>
        {!! Form::select('food_type_id['.$dietScheduleTypeId.']', [''=>'Select Food Type'] + $foodTypeList, null,['class'=>'select2me form-control food_type_id',  'data-rule-required'=>'true', 'data-msg-required'=>'Please Select Food Type']) !!}
    </td>

    <td>
        <span class='food_list_by_food_type' id="new_food_list_{!! $dietScheduleTypeId !!}">
            {!! Form::select('food_id['.$dietScheduleTypeId.']', [''=>'Select Food'] , null,['class'=>'select2me form-control select-new-food food_id',  'data-rule-required'=>'true', 'data-msg-required'=>'Please Select Food']) !!}
        </span>
        <span class="help-block">Search here or select from list</span>
    </td>

    {!! Form::hidden('diet_schedule_type_id['.$dietScheduleTypeId.']', $dietScheduleTypeId, array('class' => 'diet_schedule_type_id')) !!}

    {!! Form::hidden('diet_plan_row_id['.$dietScheduleTypeId.']', $dietScheduleTypeId, array('class' => 'unique_diet_plan_id')) !!}
    <td>
        {!! Form::text('servings_recommended['.$dietScheduleTypeId.']', null, ['class'=>'form-control servings_recommended unit_servings_'.$dietScheduleTypeId.'', 'maxlength' =>5, 'minlength'=>1, 'data-rule-required'=>'true', 'data-msg-required'=>trans('admin::messages.required-enter', ['name' => 'Servings Recommended']) , 'data-msg-minlength'=>trans('admin::messages.error-minlength', ['name' => 'Servings Recommended'])])!!}
        {!! Form::hidden('calories['.$dietScheduleTypeId.']', null, array('class' => 'unit_calories unit_calories_'.$dietScheduleTypeId.'')) !!}
    </td>
    <td class="measure"> </td>
    <td class="calories"> </td>
    <td> <span class='total_calories'></span> </td>
    <td  style="width:100px">
        <a href='javascript:;' class="btn default close-food-row-btn remove-row-{!! $myRowId !!}">Cancel</a>
    </td>
</tr>
