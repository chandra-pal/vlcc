<?php
if (isset($medical_problem)) {
    $flipped = array_flip($medical_problem);
    foreach ($medical_problem_types as $k => $v) {
        $id = "field_" . $k;
        $selected = (isset($flipped[$k])) ? true : null;
        ?>
        <div class="col-md-4 checkbox-container"><label class="checkbox-inline">{!! Form::checkbox('current_associated_medical_problem[]', $k,$selected, ['class' => 'field', 'id'=>$id]) !!} {!! $v !!}</label></div>
        <?php
    }
}?>