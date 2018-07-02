<div class="portlet box yellow-gold edit-form-main">
    <div class="portlet-title togglelable">
        <div class="caption">
            <i class="fa fa-pencil"></i>Edit Member Diet Plan
        </div>
        <div class="tools">
            <a href="javascript:;" class="collapse box-expand-form"></a>
        </div>
    </div>
    <div class="portlet-body form">
        {!! Form::model($memberDietPlan, ['route' => ['admin.member-diet-plan.store', $memberDietPlan->id], 'method' => 'put', 'class' => 'form-horizontal panel post-form','id'=>'edit-diet-plan', 'msg' => 'Diet Plan updated successfully.']) !!}
        <table class="table table-striped table-bordered table-hover">
            <thead></thead>
            <tbody>
            @include('admin::member-diet-plan.form')

            <tr class="col-md-6">
                <td class="col-md-offset-6 col-md-9">
                    <button type="submit" class="btn green save-member-diet-plan">Save</button>
                    <button type="button" class="btn default btn-collapse btn-collapse-form-edit">Cancel</button>
                </td>
            </tr>
            </tbody>
        </table>
        {!! Form::close() !!}
    </div>
</div>