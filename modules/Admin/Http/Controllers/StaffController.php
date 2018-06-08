<?php

/**
 * The class for managing staff specific actions.
 *
 *
 * @author Bhawna Thadhani <bhawnat@iprogrammer.com>
 * @package Admin
 * @since 1.0
 */

namespace Modules\Admin\Http\Controllers;

use Auth;
use Datatables;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Modules\Admin\Models\Staff;
use Modules\Admin\Repositories\StaffRepository;
use Modules\Admin\Http\Requests\StaffCreateRequest;
use Modules\Admin\Http\Requests\StaffUpdateRequest;

class StaffController extends Controller {

    /**
     * The StaffRepository instance.
     *
     * @var Modules\Admin\Repositories\StaffRepository
     */
    protected $repository;

    /**
     * Create a new StaffController instance.
     *
     * @param  Modules\Admin\Repositories\ConfigSettingRepository $repository
     * @return void
     */
    public function __construct(StaffRepository $repository) {
        parent::__construct();
        $this->middleware('acl');
        $this->repository = $repository;
    }

    /**
     * List all the data
     * @param Modules\Admin\Repositories\StaffRepository $staffRepository
     * @return response
     */
    public function index() {
        return view('admin::staff.index');
    }

    /**
     * Display a listing of the resource.
     *
     * @return datatable
     */
    public function getData(Request $request) {
        $staffs = $this->repository->data($request->all());

        //filter to display own records
        if (Auth::guard('admin')->user()->hasOwnView && (empty(Auth::guard('admin')->user()->hasEdit) || empty(Auth::guard('admin')->user()->hasDelete))) {
            $staffs = $staffs->filter(function ($row) {
                return (Str::equals($row['created_by'], Auth::guard('admin')->user()->id)) ? true : false;
            });
        }
        return Datatables::of($staffs)
                        ->addColumn('status', function ($staff) {
                            $status = ($staff->status == 1) ? '<span class="label label-sm label-success">' . trans('admin::messages.active') . '</span>' : '<span class="label label-sm label-danger">' . trans('admin::messages.inactive') . '</span>';
                            return $status;
                        })
                        ->addColumn('action', function ($staff) {
                            $actionList = '';
                            if (!empty(Auth::guard('admin')->user()->hasEdit) || (!empty(Auth::guard('admin')->user()->hasOwnEdit) && ($staff->created_by == Auth::guard('admin')->user()->id))) {
                                $actionList = '<a href="javascript:;" data-action="edit" data-id="' . $staff->id . '" id="' . $staff->id . '" class="btn btn-xs default margin-bottom-5 yellow-gold edit-form-link" title="Edit"><i class="fa fa-pencil"></i></a>';
                            }
                            return $actionList;
                        })
                        ->make(true);
    }

    /**
     * Display a form to create new staff.
     *
     * @return view as response
     */
    public function create() {
        return view('admin::staff.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  Modules\Admin\Http\Requests\StaffCreateRequest $request
     * @return json encoded Response
     */
    public function store(StaffCreateRequest $request) {
        $response = $this->repository->store($request->all());

        return response()->json($response);
    }

    /**
     * Show the form for editing the specified staff.
     *
     * @param  Modules\Admin\Models\Staff $staff
     * @return json encoded Response
     */
    public function edit(Staff $staff) {
        $response['success'] = true;
        $response['form'] = view('admin::staff.edit', compact('staff'))->render();

        return response()->json($response);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  Modules\Admin\Http\Requests\StaffUpdateRequest $request, Modules\Admin\Models\Staff $staff
     * @return json encoded Response
     */
    public function update(StaffUpdateRequest $request, Staff $staff) {
        $response = $this->repository->update($request->all(), $staff);

        return response()->json($response);
    }

}
