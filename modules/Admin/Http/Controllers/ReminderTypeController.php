<?php

/**
 * The class for managing reminder type specific actions.
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
use Modules\Admin\Models\ReminderType;
use Modules\Admin\Repositories\ReminderTypeRepository;
use Modules\Admin\Http\Requests\ReminderTypeCreateRequest;
use Modules\Admin\Http\Requests\ReminderTypeUpdateRequest;
use Modules\Admin\Http\Requests\ReminderTypeDeleteRequest;

class ReminderTypeController extends Controller {

    /**
     * The ReminderTypeRepository instance.
     *
     * @var Modules\Admin\Repositories\ReminderTypeRepository
     */
    protected $repository;

    /**
     * Create a new ReminderTypeController instance.
     *
     * @param  Modules\Admin\Repositories\ReminderTypeRepository $repository
     * @return void
     */
    public function __construct(ReminderTypeRepository $repository) {
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
        return view('admin::reminder-type.index');
    }

    /**
     * Get a listing of the resource.
     *
     * @return datatable
     */
    public function getData() {
        $reminderType = $this->repository->data();
        //filter to display own records
        if (Auth::guard('admin')->user()->hasOwnView && (empty(Auth::guard('admin')->user()->hasEdit) || empty(Auth::guard('admin')->user()->hasDelete))) {
            $reminderType = $reminderType->filter(function ($row) {
                return (Str::equals($row['created_by'], Auth::guard('admin')->user()->id)) ? true : false;
            });
        }

        return Datatables::of($reminderType)
                        ->addColumn('status', function ($reminderType) {
                            $status = ($reminderType->status == 1) ? '<span class="label label-sm label-success">' . trans('admin::messages.active') . '</span>' : '<span class="label label-sm label-danger">' . trans('admin::messages.inactive') . '</span>';
                            return $status;
                        })
                        ->addColumn('action', function ($reminderType) {
                            $actionList = '';
                            if (!empty(Auth::guard('admin')->user()->hasEdit) || (!empty(Auth::guard('admin')->user()->hasOwnEdit) && ($reminderType->created_by == Auth::guard('admin')->user()->id))) {
                                $actionList .= '<a href="javascript:;" data-action="edit" title="Edit" data-id="' . $reminderType->id . '" class="btn btn-xs default margin-bottom-5 yellow-gold edit-form-link" title="Edit" id="' . $reminderType->id . '"><i class="fa fa-pencil"></i></a>';
                            }
                            if (!empty(Auth::guard('admin')->user()->hasDelete) || (!empty(Auth::guard('admin')->user()->hasOwnDelete) && ($reminderType->created_by == Auth::guard('admin')->user()->id))) {
                                $actionList .= '<a href="javascript:;" class="btn btn-xs default delete red-thunderbird margin-bottom-5" title="Delete" id =' . $reminderType->id . ' created_by = ' . $reminderType->created_by . ' ><i class="fa fa-trash-o"></i></a>';
                            }
                            return $actionList;
                        })
                        ->make(true);
    }

    /**
     * Display a form to create new reminder type.
     *
     * @return view as response
     */
    public function create() {
        return view('admin::reminder-type.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  Modules\Admin\Http\Requests\ReminderTypeCreateRequest $request
     * @return json encoded Response
     */
    public function store(ReminderTypeCreateRequest $request) {
        $response = $this->repository->create($request->all());

        return response()->json($response);
    }

    /**
     * Show the form for editing the specified reminder type.
     *
     * @param  Modules\Admin\Models\ReminderType $reminderType
     * @return json encoded Response
     */
    public function edit(ReminderType $reminderType) {
        $response['success'] = true;
        $response['form'] = view('admin::reminder-type.edit', compact('reminderType'))->render();

        return response()->json($response);
    }

    /**
     * Store an updated resource in storage.
     *
     * @param  Modules\Admin\Http\Requests\ReminderTypeCreateRequest $request, Modules\Admin\Models\ReminderType $reminderType
     * @return json encoded Response
     */
    public function update(ReminderTypeUpdateRequest $request, ReminderType $reminderType) {
        $response = $this->repository->update($request->all(), $reminderType);

        return response()->json($response);
    }

    /**
     * Delete resource from storage.
     *
     * @param  Modules\Admin\Http\Requests\ReminderTypeDeleteRequest $request
     * @return json encoded Response
     */
    public function destroy(ReminderTypeDeleteRequest $request, ReminderType $reminderType) {
        $response = [];
        $result = $this->repository->deleteAction($request->all());
        if ($result) {
            $response = ['status' => 'success', 'message' => trans('admin::messages.deleted', ['name' => trans('admin::controller/reminder-type.reminder-type')])];
        } else {
            $response = ['status' => 'fail', 'message' => trans('admin::messages.not-deleted', ['name' => trans('admin::controller/reminder-type.reminder-type')])];
        }

        return response()->json($response);
    }

}
