<tr role="row" class="filter">
    <td></td><td></td>
    <td></td>
    <td>
        {!! Form::text('username', null, ['class'=>'form-control form-filter input-sm']) !!}
    </td>
    <td>
        {!! Form::text('email', null, ['class'=>'form-control form-filter input-sm']) !!}
    </td>
    <td>
        {!! Form::select('center_name', [''=> 'Select Center Name' ] + $searchCenterList, null,['autocomplete' => 'off', 'class'=>'select2me form-control form-filter state_id', 'id' => 'center_name']) !!}
    </td>
    <td></td>
    <td></td>
    <td>
        {!!  Form::select('status', ['' => 'Select',1 => trans('admin::messages.active'), 0 =>trans('admin::messages.inactive') ], null, ['id' => 'status_search', 'class'=>'select2me form-control form-filter input-sm']) !!}
    </td>
    <td>
        {!! Form::button('<i class="fa fa-search"></i>', ['title' => trans('admin::messages.search'), 'class' => 'btn btn-sm yellow filter-submit margin-bottom-5']) !!}
        {!! Form::button('<i class="fa fa-times"></i>', ['title' => trans('admin::messages.reset'), 'class' => 'btn btn-sm red blue filter-cancel margin-bottom-5']) !!}
    </td>
</tr>