<tr role="row" class="filter" id="search-rec">
    <td>
    </td>
    <td></td>
    <td>
        {!! Form::select('cname', [''=> 'Select Center Name' ] + $searchCenterList, null,['autocomplete' => 'off','class'=>'select2me form-control form-filter', 'id' => 'cname']) !!}
    </td>
    <td>
        {!! Form::text('machine_type', null, ['autocomplete' => 'off','class'=>'form-control form-filter']) !!}
    </td>
    <td>
        {!! Form::text('name', null, ['autocomplete' => 'off','class'=>'form-control form-filter']) !!}
    </td>
    <td>
        {!! Form::text('description', null, ['autocomplete' => 'off','class'=>'form-control form-filter']) !!}
    </td>
    <td>
        {!!  Form::select('status', ['' => 'Select',0 => trans('admin::messages.inactive'), 1 => trans('admin::messages.active')], null, ['id' => 'status-drop-down-search', 'autocomplete' => 'off','class'=>'select2me form-control form-filter'])!!}
    </td>

    <td>
        <button class="btn btn-sm yellow filter-submit margin-bottom-5" title="Search"><i class="fa fa-search"></i></button>
        <button class="btn btn-sm red filter-cancel margin-bottom-5" title="Reset"><i class="fa fa-times"></i></button>
    </td>
</tr>