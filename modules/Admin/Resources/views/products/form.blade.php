@section('page-level-styles')
@parent
{!! HTML::style( URL::asset('global/plugins/bootstrap-fileinput/bootstrap-fileinput.css') ) !!}
@stop

<div class="form-body">
    <div class="form-group">
        <label class="col-md-3 control-label">{!! trans('admin::controller/products.products-title') !!}<span class="required" aria-required="true">*</span></label>
        <div class="col-md-4">
            {!! Form::text('product_title', null, ['minlength'=>2,'maxlength'=>50,'class'=>'form-control', 'id'=>'product_title', 'data-rule-required'=>'true', 'data-msg-required'=>trans('admin::messages.required-enter', ['name' => trans('admin::controller/products.products-title')]), 'data-rule-maxlength'=>'50', 'data-msg-maxlength'=>trans('admin::messages.error-maxlength', ['name'=>trans('admin::controller/products.products-title')]) , 'data-msg-minlength'=>trans('admin::messages.error-minlength', ['name' => trans('admin::controller/products.products-title')]) ])!!}
            
            <span class="help-block">eg: Honey Moisturiser</span>
        </div>
    </div>
    <div class="form-group">
        <label class="col-md-3 control-label">{!! trans('admin::controller/products.products-description') !!}<span class="required" aria-required="true">*</span></label>
        <div class="col-md-6">
            {!! Form::textarea('product_description', null, ['minlength'=>2,'size' => '30x3','class'=>'form-control text-noresize', 'data-rule-required'=>'true', 'data-msg-required'=>'Product description is required', 'data-msg-minlength'=>trans('admin::messages.error-minlength', ['name' => trans('admin::controller/products.products-description')]) ])!!}
            <span class="help-block"></span>
        </div>
    </div>
    <div class="form-group ">
        <label class="col-md-3 control-label">{!! trans('admin::controller/products.avatar') !!}</label>
        <div class="col-md-8">
        <p>{!! trans('admin::controller/products.change-image-help') .' '.trans('admin::messages.mimes').' '.trans('admin::messages.max-file-size') !!}</p>
        <div class="fileinput fileinput-new" data-provides="fileinput">
            <div class="fileinput-new user-form-img margin-bottom-10">  
                @if(!empty($products->product_image))
                {!! \Modules\Admin\Services\Helper\ImageHelper::getProductImage($products->id, $products->product_image) !!}
                @else
                {!! HTML::image(URL::asset('images/default-user-icon-profile.png '), 'default-img', ['class' => 'img-thumbnail img-responsive']); !!}
                @endif
            </div>
            <div class="fileinput-preview fileinput-exists thumbnail" style="max-width: 200px; max-height: 150px;"></div>
            <div id='file-error' class='text-danger margin-bottom-10 bold'></div>
            <div class="inline">&nbsp;
                <span class="btn default btn-file">
                    <span class="fileinput-new">
                        @if(!empty($products->product_image))
                        {!! trans('admin::controller/user.change-image') !!}
                        @else 
                        {!! trans('admin::controller/user.select-image') !!}
                        @endif
                    </span>
                    <span class="fileinput-exists">{!! trans('admin::messages.change') !!} </span>
                    {!! Form::file('thefile', ['id' => 'thefile', 'class' => 'field']) !!}
                </span>
                <span class="fileinput-new">&nbsp;
                    @if(!empty($products->product_image))
                    <a href="javascript:;" class="btn default remove-image" >
                        {!! trans('admin::controller/user.remove-image') !!} </a>
                    @endif
                </span>&nbsp;
                <a href="javascript:;" class="btn default fileinput-exists" data-dismiss="fileinput">
                    {!! trans('admin::messages.remove') !!} </a>
            </div>
        </div>
        <div class="clearfix margin-top-15 margin-bottom-15">
            <span class="label label-danger">{!! trans('admin::messages.note') !!} </span>
            <span style="margin-left:10px;">{!! trans('admin::controller/user.support-image-help') !!}</span>
        </div>
    </div>
    </div>
    <div class="form-group">
        <label class="col-md-3 control-label">{!! trans('admin::controller/products.product-detail-page-url') !!}<span class="required" aria-required="true">*</span></label>
        <div class="col-md-4">
            {!! Form::text('product_detail_page_url', null, ['minlength'=>1, 'class'=>'form-control', 'id'=>'product_detail_page_url',  'data-rule-required'=>'true', 'data-msg-number'=>'Please enter valid url.', 'data-msg-required'=>trans('admin::messages.required-enter', ['name' => trans('admin::controller/products.product-detail-page-url')]) ]) !!}
            <span class="help-block">eg: https://www.vlccpersonalcare.com/product/honey-moisturiser/</span>
        </div>
    </div>   
    <div class="form-group">
        <label class="col-md-3 control-label">{!! trans('admin::controller/products.status') !!}<span class="required" aria-required="true">*</span> </label>
        <div class="col-md-4">
            <div class="radio-list">
                <label class="radio-inline">{!! Form::radio('status', '1', true) !!} {!! trans('admin::messages.active') !!}</label>
                <label class="radio-inline">{!! Form::radio('status', '0') !!} {!! trans('admin::messages.inactive') !!}</label>
            </div>
        </div>
    </div>

    
</div>