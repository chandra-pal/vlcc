@foreach ($results as $key=>$value)
<tr>
    <td><div class="checker"><span><input type="checkbox" value="1" name="id[]"></span></div></td><td>{{$key+1}}</td>
    <td>{{ $value->ip_address }}</td>
    <td>{{ $value->status }}</td>
    <td>{{ $value->created_at }}</td>
    <td><a class="btn btn-xs default" href="{!! URL::to('admin/ipaddress/'.$value->id.'/edit')!!}"><i class="fa fa-edit"></i> Edit</a></td>
</tr>
@endforeach