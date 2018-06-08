<?php

/**
 * The class for managing machine availability specific actions.
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
use Modules\Admin\Models\MachineAvailability;
use Modules\Admin\Repositories\MachineAvailabilityRepository;
use Modules\Admin\Repositories\CenterRepository;
use Modules\Admin\Repositories\MachineRepository;
use Modules\Admin\Http\Requests\MachineAvailabilityCreateRequest;
use Modules\Admin\Http\Requests\MachineAvailabilityUpdateRequest;
use Modules\Admin\Http\Requests\MachineAvailabilityDeleteRequest;

class MachineAvailabilityController extends Controller {

    /**
     * The MachineAvailabilityRepository instance.
     *
     * @var Modules\Admin\Repositories\MachineAvailabilityRepository
     */
    protected $repository;
    protected $machineRepository;
    protected $centerRepository;

    /**
     * Create a new MachineAvailabilityController instance.
     *
     * @param  Modules\Admin\Repositories\MachineAvailabilityRepository $repository
     * @return void
     */
    public function __construct(MachineAvailabilityRepository $repository, MachineRepository $machineRepository, CenterRepository $centerRepository) {
        parent::__construct();
        $this->middleware('acl');
        $this->repository = $repository;
        $this->machineRepository = $machineRepository;
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
            $response['message'] = trans('admin::messages.deleted', ['name' => trans('admin::controller/machine-availability.machine-availability')]);
        } else {
            $response['status'] = 'fail';
            $response['message'] = trans('admin::messages.not-deleted', ['name' => trans('admin::controller/machine-availability.machine-availability')]);
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
        $machineList = [];
        $arrTimes['start_time'] = $serving = config('settings.APP_SESSION_BOOKING_START_TIME');
        $arrTimes['end_time'] = $serving = config('settings.APP_SESSION_BOOKING_END_TIME');
        return view('admin::machine-availability.index', compact('centerList', 'machineList', 'arrTimes', 'groupActions'));
    }

    /**
     * Display a listing of the resource.
     *
     * @return datatable
     */
    public function getData(Request $request) {
        $params['user_id'] = Auth::guard('admin')->user()->id;
        $centerId = $request->input('center_id');
        $machineID = $request->input('machine_id');
        $fromDate = $request->input('from_date');
        $toDate = $request->input('to_date');
        $params['center_id'] = $centerId;
        $params['machine_id'] = $machineID;
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
                        ->addColumn('ids', function ($machineAvailability) {
                            $checkbox = '';
                            if (!empty(Auth::guard('admin')->user()->hasEdit) || (!empty(Auth::guard('admin')->user()->hasOwnEdit) && ($machineAvailability->created_by == Auth::guard('admin')->user()->id))) {
                                $checkbox = '<input type="checkbox" name="ids[]" value="' . $machineAvailability['id'] . '">';
                            } else if (!empty(Auth::guard('admin')->user()->hasDelete) || (!empty(Auth::guard('admin')->user()->hasOwnDelete) && ($machineAvailability->created_by == Auth::guard('admin')->user()->id))) {
                                $checkbox = '<input type="checkbox" name="ids[]" value="' . $machineAvailability['id'] . '">';
                            }
                            return $checkbox;
                        })
                        ->addColumn('cname', function ($machineAvailability) {
                            return (!empty($machineAvailability['cname']) ? $machineAvailability['cname'] : '');
                        })
                        ->addColumn('mname', function ($machineAvailability) {
                            return (!empty($machineAvailability['mname']) ? $machineAvailability['mname'] : '');
                        })
                        ->addColumn('availability_date', function ($machineAvailability) {
                            return (!empty($machineAvailability['availability_date']) ? date('d-M-Y', strtotime($machineAvailability['availability_date'])) : '');
                        })
                        ->addColumn('start_time', function ($machineAvailability) {
                            return (!empty($machineAvailability['start_time']) ? date('h:i A', strtotime($machineAvailability['start_time'])) : '');
                        })
                        ->addColumn('end_time', function ($machineAvailability) {
                            return (!empty($machineAvailability['end_time']) ? date('h:i A', strtotime($machineAvailability['end_time'])) : '');
                        })
                        /* ->addColumn('break_time', function ($machineAvailability) {
                          return date('h:i A', strtotime($machineAvailability->break_time));
                          }) */
                        /* ->addColumn('carry_forward_availability', function ($machineAvailability) {
                          if ($machineAvailability['carry_forward_availability'] == 1) {
                          $cStatus = 'Yes';
                          } else {
                          $cStatus = 'No';
                          }
                          return $cStatus;
                          }) */
                        ->addColumn('status', function ($machineAvailability) {
                            $status = ($machineAvailability['status'] == 1) ? '<span class="label label-sm label-success">' . trans('admin::messages.active') . '</span>' : '<span class="label label-sm label-danger">' . trans('admin::messages.inactive') . '</span>';
                            return $status;
                        })
                        ->addColumn('action', function ($machineAvailability) {
                            $actionList = '';
                            if (!empty(Auth::guard('admin')->user()->hasEdit) || (!empty(Auth::guard('admin')->user()->hasOwnEdit) && ($machineAvailability->created_by == Auth::guard('admin')->user()->id))) {
                                $actionList = '<a href="javascript:;" id="' . $machineAvailability['id'] . '" data-action="edit" data-id="' . $machineAvailability['id'] . '" id="' . $machineAvailability['id'] . '" class="btn btn-xs default margin-bottom-5 yellow-gold edit-form-link" title="Edit"><i class="fa fa-pencil"></i></a>';
                            }
                            if (!empty(Auth::guard('admin')->user()->hasDelete) || (!empty(Auth::guard('admin')->user()->hasOwnDelete) && ($machineAvailability->created_by == Auth::guard('admin')->user()->id))) {
                                $actionList .= '<a href="javascript:;" data-message="' . trans('admin::messages.delete-confirm') . '" data-action="delete" data-id="' . $machineAvailability['id'] . '" class="btn btn-xs default red-thunderbird margin-bottom-5 delete" title="' . trans('admin::messages.delete') . '"><i class="fa fa-trash-o"></i></a>';
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

                            if ($request->has('machine_id')) {

                                $instance->collection = $instance->collection->filter(function ($row) use ($request) {
                                    return str_contains($row['machine_id'], $request->get('machine_id')) ? true : false;
                                });
                            }

//                            if ($request->has('availability_date')) {
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
     * Display a listing of the resource.(machines for the selected center)
     *
     * @return json encoded response
     */
    public function getMachineData($centerId) {
        $machineList = $this->machineRepository->listMachineData($centerId);
        $response['list'] = View('admin::machine-availability.machinedropdown', compact('machineList'))->render();
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
        $machineList = [];
        return view('admin::machine-availability.create', compact('centerList', 'machineList'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  Modules\Admin\Http\Requests\MachineAvailabilityCreateRequest $request
     * @return json encoded Response
     */
    public function store(MachineAvailabilityCreateRequest $request) {
        $response = $this->repository->create($request->all());
        return response()->json($response);
    }

    /**
     * Show the form for editing the specified machine availability.
     *
     * @param  Modules\Admin\Models\MachineAvailability $machineAvailability
     * @return json encoded Response
     */
    public function edit(MachineAvailability $machineAvailability) {
        $logged_in_user_id = Auth::guard('admin')->user()->id;
        $centerList = $this->centerRepository->listLoggedInUsersCenters($logged_in_user_id);
        $machineList = $this->machineRepository->listMachineData($machineAvailability->center_id);
        $arrTimes['start_time'] = $serving = config('settings.APP_SESSION_BOOKING_START_TIME');
        $arrTimes['end_time'] = $serving = config('settings.APP_SESSION_BOOKING_END_TIME');
        $attributes['availability_date'] = date('d-m-Y', strtotime($machineAvailability['attributes']['availability_date']));
        $attributes['start_time'] = date('h:i A', strtotime($machineAvailability['attributes']['start_time']));
        $attributes['end_time'] = date('h:i A', strtotime($machineAvailability['attributes']['end_time']));
        //$attributes['break_time'] = date('h:i A', strtotime($machineAvailability['attributes']['break_time']));
        $attributes['carry_forward_availability'] = $machineAvailability['attributes']['carry_forward_availability'];
        $response['success'] = true;
        $response['attributes'] = $attributes;
        $response['form'] = view('admin::machine-availability.edit', compact('machineAvailability', 'centerList', 'machineList', 'arrTimes'))->render();
        return response()->json($response);
    }

    /**
     * Store an updated resource in storage.
     *
     * @param  Modules\Admin\Http\Requests\MachineAvailabilityUpdateRequest $request, Modules\Admin\Models\MachineAvailability $machineAvailability
     * @return json encoded Response
     */
    public function update(MachineAvailabilityUpdateRequest $request, MachineAvailability $machineAvailability) {
        $response = $this->repository->update($request->all(), $machineAvailability);
        return response()->json($response);
    }

    /**
     * Delete resource from storage.
     *
     * @param  Modules\Admin\Http\Requests\MachineAvailabilityDeleteRequest $request
     * @return json encoded Response
     */
    public function destroy(MachineAvailabilityDeleteRequest $request, MachineAvailability $machineAvailability) {
        $response = [];
        $result = $this->repository->deleteAction($request->all());
        if ($result) {
            $response = ['status' => 'success', 'message' => trans('admin::messages.deleted', ['name' => trans('admin::controller/machine-availability.machine-availability')])];
        } else {
            $response = ['status' => 'fail', 'message' => trans('admin::messages.not-deleted', ['name' => trans('admin::controller/machine-availability.machine-availability')])];
        }

        return response()->json($response);
    }

}
