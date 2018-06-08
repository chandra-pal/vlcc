<div class="form-body">
    <div class="form-group">
        <label class="col-md-3 control-label">{!! trans('admin::controller/product-recommendation.select-product') !!}<span class="required" aria-required="true">*</span></label>
        <div class="col-md-4">
            {!! Form::select('product_id[]',[''=>'Select Product'] + $productList, $selectedProduct,['multiple'=>'multiple','class'=>'form-control skip-select', 'id' => 'product_id', 'data-rule-required'=>'true', 'data-msg-required'=>trans('admin::messages.required-select', ['name' => trans('admin::controller/product-recommendation.product-recommendation')])]) !!}
        </div>
    </div>
    <div class="form-group">
        <label class="col-md-3 control-label">{!! trans('admin::controller/diet-plan.status') !!}<span class="required" aria-required="true">*</span> </label>
        <div class="col-md-4">
            <div class="radio-list">
                <label class="radio-inline">{!! Form::radio('status', '1', true) !!} {!! trans('admin::messages.active') !!}</label>
                <label class="radio-inline">{!! Form::radio('status', '0') !!} {!! trans('admin::messages.inactive') !!}</label>
            </div>
        </div>
    </div>

</div>