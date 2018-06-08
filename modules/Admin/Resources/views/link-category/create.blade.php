{!! Form::open(['route' => ['admin.link-category.store'], 'method' => 'post', 'class' => 'form-horizontal panel link-category-form','id'=>'category-form']) !!}
<div class="form-body">
    @include('admin::link-category.form',['action'=>'create'])
</div>
<div class="form-actions">
    <div class="row">
        <div class="col-md-6">
            <div class="row">
                <div class="col-md-offset-4 col-md-9">
                    <button type="submit" name="submit" class="btn green" id='saveBtn'>Submit</button>
                    <button type="button" class="btn default btn-collapse-form btn-collapse">Cancel</button>
                </div>
            </div>
        </div>
        <div class="col-md-6"></div>
    </div>
</div>
{!! Form::close() !!}
