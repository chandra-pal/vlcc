@if($message)
<div class="alert alert-{!! $type !!} alert-dismissible" role="alert">
    <button type="button" class="close" data-dismiss="alert">
        <span aria-hidden="true">&times;</span>
        <span class="sr-only">Close</span>
    </button>
    {!! $message !!}
</div>
@endif

@if ($errors->count())
<div class="alert alert-danger">
    <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
    {!! HTML::ul($errors->all()) !!}
</div>
@endif

<div id="ajax-response-text"></div>