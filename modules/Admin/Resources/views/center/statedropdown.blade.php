<div id="state-drop-down">{!! Form::select('state_id', [''=> trans('admin::messages.select-name',['name'=>trans('admin::controller/locations.state')]) ] + $stateList, null,['class'=>'select2me form-control form-filter state_id', 'id' => 'state_id', 'data-rule-required'=>'true', 'data-msg-required'=>'Please select State.']) !!}</div>