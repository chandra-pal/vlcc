<?php

/**
 * The class for managing staff availability specific actions.
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
use Modules\Admin\Models\StaffAvailability;
use Modules\Admin\Repositories\StaffAvailabilityRepository;
use Modules\Admin\Repositories\CenterRepository;
use Modules\Admin\Repositories\UserRepository;
use Modules\Admin\Http\Requests\StaffAvailabilityCreateRequest;
use Modules\Admin\Http\Requests\StaffAvailabilityUpdateRequest;
use Modules\Admin\Http\Requests\StaffAvailabilityDeleteRequest;

class StaffAvailabilityController extends Controller {

    /**
     * The StaffAvailabilityRepository instance.
     *
     * @var Modules\Admin\Repositories\StaffAvailabilityRepository
     */
    protected $repository;
    protected $userRepository;
    protected $centerRepository;

    /**
     * Create a new StaffAvailabilityController instance.
     *
     * @param  Modules\Admin\Repositories\StaffAvailabilityController $repository
     * @return void
     */
    public function __construct(StaffAvailabilityRepository $repository, UserRepository $userRepository, CenterRepository $centerRepository) {
        parent::__construct();
        $this->middleware('acl');
        $this->repository = $repository;
        $this->userRepository = $userRepository;
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
            $response['message'] = trans('admin::messages.deleted', ['name' => trans('admin::controller/staff-availability.staff-availability')]);
        } else {
            $response['status'] = 'fail';
            $response['message'] = trans('admin::messages.not-deleted', ['name' => trans('admin::controller/staff-availability.staff-availability')]);
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
        $staffList = [];
        $arrTimes['start_time'] = $serving = config('settings.APP_SESSION_BOOKING_START_TIME');
        $arrTimes['end_time'] = $serving = config('settings.APP_SESSION_BOOKING_END_TIME');
        return view('admin::staff-availability.index', compact('centerList', 'staffList', 'arrTimes', 'groupActions'));
    }

    /**
     * Display a listing of the resource.
     *
     * @return datatable
     */
    public function getData(Request $request) {
        $params['user_id'] = Auth::guard('admin')->user()->id;
        $centerId = $request->input('center_id');
        $staffID = $request->input('staff_id');
        $fromDate = $request->input('from_date');
        $toDate = $request->input('to_date');
        $params['center_id'] = $centerId;
        $params['staff_id'] = $staffID;
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
                        ->addColumn('ids', function ($staffAvailability) {
                            $checkbox = '';
                            if (!empty(Auth::guard('admin')->user()->hasEdit) || (!empty(Auth::guard('admin')->user()->hasOwnEdit) && ($staffAvailability->created_by == Auth::guard('admin')->user()->id))) {
                                $checkbox = '<input type="checkbox" name="ids[]" value="' . $staffAvailability['id'] . '">';
                            } else if (!empty(Auth::guard('admin')->user()->hasDelete) || (!empty(Auth::guard('admin')->user()->hasOwnDelete) && ($staffAvailability->created_by == Auth::guard('admin')->user()->id))) {
                                $checkbox = '<input type="checkbox" name="ids[]" value="' . $staffAvailability['id'] . '">';
                            }
                            return $checkbox;
                        })
                        ->addColumn('scname', function ($staffAvailability) {
                            return (!empty($staffAvailability['scname']) ? $staffAvailability['scname'] : '');
                        })
                        ->addColumn('sname', function ($staffAvailability) {
                            return (!empty($staffAvailability['sname']) ? $staffAvailability['sname'] : '');
                        })
                        ->addColumn('availability_date', function ($staffAvailability) {
                            return (!empty($staffAvailability['availability_date']) ? date('d-M-Y', strtotime($staffAvailability['availability_date'])) : '');
                        })
                        ->addColumn('start_time', function ($staffAvailability) {
                            return (!empty($staffAvailability['start_time']) ? date('h:i A', strtotime($staffAvailability['start_time'])) : '');
                        })
                        ->addColumn('end_time', function ($staffAvailability) {
                            return (!empty($staffAvailability['end_time']) ? date('h:i A', strtotime($staffAvailability['end_time'])) : '');
                        })
                        ->addColumn('break_time', function ($staffAvailability) {
                            //return (!empty($staffAvailability['break_time']) ? date('h:i A', strtotime($staffAvailability['break_time'])) : '');
                            if ($staffAvailability['break_time'] == '00:00:00') {
                                $breakTimeRet = 'N/A';
                            } else {
                                $breakTimeRet = date('h:i A', strtotime($staffAvailability['break_time']));
                            }
                            return $breakTimeRet;
                            // return (!empty($staffAvailability['break_time']) ? $staffAvailability['break_time'] : '');
                        })
                        /* ->addColumn('carry_forward_availability', function ($staffAvailability) {
                          if ($staffAvailability['carry_forward_availability'] == 1) {
                          $cStatus = 'Yes';
                          } else {
                          $cStatus = 'No';
                          }
                          return $cStatus;
                          }) */
                        ->addColumn('status', function ($staffAvailability) {
                            $status = ($staffAvailability['status'] == 1) ? '<span class="label label-sm label-success">' . trans('admin::messages.active') . '</span>' : '<span class="label label-sm label-danger">' . trans('admin::messages.inactive') . '</span>';
                            return $status;
                        })
                        ->addColumn('action', function ($staffAvailability) {
                            $actionList = '';
                            if (!empty(Auth::guard('admin')->user()->hasEdit) || (!empty(Auth::guard('admin')->user()->hasOwnEdit) && ($staffAvailability->created_by == Auth::guard('admin')->user()->id))) {
                                $actionList = '<a href="javascript:;" id="' . $staffAvailability['id'] . '" data-action="edit" data-id="' . $staffAvailability['id'] . '" id="' . $staffAvailability['id'] . '" class="btn btn-xs default margin-bottom-5 yellow-gold edit-form-link" title="Edit"><i class="fa fa-pencil"></i></a>';
                            }
                            if (!empty(Auth::guard('admin')->user()->hasDelete) || (!empty(Auth::guard('admin')->user()->hasOwnDelete) && ($staffAvailability->created_by == Auth::guard('admin')->user()->id))) {
                                $actionList .= '<a href="javascript:;" data-message="' . trans('admin::messages.delete-confirm') . '" data-action="delete" data-id="' . $staffAvailability['id'] . '" class="btn btn-xs default red-thunderbird margin-bottom-5 delete" title="' . trans('admin::messages.delete') . '"><i class="fa fa-trash-o"></i></a>';
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
                                    return str_contains(strtolower($row['center_id']), strtolower($request->get('center_id'))) ? true : false;
                                    // return Str::equals($row['center_id'], $request->get('center_id')) ? true : false;
                                });
                            }

                            if ($request->has('staff_id')) {

                                $instance->collection = $instance->collection->filter(function ($row) use ($request) {
                                    return str_contains(strtolower($row['staff_id']), strtolower($request->get('staff_id'))) ? true : false;
                                });
                            }

//                            if ($request->has('availability_date')) {
//
//                                $instance->collection = $instance->collection->filter(function ($row) use ($request) {
//                                    return str_contains(strtolower($row['availability_date']), strtolower($request->get('availability_date'))) ? true : false;
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
     * Display a listing of the resource.(staff for the selected center)
     *
     * @return json encoded response
     */
    public function getStaffData($centerId) {
        $staffList = $this->userRepository->listStaffData($centerId);
        $response['list'] = View('admin::staff-availability.staffdropdown', compact('staffList'))->render();
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
        $staffList = [];
        return view('admin::staff-availability.create', compact('centerList', 'staffList'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  Modules\Admin\Http\Requests\StaffAvailabilityCreateRequest $request
     * @return json encoded Response
     */
    public function store(StaffAvailabilityCreateRequest $request) {
        $response = $this->repository->create($request->all());
        return response()->json($response);
    }

    /**
     * Show the form for editing the specified staff availability.
     *
     * @param  Modules\Admin\Models\StaffAvailability $staffAvailability
     * @return json encoded Response
     */
    public function edit(StaffAvailability $staffAvailability) {
        $logged_in_user_id = Auth::guard('admin')->user()->id;
        $centerList = $this->centerRepository->listLoggedInUsersCenters($logged_in_user_id);
        $staffList = $this->userRepository->listStaffData($staffAvailability->center_id);
        $arrTimes['start_time'] = $serving = config('settings.APP_SESSION_BOOKING_START_TIME');
        $arrTimes['end_time'] = $serving = config('settings.APP_SESSION_BOOKING_END_TIME');
        $attributes['availability_date'] = date('d-m-Y', strtotime($staffAvailability['attributes']['availability_date']));
        $attributes['start_time'] = date('h:i A', strtotime($staffAvailability['attributes']['start_time']));
        $attributes['end_time'] = date('h:i A', strtotime($staffAvailability['attributes']['end_time']));
        $attributes['break_time'] = date('h:i A', strtotime($staffAvailability['attributes']['break_time']));
        $attributes['carry_forward_availability'] = $staffAvailability['attributes']['carry_forward_availability'];
        $response['success'] = true;
        $response['attributes'] = $attributes;
        $response['form'] = view('admin::staff-availability.edit', compact('staffAvailability', 'centerList', 'staffList', 'arrTimes'))->render();
        return response()->json($response);
    }

    /**
     * Store an updated resource in storage.
     *
     * @param  Modules\Admin\Http\Requests\StaffAvailabilityUpdateRequest $request, Modules\Admin\Models\StaffAvailability $staffAvailability
     * @return json encoded Response
     */
    public function update(StaffAvailabilityUpdateRequest $request, StaffAvailability $staffAvailability) {
        $response = $this->repository->update($request->all(), $staffAvailability);

        return response()->json($response);
    }

    /**
     * Delete resource from storage.
     *
     * @param  Modules\Admin\Http\Requests\StaffAvailabilityDeleteRequest $request
     * @return json encoded Response
     */
    public function destroy(StaffAvailabilityDeleteRequest $request, StaffAvailability $staffAvailability) {
        $response = [];
        $result = $this->repository->deleteAction($request->all());
        if ($result) {
            $response = ['status' => 'success', 'message' => trans('admin::messages.deleted', ['name' => trans('admin::controller/staff-availability.staff-availability')])];
        } else {
            $response = ['status' => 'fail', 'message' => trans('admin::messages.not-deleted', ['name' => trans('admin::controller/staff-availability.staff-availability')])];
        }

        return response()->json($response);
    }

}
