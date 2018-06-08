<tr role="row" class="filter">
    <td></td>
    <td></td>
    <td></td>
    <td>{!! Form::text('customer_name', null, ['class'=>'form-control form-filter customer_name']) !!}</td>
    <td>{!! Form::text('mobile_number', null, ['class'=>'form-control form-filter mobile_number']) !!}</td>
    <td style="width:20%;">
        {!! Form::select('customer_category', [''=> 'Select'] + $customerCategory, null, ['class'=>'form-control form-filter input-sm width-auto select2me', 'autocomplete'=>'off', 'id' => 'customer_category']) !!}

    </td>
    <td>
        {!! Form::button('<i class="fa fa-search"></i>', ['title' => trans('admin::messages.search'), 'class' => 'btn btn-sm yellow filter-submit margin-bottom-5']) !!}
        {!! Form::button('<i class="fa fa-times"></i>', ['title' => trans('admin::messages.reset'), 'class' => 'btn btn-sm red blue filter-cancel margin-bottom-5']) !!}
    </td>
</tr>
