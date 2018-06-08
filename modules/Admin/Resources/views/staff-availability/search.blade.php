<tr role="row" class="filter">
    <td></td>
    <td></td>
    <td>
        {!!  Form::select('center_id', ['' => 'Select Center'] +$centerList, null, ['id' => 'center-drop-down-search','autocomplete' => 'off', 'required','class'=>'form-control form-filter input-sm width-auto select2me'])!!}
    </td>
    <td id="staff-drop-down-search">
        {!!  Form::select('staff_id', ['' => 'Select Staff'] +$staffList, null, ['autocomplete' => 'off','class'=>'form-control form-filter input-sm width-auto select2me'])!!}
    </td>
<!--    <td>
        {!! Form::text('availability_date', null, ['readonly'=>'true', 'autocomplete' => 'off', 'class'=>'form-control form-filter calendarSearch']) !!}
    </td>-->
    <td>
        <div class="input-group date form_datetime from-date margin-bottom-5" data-date="{{date('Y-m-d h:i:s')}}">
            {!! Form::text('from_date', null, ['id'=>'from_date','autocomplete' => 'off','class'=>'form-control form-filter input-sm','placeholder'=>'From','disabled'=>'disabled']) !!}
            <span class="input-group-btn">
                <button class="btn from-btn default date-reset btn-sm" type="button"><i class="fa fa-times"></i></button>
                <button class="btn default date-set btn-sm" type="button"><i class="fa fa-calendar"></i></button>
            </span>
        </div>
        <div class="input-group date form_datetime to-date" data-date="{{date('Y-m-d h:i:s')}}">
            {!! Form::text('to_date', null, ['id'=>'to_date','autocomplete' => 'off','class'=>'form-control form-filter input-sm','placeholder'=>'To','disabled'=>'disabled']) !!}
            <span class="input-group-btn">
                <button class="btn to-btn default date-reset btn-sm" type="button"><i class="fa fa-times"></i></button>
                <button class="btn default date-set btn-sm" type="button"><i class="fa fa-calendar"></i></button>
            </span>
        </div>
    </td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td>
        <button class="btn btn-sm yellow filter-submit margin-bottom-5" title="Search"><i class="fa fa-search"></i></button>
        <button class="btn btn-sm red filter-cancel margin-bottom-5" title="Reset"><i class="fa fa-times"></i></button>
    </td>
</tr>