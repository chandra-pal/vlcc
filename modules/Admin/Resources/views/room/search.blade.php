<tr role="row" class="filter" id="search-room">
    <td>
    </td>
    <td>
    </td>
    <td>
        {!! Form::select('cname', [''=> 'Select Center Name' ] + $searchCenterList, null,[ 'id' => 'cname', 'autocomplete' => 'off','class'=>'select2me form-control form-filter']) !!}
    </td>
    <td>
        {!! Form::text('name', null, ['autocomplete' => 'off','class'=>'form-control form-filter']) !!}
    </td>
    <td>
        {!!  Form::select('room_type', ['' => 'Select Room Type',1 => 'Male', 2 => 'Female',  3 => 'Common'], null, ['id' => 'room-drop-down-search', 'autocomplete' => 'off', 'class'=>'select2me form-control form-filter'])!!}
    </td>
    <td>
        {!!  Form::select('status', ['' => 'Select', 1 => trans('admin::messages.active'), 0 => trans('admin::messages.inactive')], null, ['id' => 'status-drop-down-search','autocomplete' => 'off', 'class'=>'select2me form-control form-filter'])!!}
    </td>
    <td>
        {!! Form::button('<i class="fa fa-search"></i>', ['title' => trans('admin::messages.search'), 'class' => 'btn btn-sm yellow filter-submit margin-bottom-5']) !!}
        {!! Form::button('<i class="fa fa-times"></i>', ['title' => trans('admin::messages.reset'), 'class' => 'btn btn-sm red blue filter-cancel margin-bottom-5']) !!}
    </td>
</tr>