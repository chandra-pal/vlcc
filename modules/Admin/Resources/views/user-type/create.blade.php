<div class="portlet box blue add-form-main">
    <div class="portlet-title togglelable">
        <div class="caption">
            <i class="fa fa-plus"></i>Add New User Type
        </div>
        <div class="tools">
            <a href="javascript:;" class="expand box-expand-form"></a>
        </div>
    </div>
    <div class="portlet-body form display-hide">
        {!! Form::open(['route' => ['admin.user-type.store'], 'method' => 'post', 'class' => 'form-horizontal config-setting-form',  'id' => 'create-user-type', 'msg' => 'Type added successfully.']) !!}
        @include('admin::user-type.form')
        <div class="form-actions">
            <div class="row">
                <div class="col-md-6">
                    <div class="row">
                        <div class="col-md-offset-3 col-md-9">
                            <button type="submit" class="btn green">Submit</button>
                            <button type="button" class="btn default btn-collapse btn-collapse-form">Cancel</button>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                </div>
            </div>
        </div>
        {!! Form::close() !!}
    </div>
</div>