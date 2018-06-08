<label class="col-md-5 control-label" style="text-align:left !important;">Staff<span class="required" aria-required="true">*</span> </label>
<div class="col-md-4" id="staff-listing-content">
    {!! Form::select('staff_id', [''=>'Select Staff'] + $staffList, null,['autocomplete' => 'off','class'=>'select2me form-control form-filter staff_id', 'id' => 'staff_id', 'data-rule-required'=>'true', 'data-msg-required'=>trans('admin::messages.required-enter', ['name' => trans('admin::controller/staff-availability.staff')])]) !!}
</div>