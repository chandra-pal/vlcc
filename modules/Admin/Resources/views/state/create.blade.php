<div class="portlet box blue add-form-main">
    <div class="portlet-title togglelable">
        <div class="caption">
            <i class="fa fa-plus"></i>Add New State
        </div>
        <div class="tools">
            <a href="javascript:;" class="expand box-expand-form"></a>
        </div>
    </div>
    <div class="portlet-body form display-hide">
        {!! Form::open(['route' => ['admin.states.store'], 'id' => 'create-state-form', 'method' => 'post', 'class' => 'form-horizontal state-form',  'msg' => 'State added successfully.']) !!}
        @include('admin::state.form')
        <div class="form-actions">
            <div class="col-md-6">
                <div class="col-md-offset-6 col-md-9">
                    <button class="btn green" type="submit">Submit</button>
                    <button class="btn default btn-collapse btn-collapse-form" type="button">Cancel</button>
                </div>
            </div>
        </div>
        {!! Form::close() !!}
    </div>
</div>