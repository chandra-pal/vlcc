
{!! Form::select('room_id[]', [''=> trans('admin::messages.select-name', [ 'name' => trans('admin::controller/session-bookings.room') ])] + $centerRoomList, $roomId,['multiple'=>'multiple','class'=>'select2me form-control form-filter', 'id' => 'room_id', 'data-rule-required'=>'true', 'data-msg-required'=>'Please select Room.']) !!}
<span class="help-block help-block-error customer_error"></span>
<div id="set-room-availability">

    {!! $roomResourceTime !!}


</div>