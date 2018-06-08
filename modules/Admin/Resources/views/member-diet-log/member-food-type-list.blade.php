{!! Form::select('food_id[]', [''=>'Select Food'] + $foodList, null,['class'=>'select2me form-control select-new-food', 'id' => 'food_id', 'data-rule-required'=>'true', 'data-msg-required'=>'Please Select Food']) !!}
<span class="help-block">Search here or select from list</span>
<span id="food_error"></span>