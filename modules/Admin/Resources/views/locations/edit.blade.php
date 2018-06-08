<div class="portlet box yellow-gold edit-form-main">
    <div class="portlet-title togglelable">
        <div class="caption">
            <i class="fa fa-pencil"></i>Edit Location 
        </div>
        <div class="tools">
            <a href="javascript:;" class="collapse box-expand-form"></a>
        </div>
    </div>
    <div class="portlet-body form">
        {!! Form::model($locations, ['route' => ['admin.locations.update', $locations->id], 'method' => 'put', 'class' => 'form-horizontal panel config-setting-form','id'=>'edit-locations', 'msg' => 'Location updated successfully.']) !!}
        @include('admin::locations.form',['action'=>'update'])
        <div class="form-actions">
            <div class="col-md-7">
                <div class="col-md-offset-3 col-md-9">
                    <button type="submit" class="btn green">Save</button>
                    <button type="button" class="btn default btn-collapse btn-collapse-form-edit">Cancel</button>
                </div>
            </div>
        </div>
        {!! Form::close() !!}
    </div>
</div>