<?php

/**
 * The class for managing food specific actions.
 * 
 * 
 * @author Gauri Deshmukh <gaurid@iprogrammer.com>
 * @package Admin
 * @since 1.0
 */

namespace Modules\Admin\Http\Controllers;

use Auth;
use Datatables;
use Illuminate\Support\Str;
use Carbon\Carbon;
class FullCalendarController extends Controller {

    /**
     * The FoodRepository instance.
     *
     * @var Modules\Admin\Repositories\FoodRepository
     */
    protected $repository;

    /**
     * Create a new FoodController instance.
     *
     * @param  Modules\Admin\Repositories\FoodRepository $repository
     * @return void
     */
    public function __construct() {
        parent::__construct();
        $this->middleware('acl');
    }

    /**
     * Display a listing of the resource.
     *
     * @return view as response
     */
    public function index() {
        // Generate a new fullcalendar instance
        $calendar = new \Edofre\Fullcalendar\Fullcalendar();

        // You can manually add the objects as an array
        $events = $this->getEvents();
        $calendar->setEvents($events);
        // Or you can add a route and return the events using an ajax requests that returns the events as json
//        $calendar->setEvents(route('fullcalendar-ajax-events'));
        // Set options
        $calendar->setOptions([
            'locale' => 'nl',
            'weekNumbers' => true,
            'selectable' => true,
            'defaultView' => 'agendaWeek',
            // Add the callbacks
            'eventClick' => new \Edofre\Fullcalendar\JsExpression("
                function(event, jsEvent, view) {
                    console.log(event);
                }
            "),
            'viewRender' => new \Edofre\Fullcalendar\JsExpression("
                function( view, element ) {
                    console.log(\"View \"+view.name+\" rendered\");
                }
            "),
        ]);

        // Check out the documentation for all the options and callbacks.
        // https://fullcalendar.io/docs/
        return view('admin::fullcalendar.index', ['calendar' => $calendar]);
    }

    /**
     * @param Request $request
     * @return string
     */
    public function ajaxEvents(Request $request) {
        // start and end dates will be sent automatically by fullcalendar, they can be obtained using:
        // $request->get('start') & $request->get('end')
        $events = $this->getEvents();
        return json_encode($events);
    }

    /**
     * @return array
     */
    private function getEvents() {
        $events = [];
        $events[] = new \Edofre\Fullcalendar\Event([
            'id' => 0,
            'title' => 'Rest',
            'allDay' => true,
            'start' => Carbon::create(2017, 07, 29),
            'end' => Carbon::create(2017, 07, 29),
        ]);

        $events[] = new \Edofre\Fullcalendar\Event([
            'id' => 1,
            'title' => 'Appointment #' . rand(1, 999),
            'start' => Carbon::create(2017, 07, 15, 13),
            'end' => Carbon::create(2017, 07, 15, 13)->addHour(2),
        ]);

        $events[] = new \Edofre\Fullcalendar\Event([
            'id' => 2,
            'title' => 'Appointment #' . rand(1, 999),
            'editable' => true,
            'startEditable' => true,
            'durationEditable' => true,
            'start' => Carbon::create(2017, 07, 16, 10),
            'end' => Carbon::create(2017, 07, 16, 13),
        ]);

        $events[] = new \Edofre\Fullcalendar\Event([
            'id' => 3,
            'title' => 'Appointment #' . rand(1, 999),
            'editable' => true,
            'startEditable' => true,
            'durationEditable' => true,
            'start' => Carbon::create(2017, 07, 14, 9),
            'end' => Carbon::create(2017, 07, 14, 10),
            'backgroundColor' => 'black',
            'borderColor' => 'red',
            'textColor' => 'green',
        ]);
        return $events;
    }

}
