<?php

/**
 * The class for managing food specific actions.
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
use Modules\Admin\Models\Food;
use Modules\Admin\Repositories\FoodRepository;
use Modules\Admin\Repositories\FoodTypeRepository;
use Modules\Admin\Http\Requests\FoodCreateRequest;
use Modules\Admin\Http\Requests\FoodUpdateRequest;
use Modules\Admin\Http\Requests\FoodDeleteRequest;

class FoodController extends Controller {

    /**
     * The FoodRepository instance.
     *
     * @var Modules\Admin\Repositories\FoodRepository
     */
    protected $repository;
    protected $foodTyperepository;

    /**
     * Create a new FoodController instance.
     *
     * @param  Modules\Admin\Repositories\FoodRepository $repository
     * @return void
     */
    public function __construct(FoodRepository $repository, FoodTypeRepository $foodTyperepository) {
        parent::__construct();
        $this->middleware('acl');
        $this->repository = $repository;
        $this->foodTyperepository = $foodTyperepository;
    }

    /**
     * Display a listing of the resource.
     *
     * @return view as response
     */
    public function index() {
        $created_by_user_type = Auth::guard('admin')->user()->userType->id;
        $foodTypeList = $this->foodTyperepository->listFoodTypesData()->toArray();
        return view('admin::food.index', compact('created_by_user_type', 'foodTypeList'));
    }

    /**
     * Get a listing of the resource.
     *
     * @return datatable
     */
    public function getData() {
        $foods = $this->repository->data();
        
        //filter to display own records
        if (Auth::guard('admin')->user()->hasOwnView && (empty(Auth::guard('admin')->user()->hasEdit) || empty(Auth::guard('admin')->user()->hasDelete))) {
            $foods = $foods->filter(function ($row) {
                return ($row['created_by'] == Auth::guard('admin')->user()->id) ? true : false;
            });
        }
        return Datatables::of($foods)
            ->addColumn('action', function ($foods) {
                $actionList = '';
                if (!empty(Auth::guard('admin')->user()->hasEdit) || (!empty(Auth::guard('admin')->user()->hasOwnEdit) && ($foods->created_by == Auth::guard('admin')->user()->id))) {
                    $actionList .= '<a href="javascript:;" data-action="edit" title="Edit" data-id="' . $foods->id . '" class="btn btn-xs default margin-bottom-5 yellow-gold edit-form-link" title="Edit" id="' . $foods->id . '"><i class="fa fa-pencil"></i></a>';
                }
//                if (!empty(Auth::guard('admin')->user()->hasDelete) || (!empty(Auth::guard('admin')->user()->hasOwnDelete) && ($foods->created_by == Auth::guard('admin')->user()->id))) {
//                    $actionList .= '<a href="javascript:;" class="btn btn-xs default delete red-thunderbird margin-bottom-5" title="Delete" id =' . $foods->id . ' created_by = ' . $foods->created_by . ' ><i class="fa fa-trash-o"></i></a>';
//                }
                return $actionList;
            })
            ->make(true);
    }

    /**
     * Display a form to create new food 
     *
     * @return view as response
     */
    public function create() {
        return view('admin::food.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  Modules\Admin\Http\Requests\FoodCreateRequest $request
     * @return json encoded Response
     */
    public function store(FoodCreateRequest $request) {
        $foodName = ucfirst($request->all()["food_name"]);
        $foodTypeId = $request->all()["food_type_id"];        
        $updateFood = $this->repository->updateFood($foodName, $foodTypeId);
        $response = $this->repository->create($request->all());
        return response()->json($response);
    }

    /**
     * Show the form for editing the specified food.
     *
     * @param  Modules\Admin\Models\Food $Food
     * @return json encoded Response
     */
    public function edit(Food $food) {
        $created_by_user_type = Auth::guard('admin')->user()->userType->id;
        $foodTypeList = $this->foodTyperepository->listFoodTypesData()->toArray();
        $response['success'] = true;
        $response['form'] = view('admin::food.edit', compact('food', 'created_by_user_type', 'foodTypeList'))->render();

        return response()->json($response);
    }

    /**
     * Store an updated resource in storage.
     *
     * @param  Modules\Admin\Http\Requests\FoodCreateRequest $request, Modules\Admin\Models\Food $food
     * @return json encoded Response
     */
    public function update(FoodUpdateRequest $request, Food $food) {
        $response = $this->repository->update($request->all(), $food);

        return response()->json($response);
    }

    /**
     * Delete resource from storage.
     *
     * @param  Modules\Admin\Http\Requests\FoodDeleteRequest $request
     * @return json encoded Response
     */
    public function destroy(FoodDeleteRequest $request, Food $food) {
        //dd("here" . $food);
        $response = [];
        $result = $this->repository->deleteAction($request->all());
        if ($result) {
            $response = ['status' => 'success', 'message' => trans('admin::messages.deleted', ['name' => trans('admin::controller/food.food')])];
        } else {
            $response = ['status' => 'fail', 'message' => trans('admin::messages.not-deleted', ['name' => trans('admin::controller/food.food')])];
        }

        return response()->json($response);
    }

}
