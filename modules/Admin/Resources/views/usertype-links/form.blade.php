<div class="portlet box permissions-list hidden">
    <div class="portlet-body form">
        <div class="form-body">
            <div class="row">
                <div class="col-md-3 margin-bottom-15">
                    <div class="input-group">
                        <span class="input-group-addon">
                            {!! Form::checkbox('name', 'value', null, ['id'=>'selectall']); !!}
                        </span>
                        <input type="text" class="form-control" placeholder="Select All" disabled>
                    </div>
                </div>
            </div>
            @if(!empty($links))
            <div class="row">
                @foreach($links as $key=>$category)
                @if(count($category['Links']) > 0)
                {{--*/
                $categoryInfo = collect($category);
                $categoryName = collect($category)['category'];
                $categoryId = collect($category)['id'];
                /*--}}
                <div class="col-md-3 padding">
                    <div class="portlet box blue">
                        <div class="portlet-title">
                            <div class="caption">
                                {{--*/
                                $checkedCategory = !empty($userTypeCategoryLinks[$categoryId]) && count($userTypeCategoryLinks[$categoryId]) == count($category['Links']) ? true : false;
                                /*--}}
                                {!! Form::checkbox("category[]", collect($category)['id'],$checkedCategory) !!}<small>{{$categoryName}}</small>
                            </div>
                            <div class="tools">
                                <a class="collapse" href="javascript:;" data-original-title="" title=""></a>
                            </div>
                        </div>
                        <div class="portlet-body">
                            @foreach($category['Links'] as $k=>$link)
                            {{--*/
                            $linkInfo = collect($link);
                            $linkName = collect($link)['link_name'];
                            $linkId = collect($link)['id'];
                            $linkCategoryId = collect($link)['link_category_id'];
                            /*--}}
                            @if(empty($userTypeLinksData))
                            <p>{!! Form::checkbox("links[]", $linkId,null) !!} {{$linkName}}</p>
                            @else
                            {{--*/
                            $checkedLinks = in_array($linkId, $userTypeLinksData) ? true : false;
                            /*--}}
                            <p>{!! Form::checkbox("links[]", $linkId,$checkedLinks) !!} {{$linkName}}</p>
                            @endif
                            @endforeach
                        </div>
                    </div>
                </div>
                @endif
                @endforeach
            </div>
            @endif
        </div>
        <div class="form-actions">
            <div class="row">
                <div class="col-md-6">
                    <div class="row">
                        <div class="col-md-offset-3 col-md-9">
                            <button type="submit" class="btn green submit-btn">Submit</button>
                            <button type="button" class="btn default btn-collapse-form reset-form">Reset</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
