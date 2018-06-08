<div>
    <div class="page-title">
        <h4>View Details</h2>
        <span>Member Name: {{$memberData[0]->memFullName ? $memberData[0]->memFullName: ""}}</span>
        <br/> 
        <span>Mobile Number: {{$memberData[0]->mobile_number ? $memberData[0]->mobile_number: ""}}</span>
        <br/>
    </div>
    <div class="form-group">
        <label>Dieticians<span class="required" aria-required="true">*</span></label>
       {!! Form::select('dietician_id', [''=>'Select Dietician'] + $dieticianDropdown, $dieticianUserName,['class'=>'select2me', 'id' => 'dietician_id', 'data-rule-required'=>'true', 'data-msg-required'=>'Please select dietician.']) !!}
    </div>
</div>
