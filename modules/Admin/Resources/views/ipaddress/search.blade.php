<tr role="row" class="filter">
    <td></td>
    <td>
    </td>
    <td>
        {!! Form::text('ip_address', null, ['class'=>'form-control form-filter']) !!}
    </td>
    <td>
        {!! Form::text('login_details', null, ['class'=>'form-control form-filter']) !!}
    </td>
    <td>
        {!!  Form::select('status', ['' => 'Select',0 => 'Pending', 1 =>'Accepted', 2 => 'Rejected'], null, ['required', 'class'=>'select2me form-control form-filter input-sm select2-offscreen'])!!}
    </td>
    <td>
    </td>
    <td>
        <button class="btn btn-sm yellow filter-submit margin-bottom-5" title="Search"><i class="fa fa-search"></i></button>
        <button class="btn btn-sm red filter-cancel margin-bottom-5" title="Reset"><i class="fa fa-times"></i></button>
    </td>
</tr>