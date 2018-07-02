
        <tr class="child-row-{!! $Id !!}">
            <td colspan="2">
            <label class="col-md-3 control-label">Food Type<span class="required" aria-required="true">*</span></label>
                <div>
            {!! Form::select('food_type_id['.$Id.']', [''=>'Select Food Type'] + $foodTypeList, null,['class'=>'select2me form-control food_type_id',  'data-rule-required'=>'true', 'data-msg-required'=>'Please Select Food Type']) !!}
                </div>
            </td>

            <td colspan="2">
            <label class="col-md-3 control-label">Food Name<span class="required" aria-required="true">*</span></label>
                <div>
            <span class='food_list_by_food_type' id="new_food_list_{!! $Id !!}">
            {!! Form::select('food_id['.$Id.']', [''=>'Select Food'] , null,['class'=>'select2me form-control select-new-food food_id',  'data-rule-required'=>'true', 'data-msg-required'=>'Please Select Food']) !!}
            </span>
                </div>
            <span class="help-block">Search here or select from list</span>
            </td>

            {!! Form::hidden('diet_schedule_type_id['.$Id.']', $Id, array('class' => 'diet_schedule_type_id')) !!}

            {!! Form::hidden('diet_plan_row_id['.$Id.']', $Id, array('class' => 'unique_diet_plan_id')) !!}

            <td colspan="2">
            <label class="col-md-3 control-label">Servings Recommended<span class="required" aria-required="true">*</span></label>
                <div>
                {!! Form::text('servings_recommended['.$Id.']', null, ['class'=>'form-control servings_recommended unit_servings_'.$Id.'', 'maxlength' =>5, 'minlength'=>1, 'data-rule-required'=>'true', 'data-msg-required'=>trans('admin::messages.required-enter', ['name' => 'Servings Recommended']) , 'data-msg-minlength'=>trans('admin::messages.error-minlength', ['name' => 'Servings Recommended'])])!!}
                {!! Form::hidden('calories['.$Id.']', null, array('class' => 'unit_calories unit_calories_'.$Id.'')) !!}
                </div>
            </td>


            <label class="col-md-3 control-label">Measure<span class="required" aria-required="true">*</span></label>
            <td colspan="2" class="measure">
            </td>

            <label class="col-md-3 control-label">Calories<span class="required" aria-required="true">*</span></label>
            <td colspan="2" class="calories">
            </td>

            <td colspan="2">
            <label class="col-md-3 control-label">Total Calories<span class="required" aria-required="true">*</span></label>
                <span class='total_calories'></span>
            </td>
        </tr>



