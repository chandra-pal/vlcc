<?php

/**
 * The class for managing Diet Schedule Type specific actions.
 *
 *
 * @author Gauri Deshmukh <gaurid@iprogrammer.com>
 * @package Admin
 * @since 1.0
 */

namespace Modules\Admin\Http\Controllers;

use Auth;
use Datatables;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Modules\Admin\Models\DietScheduleType;
use Modules\Admin\Repositories\DietScheduleTypeRepository;
use Modules\Admin\Http\Requests\DietScheduleTypeCreateRequest;
use Modules\Admin\Http\Requests\DietScheduleTypeUpdateRequest;
use Modules\Admin\Http\Requests\DietScheduleTypeDeleteRequest;
use Modules\Admin\Services\Helper\ConfigConstantHelper;

class DietScheduleTypeController extends Controller {

    /**
     * The DietScheduleTypeRepository instance.
     *
     * @var Modules\Admin\Repositories\DietScheduleTypeRepository
     */
    protected $repository;

    /**
     * Create a new DietScheduleTypeController instance.
     *
     * @param  Modules\Admin\Repositories\dietScheduleTypeRepository $repository
     * @return void
     */
    public function __construct(DietScheduleTypeRepository $repository) {
        parent::__construct();
        $this->middleware('acl');
        $this->repository = $repository;
    }

    /**
     * List all the data
     * @return response
     */
    public function index() {
        $dietlist = $this->repository->listScheduleTypes()->toArray();
        return view('admin::diet-schedule-type.index', compact('dietlist'));
    }

    /**
     * Display a listing of the resource.
     *
     * @return datatable
     */
    public function getData(Request $request) {
        $diets = $this->repository->data();

        //filter to display own records
        if (Auth::guard('admin')->user()->hasOwnView && (empty(Auth::guard('admin')->user()->hasEdit) || empty(Auth::guard('admin')->user()->hasDelete))) {
            $diets = $diets->filter(function ($row) {
                return (Str::equals($row['created_by'], Auth::guard('admin')->user()->id)) ? true : false;
            });
        }

        return Datatables::of($diets)
                        ->addColumn('status', function ($diet) {
                            $status = ($diet->status == 1) ? '<span class="label label-sm label-success">' . trans('admin::messages.active') . '</span>' : '<span class="label label-sm label-danger">' . trans('admin::messages.inactive') . '</span>';
                            return $status;
                        })
                        ->addColumn('start_time', function ($diet) {
                            $start_time = '';
                            $start_time = date("h:i A", strtotime($diet->start_time));
                            return $start_time;
                        })
                        ->addColumn('end_time', function ($diet) {
                            $end_time = '';
                            $end_time = date("h:i A", strtotime($diet->end_time));
                            return $end_time;
                        })
                        ->addColumn('action', function ($diet) {
                            $actionList = '';
                            if (!empty(Auth::guard('admin')->user()->hasEdit) || (!empty(Auth::guard('admin')->user()->hasOwnEdit) && ($diet->created_by == Auth::guard('admin')->user()->id))) {
                                $actionList = '<a href="javascript:;" id="' . $diet->id . '" data-action="edit" data-id="' . $diet->id . '" id="' . $diet->id . '" class="btn btn-xs default margin-bottom-5 yellow-gold edit-form-link" title="Edit"><i class="fa fa-pencil"></i></a>';
                            }
                            return $actionList;
                        })
                        ->make(true);
    }

    /**
     * Display a form to create new diet schedule.
     *
     * @return view as response
     */
    public function create() {
        return view('admin::diet-schedule-type.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  Modules\Admin\Http\Requests\DietScheduleTypeCreateRequest $request
     * @return json encoded Response
     */
    public function store(DietScheduleTypeCreateRequest $request) {

        $response = $this->repository->create($request->all());

        return response()->json($response);
    }

    /**
     * Show the form for editing the specified diet schedule type.
     *
     * @param  Modules\Admin\Models\DietScheduleType $dietScheduleType
     * @return json encoded Response
     */
    public function edit(DietScheduleType $dietScheduleType) {
        $response['success'] = true;
        $response['form'] = view('admin::diet-schedule-type.edit', compact('dietScheduleType'))->render();

        return response()->json($response);
    }

    /**
     * Store an updated resource in storage.
     *
     * @param  Modules\Admin\Http\Requests\DietScheduleTypeUpdateRequest $request, Modules\Admin\Models\DietScheduleType $dietScheduleType
     * @return json encoded Response
     */
    public function update(DietScheduleTypeUpdateRequest $request, DietScheduleType $dietScheduleType) {
        $response = $this->repository->update($request->all(), $dietScheduleType);

        return response()->json($response);
    }

    /**
     * Delete resource from storage.
     *
     * @param  Modules\Admin\Http\Requests\DietScheduleTypeDeleteRequest $request
     * @return json encoded Response
     */
    public function destroy(DietScheduleTypeDeleteRequest $request, DietScheduleType $dietScheduleType) {
        $response = [];
        $result = $this->repository->deleteAction($request->all());
        if ($result) {
            $response = ['status' => 'success', 'message' => trans('admin::messages.deleted', ['name' => trans('admin::controller/diet-schedule-type.diet-schedule-type')])];
        } else {
            $response = ['status' => 'fail', 'message' => trans('admin::messages.not-deleted', ['name' => trans('admin::controller/diet-schedule-type.diet-schedule-type')])];
        }

        return response()->json($response);
    }

}
