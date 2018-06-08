<div class="form-group col-md-6">
    <div class="list_centers">
        <?php $selectedCenter = Session::get('center_id') ? Session::get('center_id') : null; ?>
        @if(isset($centersList))
        {!! Form::select('center_id', [''=> trans('admin::messages.select-name', [ 'name' => trans('admin::controller/member.center') ])] + $centersList, $selectedCenter,['class'=>'select2me form-control', 'id' => 'center_select', 'autocomplete'=>'off', 'data-rule-required'=>'true', 'data-msg-required'=>'Please select Center.']) !!}
            <span class="help-block help-block-error"></span>
        @endif
    </div>
    <span style="color: red; display: none;" id="select-center-error">Please Select Center</span>
</div>