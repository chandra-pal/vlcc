<div class="portlet box yellow-gold edit-form-main">
    <div class="portlet-title togglelable">
        <div class="caption">
            <i class="fa fa-pencil"></i>Edit IP Address
        </div>
        <div class="tools">
            <a href="javascript:;" class="collapse box-expand-form"></a>
        </div>
    </div>
    <div class="portlet-body form">
        {!! Form::model($ipAddress, ['route' => ['admin.ipaddress.update', $ipAddress->id], 'method' => 'put', 'class' => 'form-horizontal panel config-category-form', 'id'=>'edit-config-category', 'msg' => 'IP Address updated successfully.']) !!}
        @include('admin::ipaddress.form')

        <div class="form-actions">
            <div class="col-md-6">
                <div class="col-md-offset-6 col-md-9">
                    <button type="submit" class="btn green">Save</button>
                    <button type="button" class="btn default btn-collapse-form-edit">Cancel</button>
                </div>
            </div>
        </div>
        {!! Form::close() !!}
    </div>
</div>