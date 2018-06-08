<?php

/**
 * The class for managing food specific actions.
 *
 *
 * @author Sachin Gend <saching@iprogrammer.com>
 * @package Admin
 * @since 1.0
 */

namespace Modules\Admin\Http\Controllers;

use Auth;
use Datatables;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Modules\Admin\Models\Availability;
use Modules\Admin\Repositories\AvailabilityRepository;
use Modules\Admin\Http\Requests\AvailabilityCreateRequest;
use Modules\Admin\Http\Requests\AvailabilityUpdateRequest;
use Modules\Admin\Http\Requests\AvailabilityDeleteRequest;
use Modules\Admin\Services\Helper\ConfigConstantHelper;

class AvailabilityController extends Controller {

    /**
     * The AvailabilityRepository instance.
     *
     * @var Modules\Admin\Repositories\AvailabilityRepository
     */
    protected $repository;

    /**
     * Create a new AvailabilityController instance.
     *
     * @param  Modules\Admin\Repositories\AvailabilityRepository $repository
     * @return void
     */
    public function __construct(AvailabilityRepository $repository) {
        parent::__construct();
        $this->middleware('acl');
        $this->repository = $repository;
    }

    /**
     * Display a listing of the resource.
     *
     * @return view as response
     */
    public function index() {
        return view('admin::availability.index');
    }

    /**
     * Display a listing of the resource.
     *
     * @return datatable
     */
    public function getData(Request $request) {
        $availabilities = $this->repository->data();
        //filter to display own records
        if (Auth::guard('admin')->user()->hasOwnView && (empty(Auth::guard('admin')->user()->hasEdit) || empty(Auth::guard('admin')->user()->hasDelete))) {
            $availabilities = $availabilities->filter(function ($row) {
                return (Str::equals($row['created_by'], Auth::guard('admin')->user()->id)) ? true : false;
            });
        }

        return Datatables::of($availabilities)
                        ->addColumn('start_time', function ($availability) {
                            return date('h:i A', strtotime($availability->start_time));
                        })
                        ->addColumn('end_time', function ($availability) {
                            return date('h:i A', strtotime($availability->end_time));
                        })
                        ->addColumn('break_time', function ($availability) {
                            return date('h:i A', strtotime($availability->break_time));
                        })
                        ->addColumn('availability_date', function ($availability) {
                            return date('d-M-Y', strtotime($availability->availability_date));
                        })
                        ->addColumn('carry_forward_availability', function ($availability) {
                            $status = ($availability->carry_forward_availability == 1) ? trans('admin::controller/availability.yes') : trans('admin::controller/availability.no');
                            return $status;
                        })
                        ->addColumn('status', function ($availability) {
                            $status = ($availability->status == 1) ? '<span class="label label-sm label-success">' . trans('admin::messages.active') . '</span>' : '<span class="label label-sm label-danger">' . trans('admin::messages.inactive') . '</span>';
                            return $status;
                        })
                        ->addColumn('action', function ($availability) {
                            $actionList = '';
                            if (!empty(Auth::guard('admin')->user()->hasEdit) || (!empty(Auth::guard('admin')->user()->hasOwnEdit) && ($availability->created_by == Auth::guard('admin')->user()->id))) {
                                $actionList = '<a href="javascript:;" id="' . $availability->id . '" data-action="edit" data-id="' . $availability->id . '" id="' . $availability->id . '" class="btn btn-xs default margin-bottom-5 yellow-gold edit-form-link" title="Edit"><i class="fa fa-pencil"></i></a>';
                            }
                            if (!empty(Auth::guard('admin')->user()->hasDelete) || (!empty(Auth::guard('admin')->user()->hasOwnDelete) && ($availability->created_by == Auth::guard('admin')->user()->id))) {
                                $actionList .= '<a href="javascript:;" data-message="' . trans('admin::messages.delete-confirm') . '" data-action="delete" data-id="' . $availability->id . '" class="btn btn-xs default red-thunderbird margin-bottom-5 delete" title="' . trans('admin::messages.delete') . '"><i class="fa fa-trash-o"></i></a>';
                            }
                            return $actionList;
                        })
                        ->make(true);
    }

    /**
     * Display a form to create new availability.
     *
     * @return view as response
     */
    public function create() {
        return view('admin::availability.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  Modules\Admin\Http\Requests\AvailabilityCreateRequest $request
     * @return json encoded Response
     */
    public function store(AvailabilityCreateRequest $request) {
        $response = $this->repository->create($request->all());
        return response()->json($response);
    }

    /**
     * Show the form for editing the specified availability.
     *
     * @param  Modules\Admin\Models\Availability $availability
     * @return json encoded Response
     */
    public function edit(Availability $availability) {
        $attributes['availability_date'] = date('d-m-Y', strtotime($availability['attributes']['availability_date']));
        $attributes['start_time'] = date('h:i A', strtotime($availability['attributes']['start_time']));
        $attributes['end_time'] = date('h:i A', strtotime($availability['attributes']['end_time']));
        $attributes['break_time'] = date('h:i A', strtotime($availability['attributes']['break_time']));
        $attributes['carry_forward_availability'] = $availability['attributes']['carry_forward_availability'];
        $response['success'] = true;
        $response['attributes'] = $attributes;
        $response['form'] = view('admin::availability.edit', compact('availability'))->render();
        return response()->json($response);
    }

    /**
     * Store an updated resource in storage.
     *
     * @param  Modules\Admin\Http\Requests\AvailabilityUpdateRequest $request, Modules\Admin\Models\Availability $availability
     * @return json encoded Response
     */
    public function update(AvailabilityUpdateRequest $request, Availability $availability) {
        $response = $this->repository->update($request->all(), $availability);

        return response()->json($response);
    }

    /**
     * Delete resource from storage.
     *
     * @param  Modules\Admin\Http\Requests\AvailabilityDeleteRequest $request
     * @return json encoded Response
     */
    public function destroy(AvailabilityDeleteRequest $request, Availability $availability) {
        $response = [];
        $result = $this->repository->deleteAction($request->all());
        if ($result) {
            $response = ['status' => 'success', 'message' => trans('admin::messages.deleted', ['name' => trans('admin::controller/availability.availability')])];
        } else {
            $response = ['status' => 'fail', 'message' => trans('admin::messages.not-deleted', ['name' => trans('admin::controller/availability.availability')])];
        }

        return response()->json($response);
    }

    public function checkSessionTime(Request $request) {
        $request_data = $request->all();
        $param['time'] = $request_data['time'];
        $result = $this->repository->checkSessionTime($param);
    }

}
