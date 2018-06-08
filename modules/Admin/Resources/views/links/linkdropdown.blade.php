<label class="col-md-3 control-label">Link <span class="required" aria-required="true">*</span></label>
<div class="col-md-4" id='link-listing-content'>
    {!! Form::select('link_id', [''=> trans('admin::messages.select-name',['name'=>trans('admin::controller/links.link')]) ] + $linkList, null,['class'=>'form-filter select2me form-control', 'id' => 'link_id', 'data-rule-required'=>'true', 'data-msg-required'=>'Please select Link.']) !!}
</div>