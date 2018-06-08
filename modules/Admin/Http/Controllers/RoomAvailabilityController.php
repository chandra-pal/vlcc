<?php

/**
 * The class for managing room availability specific actions.
 *
 *
 * @author Bhawna Thadhani <bhawnat@iprogrammer.com>
 * @package Admin
 * @since 1.0
 */

namespace Modules\Admin\Http\Controllers;

use Auth;
use Datatables;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Modules\Admin\Models\RoomAvailability;
use Modules\Admin\Repositories\RoomAvailabilityRepository;
use Modules\Admin\Repositories\RoomRepository;
use Modules\Admin\Repositories\CenterRepository;
use Modules\Admin\Http\Requests\RoomAvailabilityCreateRequest;
use Modules\Admin\Http\Requests\RoomAvailabilityUpdateRequest;
use Modules\Admin\Http\Requests\RoomAvailabilityDeleteRequest;
use Modules\Admin\Services\Helper\ConfigConstantHelper;

class RoomAvailabilityController extends Controller {

    /**
     * The RoomAvailabilityRepository instance.
     *
     * @var Modules\Admin\Repositories\RoomAvailabilityRepository
     */
    protected $repository;
    protected $roomRepository;
    protected $centerRepository;

    /**
     * Create a new RoomAvailabilityController instance.
     *
     * @param  Modules\Admin\Repositories\RoomAvailabilityRepository $repository
     * @return void
     */
    public function __construct(RoomAvailabilityRepository $repository, RoomRepository $roomRepository, CenterRepository $centerRepository) {
        parent::__construct();
        $this->middleware('acl');
        $this->repository = $repository;
        $this->roomRepository = $roomRepository;
        $this->centerRepository = $centerRepository;
    }

    /**
     * Handle Ajax Group Action
     *
     * @param  Illuminate\Http\Request $request
     * @return Response
     */
    public function groupAction(Request $request) {
        $response = [];
        $result = $this->repository->groupAction($request->all());
        if ($result) {
            $response['status'] = 'success';
            $response['message'] = trans('admin::messages.deleted', ['name' => trans('admin::controller/room-availability.room-availability')]);
        } else {
            $response['status'] = 'fail';
            $response['message'] = trans('admin::messages.not-deleted', ['name' => trans('admin::controller/room-availability.room-availability')]);
        }
        return response()->json($response);
    }

    /**
     * Display a listing of the resource.
     *
     * @return view as response
     */
    public function index() {
        $groupActions = $this->repository->getGroupActionData();
        $logged_in_user_id = Auth::guard('admin')->user()->id;
        $centerList = $this->centerRepository->listLoggedInUsersCenters($logged_in_user_id);
        $roomList = [];
        $arrTimes['start_time'] = $serving = config('settings.APP_SESSION_BOOKING_START_TIME');
        $arrTimes['end_time'] = $serving = config('settings.APP_SESSION_BOOKING_END_TIME');
        return view('admin::room-availability.index', compact('centerList', 'roomList', 'arrTimes', 'groupActions'));
    }

    /**
     * Display a listing of the resource.
     *
     * @return datatable
     */
    public function getData(Request $request) {
        $params['user_id'] = Auth::guard('admin')->user()->id;
        $centerId = $request->input('center_id');
        $roomID = $request->input('room_id');
        $fromDate = $request->input('from_date');
        $toDate = $request->input('to_date');
        $params['center_id'] = $centerId;
        $params['room_id'] = $roomID;
        $params['from_date'] = $fromDate;
        $params['to_date'] = $toDate;

        $data = $this->repository->data($params, $request->all());

        //filter to display own records
        if (Auth::guard('admin')->user()->hasOwnView && (empty(Auth::guard('admin')->user()->hasEdit) || empty(Auth::guard('admin')->user()->hasDelete))) {
            $data = $data->filter(function ($row) {
                return (Str::equals($row['created_by'], Auth::guard('admin')->user()->id)) ? true : false;
            });
        }

        return Datatables::of($data)
                        ->addColumn('ids', function ($roomAvailability) {
                            $checkbox = '';
                            if (!empty(Auth::guard('admin')->user()->hasEdit) || (!empty(Auth::guard('admin')->user()->hasOwnEdit) && ($roomAvailability->created_by == Auth::guard('admin')->user()->id))) {
                                $checkbox = '<input type="checkbox" name="ids[]" value="' . $roomAvailability['id'] . '">';
                            } else if (!empty(Auth::guard('admin')->user()->hasDelete) || (!empty(Auth::guard('admin')->user()->hasOwnDelete) && ($roomAvailability->created_by == Auth::guard('admin')->user()->id))) {
                                $checkbox = '<input type="checkbox" name="ids[]" value="' . $roomAvailability['id'] . '">';
                            }
                            return $checkbox;
                        })
                        ->addColumn('rcname', function ($roomAvailability) {
                            return (!empty($roomAvailability['rcname']) ? $roomAvailability['rcname'] : '');
                        })
                        ->addColumn('rname', function ($roomAvailability) {
                            return (!empty($roomAvailability['rname']) ? $roomAvailability['rname'] : '');
                        })
                        ->addColumn('availability_date', function ($roomAvailability) {
                            return (!empty($roomAvailability['availability_date']) ? date('d-M-Y', strtotime($roomAvailability['availability_date'])) : '');
                        })
                        ->addColumn('start_time', function ($roomAvailability) {
                            return (!empty($roomAvailability['start_time']) ? date('h:i A', strtotime($roomAvailability['start_time'])) : '');
                        })
                        ->addColumn('end_time', function ($roomAvailability) {
                            return (!empty($roomAvailability['end_time']) ? date('h:i A', strtotime($roomAvailability['end_time'])) : '');
                        })
                        /* ->addColumn('break_time', function ($roomAvailability) {
                          return date('h:i A', strtotime($roomAvailability->break_time));
                          }) */
                        /* ->addColumn('carry_forward_availability', function ($roomAvailability) {
                          if ($roomAvailability['carry_forward_availability'] == 1) {
                          $cStatus = 'Yes';
                          } else {
                          $cStatus = 'No';
                          }
                          return $cStatus;
                          }) */
                        ->addColumn('status', function ($roomAvailability) {
                            $status = ($roomAvailability['status'] == 1) ? '<span class="label label-sm label-success">' . trans('admin::messages.active') . '</span>' : '<span class="label label-sm label-danger">' . trans('admin::messages.inactive') . '</span>';
                            return $status;
                        })
                        ->addColumn('action', function ($roomAvailability) {
                            $actionList = '';
                            if (!empty(Auth::guard('admin')->user()->hasEdit) || (!empty(Auth::guard('admin')->user()->hasOwnEdit) && ($roomAvailability->created_by == Auth::guard('admin')->user()->id))) {
                                $actionList = '<a href="javascript:;" id="' . $roomAvailability['id'] . '" data-action="edit" data-id="' . $roomAvailability['id'] . '" id="' . $roomAvailability['id'] . '" class="btn btn-xs default margin-bottom-5 yellow-gold edit-form-link" title="Edit"><i class="fa fa-pencil"></i></a>';
                            }
                            if (!empty(Auth::guard('admin')->user()->hasDelete) || (!empty(Auth::guard('admin')->user()->hasOwnDelete) && ($roomAvailability->created_by == Auth::guard('admin')->user()->id))) {
                                $actionList .= '<a href="javascript:;" data-message="' . trans('admin::messages.delete-confirm') . '" data-action="delete" data-id="' . $roomAvailability['id'] . '" class="btn btn-xs default red-thunderbird margin-bottom-5 delete" title="' . trans('admin::messages.delete') . '"><i class="fa fa-trash-o"></i></a>';
                            }
                            return $actionList;
                        })
                        ->filter(function ($instance) use ($request) {
                            if (Auth::guard('admin')->user()->hasOwnView) {
                                $instance->collection = $instance->collection->filter(function ($row) {
                                    return (Str::equals($row['created_by'], Auth::guard('admin')->user()->id)) ? true : false;
                                });
                            }

                            if ($request->has('center_id')) {

                                $instance->collection = $instance->collection->filter(function ($row) use ($request) {
                                    return str_contains($row['center_id'], $request->get('center_id')) ? true : false;
                                });
                            }

                            if ($request->has('room_id')) {

                                $instance->collection = $instance->collection->filter(function ($row) use ($request) {
                                    return str_contains($row['room_id'], $request->get('room_id')) ? true : false;
                                });
                            }

//                            if ($request->has('availability_date')) {
//
//                                $instance->collection = $instance->collection->filter(function ($row) use ($request) {
//                                    return str_contains($row['availability_date'], $request->get('availability_date')) ? true : false;
//                                });
//                            }

                            if ($request->has('from_date') && $request->has('to_date')) {
                                $instance->collection = $instance->collection->filter(function ($row) use ($request) {
                                    $fromDateArray = $request->get('from_date');
                                    $toDateArray = $request->get('to_date');
                                    $fromDate = date('Y-m-d', strtotime($fromDateArray));
                                    $toDate = date('Y-m-d', strtotime($toDateArray));
                                    return $row['availability_date'] >= $fromDate && $row['availability_date'] <= $toDate ? true : false;
                                });
                            }
                        })
                        ->make(true);
    }

    /**
     * Display a listing of the resource.(rooms for the selected center)
     *
     * @return json encoded response
     */
    public function getRoomData($centerId) {
        $roomList = $this->roomRepository->listRoomData($centerId)->toArray();
        $response['list'] = View('admin::room-availability.roomdropdown', compact('roomList'))->render();
        return response()->json($response);
    }

    /**
     * Display a form to create new availability.
     *
     * @return view as response
     */
    public function create() {
        $logged_in_user_id = Auth::guard('admin')->user()->id;
        $centerList = $this->centerRepository->listLoggedInUsersCenters($logged_in_user_id);
        $roomList = [];
        return view('admin::room-availability.create', compact('centerList', 'roomList'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  Modules\Admin\Http\Requests\RoomAvailabilityCreateRequest $request
     * @return json encoded Response
     */
    public function store(RoomAvailabilityCreateRequest $request) {
        $response = $this->repository->create($request->all());
        return response()->json($response);
    }

    /**
     * Show the form for editing the specified room availability.
     *
     * @param  Modules\Admin\Models\RoomAvailability $roomAvailability
     * @return json encoded Response
     */
    public function edit(RoomAvailability $roomAvailability) {
        $logged_in_user_id = Auth::guard('admin')->user()->id;
        $centerList = $this->centerRepository->listLoggedInUsersCenters($logged_in_user_id);
        $roomList = $this->roomRepository->listRoomData($roomAvailability->center_id)->toArray();
        $arrTimes['start_time'] = $serving = config('settings.APP_SESSION_BOOKING_START_TIME');
        $arrTimes['end_time'] = $serving = config('settings.APP_SESSION_BOOKING_END_TIME');
        $attributes['availability_date'] = date('d-m-Y', strtotime($roomAvailability['attributes']['availability_date']));
        $attributes['start_time'] = date('h:i A', strtotime($roomAvailability['attributes']['start_time']));
        $attributes['end_time'] = date('h:i A', strtotime($roomAvailability['attributes']['end_time']));
        //$attributes['break_time'] = date('h:i A', strtotime($machineAvailability['attributes']['break_time']));
        $attributes['carry_forward_availability'] = $roomAvailability['attributes']['carry_forward_availability'];
        $response['success'] = true;
        $response['attributes'] = $attributes;
        $response['form'] = view('admin::room-availability.edit', compact('roomAvailability', 'centerList', 'roomList', 'arrTimes'))->render();
        return response()->json($response);
    }

    /**
     * Store an updated resource in storage.
     *
     * @param  Modules\Admin\Http\Requests\RoomAvailabilityUpdateRequest $request, Modules\Admin\Models\RoomAvailability $roomAvailability
     * @return json encoded Response
     */
    public function update(RoomAvailabilityUpdateRequest $request, RoomAvailability $roomAvailability) {
        $response = $this->repository->update($request->all(), $roomAvailability);

        return response()->json($response);
    }

    /**
     * Delete resource from storage.
     *
     * @param  Modules\Admin\Http\Requests\RoomAvailabilityDeleteRequest $request
     * @return json encoded Response
     */
    public function destroy(RoomAvailabilityDeleteRequest $request, RoomAvailability $roomAvailability) {
        $response = [];
        $result = $this->repository->deleteAction($request->all());
        if ($result) {
            $response = ['status' => 'success', 'message' => trans('admin::messages.deleted', ['name' => trans('admin::controller/room-availability.room-availability')])];
        } else {
            $response = ['status' => 'fail', 'message' => trans('admin::messages.not-deleted', ['name' => trans('admin::controller/room-availability.room-availability')])];
        }

        return response()->json($response);
    }

}
