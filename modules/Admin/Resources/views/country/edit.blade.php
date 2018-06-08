<div class="portlet box yellow-gold edit-form-main">
    <div class="portlet-title togglelable">
        <div class="caption">
            <i class="fa fa-pencil"></i>Edit Country 
        </div>
        <div class="tools">
            <a href="javascript:;" class="collapse box-expand-form"></a>
        </div>
    </div>
    <div class="portlet-body form">
        {!! Form::model($country, ['route' => ['admin.countries.update', $country->id], 'method' => 'put', 'class' => 'form-horizontal panel country-form','id'=>'edit-country', 'msg' => 'Country updated successfully.']) !!}
        @include('admin::country.form')
        <div class="form-actions">
            <div class="col-md-6">
                <div class="col-md-offset-6 col-md-9">
                    <button type="submit" class="btn green">Save</button>
                    <button type="button" class="btn default btn-collapse btn-collapse-form-edit">Cancel</button>
                </div>
            </div>
        </div>
        </form>
    </div>
</div>