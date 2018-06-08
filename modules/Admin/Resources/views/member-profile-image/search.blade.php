<tr role="row" class="filter">
    <td></td>
    <td></td>
    <td>{!! Form::text('plan_name', null, ['class'=>'form-control form-filter']) !!}</td>
    <td> <select name="plan_type" class="form-control form-filter input-sm width-auto select2me">
            <option value="">Select</option>
            <option value="1">Veg</option>
            <option value="2">Non Veg</option>
        </select>
    </td>
    <td>
        {!! Form::text('calories', null, ['class'=>'form-control form-filter']) !!}
    </td>
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