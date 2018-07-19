
{!! Form::select('staff_id[]',[''=>'Select Staff'] +$centerStaffList, $staffId,['multiple'=>'multiple', 'class'=>'select2me form-control', 'id' => 'staff_id', 'data-rule-required'=>'true', 'data-msg-required'=>trans('admin::messages.required-select', ['name' => trans('admin::controller/session-bookings.staff')])]) !!}
<span class="help-block help-block-error customer_error"></span>
<div id="set-staff-availability">
   {{-- <div id="staff-222"><span>Staff</span><select name="staff_assign">
            <option value="1">7:30</option>
        </select>
    </span><select name="staff_assign">
        <option value="1">7:30</option>
    </select>
</div>--}}
   {!! $staffResourceTime !!}


</div>