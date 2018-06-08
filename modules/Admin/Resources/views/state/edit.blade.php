<div class="portlet box yellow-gold edit-form-main">
    <div class="portlet-title togglelable">
        <div class="caption">
            <i class="fa fa fa-pencil"></i>Edit State
        </div>
        <div class="tools">
            <a href="javascript:;" class="collapse box-expand-form"></a>
        </div>
    </div>
    <div class="portlet-body form">
        {!! Form::model($state, ['route' => ['admin.states.update', $state->id], 'id' => 'edit-state-form', 'method' => 'put', 'class' => 'form-horizontal panel state-form', 'msg' => 'State updated successfully.']) !!}
        @include('admin::state.form')
        <div class="form-actions">
            <div class="col-md-6">
                <div class="col-md-offset-6 col-md-9">
                    <button class="btn green" type="submit">Save</button>
                    <button class="btn default btn-collapse btn-collapse-form-edit" type="button">Cancel</button>
                </div>
            </div>
        </div>
        {!! Form::close() !!}
    </div>
</div>