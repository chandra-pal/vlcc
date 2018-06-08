<div class="portlet box blue add-form-main">
    <div class="portlet-title togglelable">
        <div class="caption">
            <i class="fa fa-plus"></i>Add New Machine
        </div>
        <div class="tools">
            <a href="javascript:;" class="expand box-expand-form"></a>
        </div>
    </div>
    <div class="portlet-body form display-hide">
        {!! Form::open(['route' => ['admin.machines.store'], 'method' => 'post', 'class' => 'form-horizontal post-form',  'id' => 'create-machine', 'msg' => 'Type added successfully.']) !!}
        @include('admin::machine.form',['action'=>'create'])
        <div class="form-actions">
            <div class="col-md-6">
                <div class="col-md-offset-6 col-md-9">
                    <button type="submit" class="btn green">Submit</button>
                    <button type="button" class="btn default btn-collapse btn-collapse-form">Cancel</button>
                </div>
            </div>
        </div>
        {!! Form::close() !!}
    </div>
</div>