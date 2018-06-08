<tr role="row" class="filter">
    <td></td>
    <td></td>
    <td></td>
    <td>{!! Form::text('Dieticianfullname', null, ['id' => 'Dieticianfullname', 'autocomplete' => 'off', 'class'=>'form-control form-filter Dieticianfullname']) !!}</td>
    <td>{!! Form::text('Memberfullname', null, ['id' => 'Memberfullname', 'autocomplete' => 'off', 'class'=>'form-control form-filter Memberfullname']) !!}</td>
    <td>{!! Form::text('mobile_number', null, ['id' => 'mobile_number', 'autocomplete' => 'off', 'class'=>'form-control form-filter mobile_number']) !!}</td>
<!--     <td>{!! Form::text('NotiType', null, ['id' => 'NotiType', 'autocomplete' => 'off', 'class'=>'form-control form-filter NotiType']) !!}</td>-->
    <td><select name="message_type_dropdown" id='message_type_dropdown' autocomplete="off" class="form-control form-filter input-sm select2me">
            <option value="">Select Message Type</option>
            <option value="1">General Notification</option>
            <option value="2">Activity Notification</option>
            <option value="3">Diet Notification</option>
            <option value="4">Session Notification</option>
        </select></td>
    <td></td>
    <td>
        {!! Form::button('<i class="fa fa-search"></i>', ['title' => trans('admin::messages.search'), 'class' => 'btn btn-sm yellow filter-submit margin-bottom-5']) !!}
        {!! Form::button('<i class="fa fa-times"></i>', ['title' => trans('admin::messages.reset'), 'class' => 'btn btn-sm red blue filter-cancel margin-bottom-5']) !!}
    </td>
</tr>
