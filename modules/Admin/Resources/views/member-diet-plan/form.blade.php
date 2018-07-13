
        <tr class="child-row-{!! $RowId !!}">

            <td>
            <div>
            {!! Form::select('food_type_id', [''=>'Select Food Type'] + $foodTypeList, $foodTypeId,['class'=>'select2me form-control food_type_id',  'data-rule-required'=>'true', 'data-msg-required'=>'Please Select Food Type']) !!}
                </div>
            </td>

            <td>
               <div>
            <span class='food_list_by_food_type' id="new_food_list_{{ $RowId }}">
            {!! Form::select('food_id', [''=>'Select Food'],  null,['class'=>'select2me form-control select-new-food food_id',  'data-rule-required'=>'true', 'data-msg-required'=>'Please Select Food']) !!}
            </span>
                </div>
            <span class="help-block">Search here or select from list</span>
            </td>

            {!! Form::hidden('diet_schedule_type_id', $dietScheduleTypeId, array('class' => 'diet_schedule_type_id')) !!}

            {!! Form::hidden('diet_plan_row_id', $RowId, array('class' => 'unique_diet_plan_id')) !!}

            {!! Form::hidden('diet_plan_id',$dietPlanId, array('id' => 'diet_plan_id')) !!}

            <td>
             <div>
                {!! Form::text('servings_recommended', $servingsRecommended, ['class'=>'form-control servings_recommended unit_servings_'.$RowId.'', 'maxlength' =>5, 'minlength'=>1, 'data-rule-required'=>'true', 'data-msg-required'=>trans('admin::messages.required-enter', ['name' => 'Servings Recommended']) , 'data-msg-minlength'=>trans('admin::messages.error-minlength', ['name' => 'Servings Recommended'])])!!}
                {!! Form::hidden('calories', null, array('class' => 'unit_calories unit_calories_'.$RowId.'')) !!}
                </div>
            </td>


           <td  class="measure">
            </td>

            <td  class="calories">
            </td>

            <td>
               <span class='total_calories'></span>
            </td>
        </tr>



