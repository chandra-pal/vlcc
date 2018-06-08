<label class="col-md-5 control-label" style="text-align:left !important;">Room<span class="required" aria-required="true">*</span> </label>
<div class="col-md-4" id="room-listing-content">
    {!! Form::select('room_id', [''=>'Select Room'] + $roomList, null,['autocomplete' => 'off', 'class'=>'select2me form-control form-filter room_id', 'id' => 'room_id', 'data-rule-required'=>'true', 'data-msg-required'=>trans('admin::messages.required-enter', ['name' => trans('admin::controller/room-availability.room')])]) !!}
</div>
