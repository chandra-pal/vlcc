<tr role="row" class="filter">
    <td>
    </td>
    <td>
    </td>
    <td>
        {!!  Form::select('country_id', ['' => 'Select Country'] +$countryList, null, ['id' => 'country-drop-down-search', 'required', 'class'=>'select2me form-control form-filter input-sm select2-offscreen']) !!}
    </td>
    <td>
        <div id="state-drop-down-search">
            {!!  Form::select('state_id', ['' => 'Select State'] +$stateList, null, ['class'=>'form-control form-filter']) !!}
        </div>

    </td>
    <td>
        {!! Form::text('name', null, ['class'=>'form-control form-filter']) !!}
    </td>
    <td>
        {!!  Form::select('status', ['' => 'Select',0 => trans('admin::messages.inactive'), 1 => trans('admin::messages.active')], null, ['id' => 'status-drop-down-search', 'class'=>'form-control form-filter'])!!}
    </td>
    <td>
        <button class="btn btn-sm yellow filter-submit margin-bottom-5" title="{!! trans('admin::messages.search') !!}"><i class="fa fa-search"></i></button>
        <button class="btn btn-sm red filter-cancel margin-bottom-5" title="{!! trans('admin::messages.reset') !!}"><i class="fa fa-times"></i></button>
    </td>
</tr>