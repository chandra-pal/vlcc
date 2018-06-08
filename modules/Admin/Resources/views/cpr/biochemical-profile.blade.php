<table class="table table-striped table-bordered table-hover">
    <thead>
        <tr role="row" class="heading">
            <th colspan="2"> Biochemical Profile </th>
            <th> {!! trans('admin::controller/cpr.initial') !!} </th>
            <th> {!! trans('admin::controller/cpr.final') !!} </th>
        </tr>
        <tr role="row" class="heading">
            <td> {!! trans('admin::controller/cpr.condition') !!} </td>
            <td> {!! trans('admin::controller/cpr.test-to-be-done') !!} </td>
            <td>  </td>
            <td>  </td>
        </tr>
    </thead>
    <tbody>

        @foreach($biochemicalCondition as $k => $v)
        @foreach($v['condition_test'] as $key=> $test)
        <tr role="row" class="heading">
            @if( $key==0)
            <td rowspan="{!! (sizeof($v['condition_test'])) !!}" > {!! $v['condition_name'] !!}</td>
            @endif
            <td>
                {!! $test['test_name'] !!}
                {!! Form::hidden('test_id[]', $test['id'], ['id'=>'test_id'])!!}
            </td>
            <td>
                {!! Form::text('initial_'.$test['id'], null, ['minlength'=>2,'maxlength'=>30,'class'=>'form-control min-one-required', 'id'=>'initial_'.$test['id'], 'data-rule-maxlength'=>'30', 'data-msg-maxlength'=>trans('admin::messages.error-maxlength', ['name'=>trans('admin::controller/cpr.initial')]) , 'data-msg-minlength'=>trans('admin::messages.error-minlength', ['name' => trans('admin::controller/cpr.initial')]) ])!!}
            </td>
            <td>
                {!! Form::text('final_'.$test['id'], null, ['minlength'=>2,'maxlength'=>30,'class'=>'form-control min-one-required', 'id'=>'final_'.$test['id'],'data-rule-maxlength'=>'30', 'data-msg-maxlength'=>trans('admin::messages.error-maxlength', ['name'=>trans('admin::controller/cpr.final')]) , 'data-msg-minlength'=>trans('admin::messages.error-minlength', ['name' => trans('admin::controller/cpr.final')]) ])!!}
            </td>
        </tr>
        @endforeach

        @endforeach

    </tbody>
</table>