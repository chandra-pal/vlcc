<?php

/**
 * The class for managing Measurement specific actions.
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
use Modules\Admin\Models\Measurement;
use Modules\Admin\Repositories\MeasurementRepository;
use Modules\Admin\Http\Requests\MeasurementCreateRequest;
use Modules\Admin\Http\Requests\MeasurementUpdateRequest;
use Modules\Admin\Http\Requests\MeasurementDeleteRequest;
use Modules\Admin\Services\Helper\MeasurementConstantHelper;

class MeasurementController extends Controller {

    /**
     * The MeasurementRepository instance.
     *
     * @var Modules\Admin\Repositories\MeasurementRepository
     */
    protected $repository;

    /**
     * Create a new MeasurementController instance.
     *
     * @param  Modules\Admin\Repositories\MeasurementRepository $repository
     * @return void
     */
    public function __construct(MeasurementRepository $repository) {
        parent::__construct();
        $this->middleware('acl');
        $this->repository = $repository;
    }

    /**
     * List all the data
     * @return response
     */
    public function index() {
        $allCategoriesList = $this->repository->listAllCategoriesData()->toArray();

        return view('admin::measurement.index', compact('allCategoriesList'));
    }

    /**
     * Display a listing of the resource.
     *
     * @return datatable
     */
    public function getData(Request $request) {
        $measurements = $this->repository->data();


        //filter to display own records
        if (Auth::guard('admin')->user()->hasOwnView && (empty(Auth::guard('admin')->user()->hasEdit) || empty(Auth::guard('admin')->user()->hasDelete))) {
            $measurements = $measurements->filter(function ($row) {
                return (Str::equals($row['created_by'], Auth::guard('admin')->user()->id)) ? true : false;
            });
        }

        return Datatables::of($measurements)
                        ->addColumn('status', function ($measurements) {
                            $status = ($measurements->status == 1) ? '<span class="label label-sm label-success">' . trans('admin::messages.active') . '</span>' : '<span class="label label-sm label-danger">' . trans('admin::messages.inactive') . '</span>';
                            return $status;
                        })
                        ->addColumn('action', function ($measurements) {
                            $actionList = '';
                            if (!empty(Auth::guard('admin')->user()->hasEdit) || (!empty(Auth::guard('admin')->user()->hasOwnEdit) && ($measurements->created_by == Auth::guard('admin')->user()->id))) {
                                $actionList = '<a href="javascript:;" id="' . $measurements->id . '" data-action="edit" data-id="' . $measurements->id . '" id="' . $measurements->id . '" class="btn btn-xs default margin-bottom-5 yellow-gold edit-form-link" title="Edit"><i class="fa fa-pencil"></i></a>';
                            }
                            if (!empty(Auth::guard('admin')->user()->hasDelete) || (!empty(Auth::guard('admin')->user()->hasOwnDelete) && ($measurements->created_by == Auth::guard('admin')->user()->id))) {
                                $actionList .= '<a href="javascript:;" data-message="' . trans('admin::messages.delete-confirm') . '" data-action="delete" data-id="' . $measurements->id . '" class="btn btn-xs default red-thunderbird margin-bottom-5 delete" title="' . trans('admin::messages.delete') . '"><i class="fa fa-trash-o"></i></a>';
                            }
                            return $actionList;
                        })
                        ->make(true);
    }

    /**
     * Display a form to create new measurement.
     *
     * @return view as response
     */
    public function create() {
        $categoryList = $this->measurementRepository->listCategoryData()->toArray();

        return view('admin::measurement.create', compact('categoryList'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  Modules\Admin\Http\Requests\MeasurementCreateRequest $request
     * @return json encoded Response
     */
    public function store(MeasurementCreateRequest $request) {
        $response = $this->repository->create($request->all());

        return response()->json($response);
    }

    /**
     * Show the form for editing the specified configuration setting.
     *
     * @param  Modules\Admin\Models\Measurement $measurement, Modules\Admin\Repositories\MeasurementCategoryRepository $measurementCategoryRepository
     * @return json encoded Response
     */
    public function edit(Measurement $measurement) {
        $categoryList = $this->repository->listCategoryData()->toArray();

        $response['success'] = true;
        $response['form'] = view('admin::measurement.edit', compact('measurement', 'categoryList'))->render();

        return response()->json($response);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  Modules\Admin\Http\Requests\MeasurementUpdateRequest $request, Modules\Admin\Models\Measurement $measurement
     * @return json encoded Response
     */
    public function update(MeasurementUpdateRequest $request, Measurement $measurement) {
        $response = $this->repository->update($request->all(), $measurement);

        return response()->json($response);
    }

    /**
     * Delete resource from storage.
     *
     * @param  Modules\Admin\Http\Requests\MeasurementDeleteRequest $request
     * @return json encoded Response
     */
    public function destroy(MeasurementDeleteRequest $request, Measurement $measurement) {
        $response = [];
        $result = $this->repository->deleteAction($request->all());
        if ($result) {
            $response = ['status' => 'success', 'message' => trans('admin::messages.deleted', ['name' => trans('admin::controller/measurement.measurement')])];
        } else {
            $response = ['status' => 'fail', 'message' => trans('admin::messages.not-deleted', ['name' => trans('admin::controller/measurement.measurement')])];
        }

        return response()->json($response);
    }

}
