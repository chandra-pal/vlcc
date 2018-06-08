<?php

/**
 * The class for managing food types specific actions.
 *
 *
 * @author Priyanka Deshpande <priyankad@iprogrammer.com>
 * @package Admin
 * @since 1.0
 */

namespace Modules\Admin\Http\Controllers;

use Auth;
use Datatables;
use Illuminate\Support\Str;
use Modules\Admin\Models\FoodType;
use Modules\Admin\Repositories\FoodTypeRepository;
use Modules\Admin\Http\Requests\FoodTypeCreateRequest;
use Modules\Admin\Http\Requests\FoodTypeUpdateRequest;

class FoodTypeController extends Controller {

    /**
     * The FoodTypeRepository instance.
     *
     * @var Modules\Admin\Repositories\FoodTypeRepository
     */
    protected $repository;

    /**
     * Create a new FoodTypeController instance.
     *
     * @param  Modules\Admin\Repositories\FoodTypeRepository $repository
     * @return void
     */
    public function __construct(FoodTypeRepository $repository) {
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
        return view('admin::food-type.index');
    }

    /**
     * Get a listing of the resource.
     *
     * @return datatable
     */
    public function getData() {
        $foodType = $this->repository->data();
        
        //filter to display own records
        if (Auth::guard('admin')->user()->hasOwnView && (empty(Auth::guard('admin')->user()->hasEdit) || empty(Auth::guard('admin')->user()->hasDelete))) {
            $foodType = $foodType->filter(function ($row) {
                return (Str::equals($row['created_by'], Auth::guard('admin')->user()->id)) ? true : false;
            });
        }

        return Datatables::of($foodType)
                        ->addColumn('status', function ($foodType) {
                            $status = ($foodType->status == 1) ? '<span class="label label-sm label-success">' . trans('admin::messages.active') . '</span>' : '<span class="label label-sm label-danger">' . trans('admin::messages.inactive') . '</span>';
                            return $status;
                        })
                        ->addColumn('action', function ($foodType) {
                            $actionList = '';
                            if (!empty(Auth::guard('admin')->user()->hasEdit) || (!empty(Auth::guard('admin')->user()->hasOwnEdit) && ($foodType->created_by == Auth::guard('admin')->user()->id))) {
                                $actionList .= '<a href="javascript:;" data-action="edit" title="Edit" data-id="' . $foodType->id . '" class="btn btn-xs default margin-bottom-5 yellow-gold edit-form-link" title="Edit" id="' . $foodType->id . '"><i class="fa fa-pencil"></i></a>';
                            }
//                            if (!empty(Auth::guard('admin')->user()->hasDelete) || (!empty(Auth::guard('admin')->user()->hasOwnDelete) && ($foodType->created_by == Auth::guard('admin')->user()->id))) {
//                                $actionList .= '<a href="javascript:;" class="btn btn-xs default delete red-thunderbird margin-bottom-5" title="Delete" id =' . $foodType->id . ' created_by = ' . $foodType->created_by . ' ><i class="fa fa-trash-o"></i></a>';
//                            }
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
        return view('admin::food-type.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  Modules\Admin\Http\Requests\FoodTypeCreateRequest $request
     * @return json encoded Response
     */
    public function store(FoodTypeCreateRequest $request) {    
        $response = $this->repository->create($request->all());
        return response()->json($response);
    }

    /**
     * Show the form for editing the specified activity type.
     *
     * @param  Modules\Admin\Models\FoodType $foodType
     * @return json encoded Response
     */
    public function edit(FoodType $foodType) {
        $response['success'] = true;
        $response['form'] = view('admin::food-type.edit', compact('foodType'))->render();

        return response()->json($response);
    }

    /**
     * Store an updated resource in storage.
     *
     * @param  Modules\Admin\Http\Requests\FoodTypeCreateRequest $request, Modules\Admin\Models\FoodType $foodType
     * @return json encoded Response
     */
    public function update(FoodTypeUpdateRequest $request, FoodType $foodType) {
        $response = $this->repository->update($request->all(), $foodType);

        return response()->json($response);
    }

    /**
     * Delete resource from storage.
     *
     * @param  Modules\Admin\Http\Requests\FoodTypeDeleteRequest $request
     * @return json encoded Response
     */
    public function destroy(FoodTypeDeleteRequest $request, FoodType $foodType) {
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
