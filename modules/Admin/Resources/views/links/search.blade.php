<tr role="row" class="filter">
    <td></td>
    <td></td>
    <td>{!! Form::text('cat', null, ['class'=>'form-control form-filter']) !!}</td>
    <td>
    </td>
    <td>
        {!! Form::text('link_name', null, ['class'=>'form-control form-filter']) !!}

    </td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td>
        <select name="status" class="form-control form-filter input-sm width-auto">
            <option value="">Select</option>
            <option value="1"> {!! trans('admin::messages.active') !!}</option>
            <option value="0"> {!! trans('admin::messages.inactive') !!}</option>
        </select>
    </td>
    <td></td>
    <td>
        {!! Form::button('<i class="fa fa-search"></i>', ['title' => trans('admin::messages.search'), 'class' => 'btn btn-sm yellow filter-submit margin-bottom-5']) !!}
        {!! Form::button('<i class="fa fa-times"></i>', ['title' => trans('admin::messages.reset'), 'class' => 'btn btn-sm red blue filter-cancel margin-bottom-5']) !!}
    </td>
</tr>