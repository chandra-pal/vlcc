<?php

/**
 * The class for managing machine specific actions.
 *
 *
 * @author Bhawna Thadhani <bhawnat@iprogrammer.com>
 * @package Admin
 * @since 1.0
 */

namespace Modules\Admin\Http\Controllers;

use Auth;
use Cache;
use Datatables;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Modules\Admin\Models\Machine;
use Modules\Admin\Models\Center;
use Modules\Admin\Repositories\MachineRepository;
use Modules\Admin\Repositories\MachineTypeRepository;
use Modules\Admin\Repositories\CenterRepository;
use Modules\Admin\Http\Requests\MachineCreateRequest;
use Modules\Admin\Http\Requests\MachineUpdateRequest;

class MachineController extends Controller {

    /**
     * The MachineRepository instance.
     *
     * @var Modules\Admin\Repositories\MachineRepository
     */
    protected $repository;
    protected $machineTypeRepository;
    protected $centerRepository;

    /**
     * Create a new MachineController instance.
     *
     * @param  Modules\Admin\Repositories\ConfigSettingRepository $repository
     * @return void
     */
    public function __construct(MachineRepository $repository, MachineTypeRepository $machineTypeRepository, CenterRepository $centerRepository) {
        parent::__construct();
        $this->middleware('acl');
        $this->repository = $repository;
        $this->machineTypeRepository = $machineTypeRepository;
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
            $response['message'] = trans('admin::messages.updated', ['name' => trans('admin::controller/machine.machine')]);
        } else {
            $response['status'] = 'fail';
            $response['message'] = trans('admin::messages.not-updated', ['name' => trans('admin::controller/machine.machine')]);
        }
        return response()->json($response);
    }

    /**
     * Display a listing of the resource.
     *
     * @return datatable
     */
    public function getData(Request $request) {
        $params['user_id'] = Auth::guard('admin')->user()->id;
        $machines = $this->repository->data($params);

        //filter to display own records
        if (Auth::guard('admin')->user()->hasOwnView && (empty(Auth::guard('admin')->user()->hasEdit) || empty(Auth::guard('admin')->user()->hasDelete))) {
            $machines = $machines->filter(function ($row) {
                return (Str::equals($row['created_by'], Auth::guard('admin')->user()->id)) ? true : false;
            });
        }
        return Datatables::of($machines)
                        ->addColumn('cname', function ($machine) {
                            $centerList = array();
                            $centerList1 = array();
                            $centerList = explode(',', $machine['center_id']);
                            $i = 1;
                            foreach ($centerList as $data) {
                                array_push($centerList1, $i . '. ' . $data);
                                $i++;
                            }

                            if (!empty($centerList)) {
                                if (count($centerList1) > 1) {
                                    return $center = implode('</br> ', $centerList1);
                                } else {
                                    return $center = implode('', $centerList1);
                                }
                            } else {
                                return 'N/A';
                            }
                        })
                        ->addColumn('machine_type', function ($machine) {
                            return (!empty($machine['machine_type']) ? $machine['machine_type'] : '');
                        })
                        ->addColumn('name', function ($machine) {
                            return (!empty($machine['name']) ? $machine['name'] : '');
                        })
                        ->addColumn('description', function ($machine) {
                            return (!empty($machine['description']) ? $machine['description'] : '');
                        })
                        ->addColumn('status_format', function ($machine) {
                            switch ($machine['status']) {
                                case 0:
                                    $status = '<span class="label label-sm label-danger">' . trans('admin::messages.inactive') . '</span>';
                                    break;
                                case 1:
                                    $status = '<span class="label label-sm label-success">' . trans('admin::messages.active') . '</span>';
                                    break;
                            }
                            return $status;
                        })
                        ->addColumn('action', function ($machine) {
                            $actionList = '';
                            if (!empty(Auth::guard('admin')->user()->hasEdit) || (!empty(Auth::guard('admin')->user()->hasOwnEdit) && ($machine->created_by == Auth::guard('admin')->user()->id))) {
                                $actionList = '<a href="javascript:;" data-action="edit" data-id="' . $machine['id'] . '" id="' . $machine['id'] . '" class="btn btn-xs default margin-bottom-5 yellow-gold edit-form-link" title="Edit"><i class="fa fa-pencil"></i></a>';
                            }
                            return $actionList;
                        })
                        ->filter(function ($instance) use ($request) {
                            if (Auth::guard('admin')->user()->hasOwnView) {
                                $instance->collection = $instance->collection->filter(function ($row) {
                                    return (Str::equals($row['created_by'], Auth::guard('admin')->user()->id)) ? true : false;
                                });
                            }
                            if ($request->has('cname')) {
                                $found = false;
                                $instance->collection = $instance->collection->filter(function ($row) use ($request, $found) {
                                    $searchFilter = array();
                                    $centerList = explode(',', $row['center_id']);
                                    foreach ($centerList as $data) {
                                        if (Str::equals((string) $data, $request->get('cname'))) {
                                            $found = true;
                                        }
                                    }
                                    return $found;
                                });
                            }
                            if ($request->has('machine_type')) {
                                $instance->collection = $instance->collection->filter(function ($row) use ($request) {
                                    return str_contains(strtolower($row['machine_type']), strtolower($request->get('machine_type'))) ? true : false;
                                });
                            }
                            if ($request->has('name')) {
                                $instance->collection = $instance->collection->filter(function ($row) use ($request) {
                                    return str_contains(strtolower($row['name']), strtolower($request->get('name'))) ? true : false;
                                });
                            }
                            if ($request->has('description')) {
                                $instance->collection = $instance->collection->filter(function ($row) use ($request) {
                                    return str_contains(strtolower($row['description']), strtolower($request->get('description'))) ? true : false;
                                });
                            }
                            if ($request->has('status')) {
                                $instance->collection = $instance->collection->filter(function ($row) use ($request) {
                                    return str_contains(strtolower($row['status']), strtolower($request->get('status'))) ? true : false;
                                });
                            }
                        })
                        ->make(true);
    }

    /**
     * List all the data
     * @param Modules\Admin\Repositories\MachineRepository $machineRepository
     * @return response
     */
    public function index() {
        $machineName1="";
        $selectedCenters = [];
        $logged_in_user_id = Auth::guard('admin')->user()->id;
        $machineTypesList = $this->machineTypeRepository->listMachineTypesData()->toArray();
        $centerList = $this->centerRepository->listLoggedInUsersCenters($logged_in_user_id);
        $searchCenterList = [];
        if (!empty($centerList)) {
            foreach ($centerList as $key => $value) {
                $searchCenterList[$value] = $value;
            }
        }
        return view('admin::machine.index', compact('data','machineName1', 'machineTypesList', 'centerList', 'selectedCenters', 'searchCenterList'));
    }

    /**
     * Display a form to create new machine.
     *
     * @return view as response
     */
    public function create() {
        $selectedCenters = [];
        // $centersList = $this->centerRepository->listCenterData()->toArray();
        $logged_in_user_id = Auth::guard('admin')->user()->id;
        $machineTypesList = $this->machineTypeRepository->listMachineTypesData()->toArray();
        $centerList = $this->centerRepository->listLoggedInUsersCenters($logged_in_user_id);
        return view('admin::machine.create', compact('data', 'machineTypesList', 'centerList', 'selectedCenters'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  Modules\Admin\Http\Requests\MachineCreateRequest $request
     * @return json encoded Response
     */
    public function store(MachineCreateRequest $request) {
        $response = $this->repository->create($request->all());
        return response()->json($response);
    }

    /**
     * Show the form for editing the specified machine.
     *
     * @param  Modules\Admin\Models\Machine $machine
     * @return json encoded Response
     */
    public function edit(Machine $machine) {
        //$centerList = $this->centerRepository->listCenterData()->toArray();
        $logged_in_user_id = Auth::guard('admin')->user()->id;
        $machineTypesList = $this->machineTypeRepository->listMachineTypesData()->toArray();
        $centerList = $this->centerRepository->listLoggedInUsersCenters($logged_in_user_id);
        $selectedCenters = $this->repository->getCentersIdsByMachine($machine->id);
        Cache::tags(Center::table())->flush();
        $response['success'] = true;
        $machineName=explode("-",$machine->name);
        $machineName1=$machineName['1'];
        $response['form'] = view('admin::machine.edit', compact('machine', 'machineName1','machineTypesList', 'centerList', 'selectedCenters'))->render();
        return response()->json($response);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  Modules\Admin\Http\Requests\MachineUpdateRequest $request, Modules\Admin\Models\Machine $machine
     * @return json encoded Response
     */
    public function update(MachineUpdateRequest $request, Machine $machine) {
        $response = $this->repository->update($request->all(), $machine);
        return response()->json($response);
    }

}
