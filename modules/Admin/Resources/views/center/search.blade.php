<tr role="row" class="filter">
    <td></td>
    <td></td>
    <td></td>
    <td>
        {!!  Form::select('country_id', ['' => 'Select Country'] +$countryList, null, ['id' => 'country-drop-down-search', 'required', 'class'=>'select2me form-control form-filter input-sm select2-offscreen']) !!}
    </td>
    <td>
        <div>
            {!!  Form::select('state_id', ['' => 'Select State'] +$stateList, null, ['class'=>'select2me form-control form-filter select2-offscreen', 'id' => 'state-drop-down-search']) !!}
        </div>

    </td>
    <td>
        <div>
            {!!  Form::select('city_id', ['' => 'Select City'] +$cityList, null, ['class'=>'select2me form-control form-filter select2-offscreen', 'id' => 'city-drop-down-search']) !!}
        </div>

    </td>
    <td>{!! Form::text('address', null, ['class'=>'form-control form-filter']) !!}</td>
    <td>{!! Form::text('area', null, ['class'=>'form-control form-filter']) !!}</td>
    <td>{!! Form::text('phone_number', null, ['class'=>'form-control form-filter']) !!}</td>
    <td>
        <select name="status" class="form-control form-filter input-sm width-auto select2me">
            <option value="">Select</option>
            <option value="1"> {!! trans('admin::messages.active') !!}</option>
            <option value="0"> {!! trans('admin::messages.inactive') !!}</option>
        </select>
    </td>
    <td>
        {!! Form::button('<i class="fa fa-search"></i>', ['title' => trans('admin::messages.search'), 'class' => 'btn btn-sm yellow filter-submit margin-bottom-5']) !!}
        {!! Form::button('<i class="fa fa-times"></i>', ['title' => trans('admin::messages.reset'), 'class' => 'btn btn-sm red blue filter-cancel margin-bottom-5']) !!}
    </td>
</tr>