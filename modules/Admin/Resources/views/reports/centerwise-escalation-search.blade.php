<tr role="row" class="filter">
    <td></td>
    <td></td>
    <td></td>
    <td>{!! Form::text('ATHfullname', null, ['id' => 'ATHfullname', 'autocomplete' => 'off', 'class'=>'form-control form-filter ATHfullname']) !!}</td>
    <td>{!! Form::text('Dieticianfullname', null, ['id' => 'Dieticianfullname', 'autocomplete' => 'off', 'class'=>'form-control form-filter Dieticianfullname']) !!}</td>
    <td>{!! Form::text('Memberfullname', null, ['id' => 'Memberfullname', 'autocomplete' => 'off', 'class'=>'form-control form-filter Memberfullname']) !!}</td>
    <td>{!! Form::text('mobile_number', null, ['id' => 'mobile_number', 'autocomplete' => 'off', 'class'=>'form-control form-filter mobile_number']) !!}</td>
    <td></td>
    <td>
        {!! Form::button('<i class="fa fa-search"></i>', ['title' => trans('admin::messages.search'), 'class' => 'btn btn-sm yellow filter-submit margin-bottom-5']) !!}
        {!! Form::button('<i class="fa fa-times"></i>', ['title' => trans('admin::messages.reset'), 'class' => 'btn btn-sm red blue filter-cancel margin-bottom-5']) !!}
    </td>
</tr>
