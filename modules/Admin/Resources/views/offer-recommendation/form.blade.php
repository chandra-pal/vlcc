<div class="form-body">
    <div class="form-group">
        <label class="col-md-3 control-label">{!! trans('admin::controller/offer-recommendation.select-offer') !!}<span class="required" aria-required="true">*</span></label>
        <div class="col-md-4">
            {!! Form::select('offer_id[]',[''=>'Select Offer'] + $offerList, $selectedOffer,['multiple'=>'multiple','class'=>'form-control skip-select', 'id' => 'offer_id', 'data-rule-required'=>'true', 'data-msg-required'=>trans('admin::messages.required-select', ['name' => trans('admin::controller/offer-recommendation.offer-recommendation')])]) !!}
            <inpu type="hidden" id="offer_ids" name="offer_ids">
        </div>
    </div>
    <div class="form-group">
        <label class="col-md-3 control-label">{!! trans('admin::controller/offer-recommendation.status') !!}<span class="required" aria-required="true">*</span> </label>
        <div class="col-md-4">
            <div class="radio-list">
                <label class="radio-inline">{!! Form::radio('status', '1', true) !!} {!! trans('admin::messages.active') !!}</label>
                <label class="radio-inline">{!! Form::radio('status', '0') !!} {!! trans('admin::messages.inactive') !!}</label>
            </div>
        </div>
    </div>

</div>