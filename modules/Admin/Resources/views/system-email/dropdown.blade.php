{!! Form::open() !!}

@if(!empty(Auth::guard('admin')->user()->hasAdd))
{!! Form::select('size', [ '' => trans('admin::messages.select-name',['name' => trans('admin::controller/system-email.system-email') ]) , trans('admin::messages.add-name', ['name'=>trans('admin::controller/system-email.system-email')]) => [ 'new' => trans('admin::messages.add-name', ['name'=>trans('admin::controller/system-email.system-email')]) ]] + $emails, null, ['class'=>'select2me form-control','id'=>'selectEmailTemplate']) !!}
@else
{!! Form::select('size', [ '' => 'Select' ] + $emails, null, ['class'=>'select2me form-control','id'=>'selectEmailTemplate']) !!}
@endif

<span class="help-block">{!! trans('admin::controller/system-email.name-help') !!}</span>
{!! Form::close() !!}