<tr role="row" class="filter">
    <td></td>
    <td></td>
    <td></td>
    <td>{!! Form::text('username', null, ['id' => 'username', 'autocomplete' => 'off', 'class'=>'form-control form-filter usernamee']) !!}</td>
    <td>{!! Form::text('fullname', null, ['id' => 'fullname', 'autocomplete' => 'off', 'class'=>'form-control form-filter fullname']) !!}</td>
    <td>{!! Form::text('contact', null, ['id' => 'contact', 'autocomplete' => 'off', 'class'=>'form-control form-filter contact']) !!}</td>
    <td>{!! Form::text('designation', null, ['id' => 'designation', 'autocomplete' => 'off', 'class'=>'form-control form-filter designation']) !!}</td>
    <td>
        {!! Form::button('<i class="fa fa-search"></i>', ['title' => trans('admin::messages.search'), 'class' => 'btn btn-sm yellow filter-submit margin-bottom-5']) !!}
        {!! Form::button('<i class="fa fa-times"></i>', ['title' => trans('admin::messages.reset'), 'class' => 'btn btn-sm red blue filter-cancel margin-bottom-5']) !!}
    </td>
</tr>
