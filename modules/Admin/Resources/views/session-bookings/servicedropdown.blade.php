
{!! Form::select('service_id[]', [''=>'Select Service'] +$serviceList, $selectedServices, ['multiple'=>'multiple', 'class'=>'select2me form-control form-filter select-service', 'id' => 'service_id', 'data-rule-required'=>'true', 'data-msg-required'=>'Please select Service.']) !!}
<span class="help-block help-block-error customer_error"></span>

@if(!empty($used_services))
<div id="used-services-block">
    <p class="help-block help-block-error">Completed Services : </p>
    @foreach ($used_services as $item)
    @if ($item == end($used_services))
    <span>{{ $item }}</span>
    @else
    <span>{{ $item }},</span>
    @endif
    @endforeach
</div>
@endif

@if(!empty($unpaid_services))
<!--<div id="unpaid-services-block">
    <p class="help-block help-block-error">Unpaid Services : </p>
    @foreach ($unpaid_services as $item)
    @if ($item == end($unpaid_services))
    <span>{{ $item }}</span>
    @else
    <span>{{ $item }},</span>
    @endif
    @endforeach
</div>-->
@endif
