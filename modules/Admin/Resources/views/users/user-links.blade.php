@if(!empty($links))
<div class="col-md-12">
    <div>
        <h3 class="form-section">{!! trans('admin::controller/user.links-assign') !!}</h3>
        <div class="row">
            <div class="col-md-3 margin-bottom-15">
                <div class="input-group">
                    <span class="input-group-addon">
                        {!! Form::checkbox('selectall', 'value', null, ['id'=>'selectall']); !!}
                    </span>
                    <input type="text" class="form-control" placeholder="Select All" disabled>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        @foreach($links as $category)
        @if(!empty($category[0]['category']) > 0)
        <div class="col-md-3 padding">
            <div class="portlet box green-meadow">
                <div class="portlet-title">
                    <div class="caption">
                        {!! Form::checkbox("category[]", $category[0]['linkCatId'],null) !!}<small>{{$category[0]['category']}}</small>
                    </div>
                    <div class="tools">
                        <a class="collapse" href="javascript:;" data-original-title="" title=""></a>
                    </div>
                </div>
                <div class="portlet-body">
                    @foreach($category as $link)
                    <div class="user-link-box">
                        {{--*/
                            $linkId = $link['linkId'];
                            $linkName = $link['link_name'];
                        /*--}}
                        @if (!empty($userLinks) && in_array($linkId, array_keys($userLinks)))
                        <p>{!! Form::checkbox('links[]', $linkId,true) !!} <strong>{{$linkName}}</strong> ({{ $viewRecords }})</p>

                        <div class="link-actions">
                            {!! Form::checkbox("user_links[$linkId][is_add]", 1, $userLinks[$linkId]['is_add']) !!} Add
                            {!! Form::checkbox("user_links[$linkId][is_edit]", 1, $userLinks[$linkId]['is_edit']) !!} Edit All
                            {!! Form::checkbox("user_links[$linkId][is_delete]", 1, $userLinks[$linkId]['is_delete']) !!} Delete All <br>
                            {!! Form::checkbox("user_links[$linkId][own_view]", 1, $userLinks[$linkId]['own_view']) !!} View only his records <br>
                            {!! Form::checkbox("user_links[$linkId][own_edit]", 1, $userLinks[$linkId]['own_edit']) !!} Edit only his records <br />
                            {!! Form::checkbox("user_links[$linkId][own_delete]", 1, $userLinks[$linkId]['own_delete']) !!} Delete only his records <br><br>
                        </div>
                        @else
                        <p>{!! Form::checkbox('links[]', $linkId, null) !!} <strong>{{$linkName}}</strong> ({{ $viewRecords }})</p>

                        <div class="link-actions">
                            {!! Form::checkbox("user_links[$linkId][is_add]", 1, null) !!} Add
                            {!! Form::checkbox("user_links[$linkId][is_edit]", 1, null) !!} Edit All
                            {!! Form::checkbox("user_links[$linkId][is_delete]", 1, null) !!} Delete All <br>
                            {!! Form::checkbox("user_links[$linkId][own_view]", 1, null) !!} View only his records <br>
                            {!! Form::checkbox("user_links[$linkId][own_edit]", 1, null) !!} Edit only his records <br />
                            {!! Form::checkbox("user_links[$linkId][own_delete]", 1, null) !!} Delete only his records <br><br>
                        </div>
                        @endif

                    </div>
                    @endforeach
                </div>
            </div>
        </div>
        @endif
        @endforeach
    </div>
</div>
@endif


