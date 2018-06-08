
{!! Form::select('staff_id[]',[''=>'Select Staff'] +$centerStaffList, $staffId,['multiple'=>'multiple', 'class'=>'select2me form-control', 'id' => 'staff_id', 'data-rule-required'=>'true', 'data-msg-required'=>trans('admin::messages.required-select', ['name' => trans('admin::controller/session-bookings.staff')])]) !!}
<span class="help-block help-block-error customer_error"></span>