<?php

/**
 * The class for managing activity type specific actions.
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
use Modules\Admin\Models\ActivityType;
use Modules\Admin\Repositories\ActivityTypeRepository;
use Modules\Admin\Http\Requests\ActivityTypeCreateRequest;
use Modules\Admin\Http\Requests\ActivityTypeUpdateRequest;
use Modules\Admin\Http\Requests\ActivityTypeDeleteRequest;

class ActivityTypeController extends Controller {

    /**
     * The ActivityTypeRepository instance.
     *
     * @var Modules\Admin\Repositories\ActivityTypeRepository
     */
    protected $repository;

    /**
     * Create a new ActivityTypeController instance.
     *
     * @param  Modules\Admin\Repositories\ActivityTypeRepository $repository
     * @return void
     */
    public function __construct(ActivityTypeRepository $repository) {
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
        return view('admin::activity-type.index');
    }

    /**
     * Get a listing of the resource.
     *
     * @return datatable
     */
    public function getData() {
        $activityType = $this->repository->data();
        //filter to display own records
        if (Auth::guard('admin')->user()->hasOwnView && (empty(Auth::guard('admin')->user()->hasEdit) || empty(Auth::guard('admin')->user()->hasDelete))) {
            $activityType = $activityType->filter(function ($row) {
                return (Str::equals($row['created_by'], Auth::guard('admin')->user()->id)) ? true : false;
            });
        }

        return Datatables::of($activityType)
                        ->addColumn('status', function ($activityType) {
                            $status = ($activityType->status == 1) ? '<span class="label label-sm label-success">' . trans('admin::messages.active') . '</span>' : '<span class="label label-sm label-danger">' . trans('admin::messages.inactive') . '</span>';
                            return $status;
                        })
                        ->addColumn('action', function ($activityType) {
                            $actionList = '';
                            if (!empty(Auth::guard('admin')->user()->hasEdit) || (!empty(Auth::guard('admin')->user()->hasOwnEdit) && ($activityType->created_by == Auth::guard('admin')->user()->id))) {
                                $actionList .= '<a href="javascript:;" data-action="edit" title="Edit" data-id="' . $activityType->id . '" class="btn btn-xs default margin-bottom-5 yellow-gold edit-form-link" title="Edit" id="' . $activityType->id . '"><i class="fa fa-pencil"></i></a>';
                            }
                            if (!empty(Auth::guard('admin')->user()->hasDelete) || (!empty(Auth::guard('admin')->user()->hasOwnDelete) && ($activityType->created_by == Auth::guard('admin')->user()->id))) {
                                $actionList .= '<a href="javascript:;" class="btn btn-xs default delete red-thunderbird margin-bottom-5" title="Delete" id =' . $activityType->id . ' created_by = ' . $activityType->created_by . ' ><i class="fa fa-trash-o"></i></a>';
                            }
                            return $actionList;
                        })
                        ->make(true);
    }

    /**
     * Display a form to create new activity type.
     *
     * @return view as response
     */
    public function create() {
        return view('admin::activity-type.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  Modules\Admin\Http\Requests\ActivityTypeCreateRequest $request
     * @return json encoded Response
     */
    public function store(ActivityTypeCreateRequest $request) {
        $response = $this->repository->create($request->all());

        return response()->json($response);
    }

    /**
     * Show the form for editing the specified activity type.
     *
     * @param  Modules\Admin\Models\ActivityType $activityType
     * @return json encoded Response
     */
    public function edit(ActivityType $activityType) {
        $response['success'] = true;
        $response['form'] = view('admin::activity-type.edit', compact('activityType'))->render();

        return response()->json($response);
    }

    /**
     * Store an updated resource in storage.
     *
     * @param  Modules\Admin\Http\Requests\ActivityTypeCreateRequest $request, Modules\Admin\Models\ActivityType $activityType
     * @return json encoded Response
     */
    public function update(ActivityTypeUpdateRequest $request, ActivityType $activityType) {
        $response = $this->repository->update($request->all(), $activityType);

        return response()->json($response);
    }

    /**
     * Delete resource from storage.
     *
     * @param  Modules\Admin\Http\Requests\ActivityTypeDeleteRequest $request
     * @return json encoded Response
     */
    public function destroy(ActivityTypeDeleteRequest $request, ActivityType $activityType) {
        $response = [];
        $result = $this->repository->deleteAction($request->all());
        if ($result) {
            $response = ['status' => 'success', 'message' => trans('admin::messages.deleted', ['name' => trans('admin::controller/activity-type.activity-type')])];
        } else {
            $response = ['status' => 'fail', 'message' => trans('admin::messages.not-deleted', ['name' => trans('admin::controller/activity-type.activity-type')])];
        }

        return response()->json($response);
    }

}
