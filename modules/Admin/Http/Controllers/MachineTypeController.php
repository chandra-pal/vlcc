<?php

/**
 * The class for managing machine type specific actions.
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
use Modules\Admin\Models\MachineType;
use Modules\Admin\Repositories\MachineTypeRepository;
use Modules\Admin\Http\Requests\MachineTypeCreateRequest;
use Modules\Admin\Http\Requests\MachineTypeUpdateRequest;

class MachineTypeController extends Controller {

    /**
     * The MachineTypeRepository instance.
     *
     * @var Modules\Admin\Repositories\MachineTypeRepository
     */
    protected $repository;

    /**
     * Create a new MachineTypeController instance.
     *
     * @param  Modules\Admin\Repositories\ConfigSettingRepository $repository
     * @return void
     */
    public function __construct(MachineTypeRepository $repository) {
        parent::__construct();
        $this->middleware('acl');
        $this->repository = $repository;
    }

    /**
     * Display a listing of the resource.
     *
     * @return datatable
     */
    public function getData(Request $request) {
        $machineTypes = $this->repository->data($request->all());

        //filter to display own records
        if (Auth::guard('admin')->user()->hasOwnView && (empty(Auth::guard('admin')->user()->hasEdit) || empty(Auth::guard('admin')->user()->hasDelete))) {
            $machineTypes = $machineTypes->filter(function ($row) {
                return (Str::equals($row['created_by'], Auth::guard('admin')->user()->id)) ? true : false;
            });
        }
        return Datatables::of($machineTypes)
//                        ->addColumn('machine_type', function ($machineType) {
//                            //return (!empty($machineType['machine_type']) ? $machineType['machine_type'] : '');
//                            return (!empty($machineType->machine_type) ? $machineType->machine_type : '');
//                        })
                        ->addColumn('status_format', function ($machineType) {
                            switch ($machineType->status) {

                                case 0:
                                    $status = '<span class="label label-sm label-danger">' . trans('admin::messages.inactive') . '</span>';
                                    break;
                                case 1:
                                    $status = '<span class="label label-sm label-success">' . trans('admin::messages.active') . '</span>';
                                    break;
                            }
                            return $status;
                        })
                        ->addColumn('action', function ($machineType) {
                            $actionList = '';
                            if (!empty(Auth::guard('admin')->user()->hasEdit) || (!empty(Auth::guard('admin')->user()->hasOwnEdit) && ($machineType->created_by == Auth::guard('admin')->user()->id))) {
                                $actionList = '<a href="javascript:;" data-action="edit" data-id="' . $machineType->id . '" id="' . $machineType->id . '" class="btn btn-xs default margin-bottom-5 yellow-gold edit-form-link" title="Edit"><i class="fa fa-pencil"></i></a>';
                            }
                            return $actionList;
                        })
                        ->make(true);
    }

    /**
     * List all the data
     * @param Modules\Admin\Repositories\MachineTypeRepository $repository
     * @return response
     */
    public function index() {
        return view('admin::machine-type.index');
    }

    /**
     * Display a form to create new machine type.
     *
     * @return view as response
     */
    public function create() {
        return view('admin::machine-type.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  Modules\Admin\Http\Requests\MachineTypeCreateRequest $request
     * @return json encoded Response
     */
    public function store(MachineTypeCreateRequest $request) {
        $response = $this->repository->create($request->all());
        return response()->json($response);
    }

    /**
     * Show the form for editing the specified machine type.
     *
     * @param  Modules\Admin\Models\MachineType $machineType
     * @return json encoded Response
     */
    public function edit(MachineType $machineType) {
        $response['success'] = true;
        $response['form'] = view('admin::machine-type.edit', compact('machineType'))->render();
        return response()->json($response);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  Modules\Admin\Http\Requests\MachineTypeUpdateRequest $request, Modules\Admin\Models\MachineType $machineType
     * @return json encoded Response
     */
    public function update(MachineTypeUpdateRequest $request, MachineType $machineType) {
        $response = $this->repository->update($request->all(), $machineType);
        return response()->json($response);
    }

}
