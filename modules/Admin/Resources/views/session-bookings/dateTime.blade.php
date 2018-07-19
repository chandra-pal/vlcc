
<tr>
<div class="input-group date form_datetime from-date margin-bottom-5" data-date="{{date('Y-m-d h:i:s')}}">
    {!! Form::text('activity_time_from', null, ['class'=>'form-control form-filter input-sm','placeholder'=>'From','disabled'=>'disabled']) !!}
    <span class="input-group-btn">
    <button class="btn from-btn default date-reset btn-sm" type="button"><i class="fa fa-times"></i></button>
    <button class="btn default date-set btn-sm" type="button"><i class="fa fa-calendar"></i></button>
    </span>
    </div>
</tr>
<tr>
	<div class="input-group date form_datetime to-date" data-date="{{date('Y-m-d h:i:s')}}">
    {!! Form::text('activity_time_to', null, ['class'=>'form-control form-filter input-sm','placeholder'=>'To','disabled'=>'disabled']) !!}
    <span class="input-group-btn">
    <button class="btn to-btn default date-reset btn-sm" type="button"><i class="fa fa-times"></i></button>
    <button class="btn default date-set btn-sm" type="button"><i class="fa fa-calendar"></i></button>
    </span>
	</div>
</tr>