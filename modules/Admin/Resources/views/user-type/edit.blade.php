<div class="portlet box yellow-gold edit-form-main">
    <div class="portlet-title togglelable">
        <div class="caption">
            <i class="fa fa fa-pencil"></i>Edit User Role
        </div>
        <div class="tools">
            <a href="javascript:;" class="collapse box-expand-form"></a>
        </div>
    </div>
    <div class="portlet-body form">
        {!! Form::model($userType, ['route' => ['admin.user-type.update', $userType->id], 'method' => 'put', 'class' => 'form-horizontal panel user-type-form','id'=>'edit-user-type', 'msg' => 'User Type updated successfully.']) !!}
        @include('admin::user-type.form')
        <div class="form-actions">
            <div class="row">
                <div class="col-md-6">
                    <div class="row">
                        <div class="col-md-offset-3 col-md-9">
                            <button type="submit" class="btn green">Save</button>
                            <button type="button" class="btn default btn-collapse btn-collapse-form-edit">Cancel</button>
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