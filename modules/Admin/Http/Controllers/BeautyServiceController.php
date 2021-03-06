<?php

/**
 * The class for managing beauty services specific actions.
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
use Modules\Admin\Models\BeautyServices;
use Modules\Admin\Repositories\BeautyServiceRepository;
use Modules\Admin\Http\Requests\BeautyServiceCreateRequest;
use Modules\Admin\Http\Requests\BeautyServiceUpdateRequest;

class BeautyServiceController extends Controller {

    /**
     * The BeautyServiceRepository instance.
     *
     * @var Modules\Admin\Repositories\BeautyServiceRepository
     */
    protected $repository;

    /**
     * Create a new BeautyServiceController instance.
     *
     * @param  Modules\Admin\Repositories\ConfigSettingRepository $repository
     * @return void
     */
    public function __construct(BeautyServiceRepository $repository) {
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
        $beautyServices = $this->repository->data($request->all());

        //filter to display own records
        if (Auth::guard('admin')->user()->hasOwnView && (empty(Auth::guard('admin')->user()->hasEdit) || empty(Auth::guard('admin')->user()->hasDelete))) {
            $beautyServices = $beautyServices->filter(function ($row) {
                return (Str::equals($row['created_by'], Auth::guard('admin')->user()->id)) ? true : false;
            });
        }
        return Datatables::of($beautyServices)
                        ->addColumn('status_format', function ($beautyService) {
                            switch ($beautyService->status) {

                                case 0:
                                    $status = '<span class="label label-sm label-danger">' . trans('admin::messages.inactive') . '</span>';
                                    break;
                                case 1:
                                    $status = '<span class="label label-sm label-success">' . trans('admin::messages.active') . '</span>';
                                    break;
                            }
                            return $status;
                        })
                        ->addColumn('action', function ($beautyService) {
                            $actionList = '';
                            if (!empty(Auth::guard('admin')->user()->hasEdit) || (!empty(Auth::guard('admin')->user()->hasOwnEdit) && ($beautyService->created_by == Auth::guard('admin')->user()->id))) {
                                $actionList = '<a href="javascript:;" data-action="edit" data-id="' . $beautyService->id . '" id="' . $beautyService->id . '" class="btn btn-xs default margin-bottom-5 yellow-gold edit-form-link" title="Edit"><i class="fa fa-pencil"></i></a>';
                            }
                            return $actionList;
                        })
                        ->make(true);
    }

    /**
     * List all the data
     * @param Modules\Admin\Repositories\BeautyServiceRepository $repository
     * @return response
     */
    public function index() {
        return view('admin::beauty-service.index');
    }

    /**
     * Display a form to create new beauty service.
     *
     * @return view as response
     */
    public function create() {
        return view('admin::beauty-service.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  Modules\Admin\Http\Requests\BeautyServiceCreateRequest $request
     * @return json encoded Response
     */
    public function store(BeautyServiceCreateRequest $request) {
        $response = $this->repository->create($request->all());
        return response()->json($response);
    }

    /**
     * Show the form for editing the specified machine type.
     *
     * @param  Modules\Admin\Models\BeautyServices $beautyService
     * @return json encoded Response
     */
    public function edit(BeautyServices $beautyService) {
        $response['success'] = true;
        $response['form'] = view('admin::beauty-service.edit', compact('beautyService'))->render();
        return response()->json($response);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  Modules\Admin\Http\Requests\BeautyServiceUpdateRequest $request, Modules\Admin\Models\BeautyServices $beautyService
     * @return json encoded Response
     */
    public function update(BeautyServiceUpdateRequest $request, BeautyServices $beautyService) {
        $response = $this->repository->update($request->all(), $beautyService);
        return response()->json($response);
    }

}
