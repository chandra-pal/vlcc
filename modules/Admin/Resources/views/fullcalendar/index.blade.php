@extends('admin::layouts.master')
@section('template-level-scripts')
@parent
<script src="//cdnjs.cloudflare.com/ajax/libs/moment.js/2.9.0/moment.min.js"></script>
<script src="//cdnjs.cloudflare.com/ajax/libs/fullcalendar/2.2.7/fullcalendar.min.js"></script>
<link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/fullcalendar/2.2.7/fullcalendar.min.css"/>
@stop

@section('scripts')
@parent
<script type="text/javascript">
    jQuery(document).ready(function () {
        jQuery('#fullcalendar').fullCalendar({
            "header": {
                "left": "prev,next today",
                "center": "title",
                "right": "month,agendaWeek,agendaDay"
            },
            "firstDay": 1,
            "locale": "nl",
            "weekNumbers": true,
            "selectable": true,
            "defaultView": "agendaWeek",
            "eventClick": function (event, jsEvent, view) {
                console.log(event);
            },
            "viewRender": function (view, element) {
                console.log("View " + view.name + " rendered");
            },
            "events": [{
                    "id": 0,
                    "title": "Rest",
                    "allDay": true,
                    "start": "2017-07-29 11:26:49",
                    "end": "2017-07-29 00:00:00",
                    "url": null,
                    "className": null,
                    "editable": false,
                    "startEditable": false,
                    "durationEditable": false,
                    "rendering": null,
                    "overlap": true,
                    "constraint": null,
                    "source": null,
                    "color": null,
                    "backgroundColor": null,
                    "borderColor": null,
                    "textColor": null
                },
                {
                    "id": 1,
                    "title": "Appointment #303",
                    "allDay": false,
                    "start": "2017-07-15T13:00:00+00:00",
                    "end": "2017-07-15T15:00:00+00:00",
                    "url": null,
                    "className": null,
                    "editable": false,
                    "startEditable": false,
                    "durationEditable": false,
                    "rendering": null,
                    "overlap": true,
                    "constraint": null,
                    "source": null,
                    "color": null,
                    "backgroundColor": null,
                    "borderColor": null,
                    "textColor": null,
                },
                {
                    "id": 2,
                    "title": "Appointment #894",
                    "allDay": false,
                    "start": "2017-07-16T10:00:00+00:00",
                    "end": "2017-07-16T13:00:00+00:00",
                    "url": null,
                    "className": null,
                    "editable": true,
                    "startEditable": true,
                    "durationEditable": true,
                    "rendering": null,
                    "overlap": true,
                    "constraint": null,
                    "source": null,
                    "color": null,
                    "backgroundColor": null,
                    "borderColor": null,
                    "textColor": null,
                }]
        });
    });
</script>
@stop
@section('content')
{{--*/ $linkIcon = \Modules\Admin\Services\Helper\MenuHelper::getSelectedPageLinkIcon() /*--}}
<div class="portlet light col-lg-12">
    <div class="portlet-title">
        <div class="caption">
            <i class="fa {{$linkIcon}} font-blue-sharp"></i>
            <span class="caption-subject font-blue-sharp bold uppercase"></span>
        </div>       
    </div>
    <div class="portlet-body">
        <div id="fullcalendar"></div>       
    </div>
</div>
@stop
