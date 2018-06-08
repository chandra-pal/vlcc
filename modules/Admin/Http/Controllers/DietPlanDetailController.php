<?php

/**
 * The class for managing diet plan details specific actions.
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
use Modules\Admin\Models\DietPlanDetail;
use Modules\Admin\Repositories\DietPlanDetailRepository;
use Modules\Admin\Repositories\DietPlanRepository;
use Modules\Admin\Repositories\DietScheduleTypeRepository;
use Modules\Admin\Repositories\FoodRepository;
use Modules\Admin\Http\Requests\DietPlanDetailCreateRequest;
use Modules\Admin\Http\Requests\DietPlanDetailUpdateRequest;
use Modules\Admin\Http\Requests\DietPlanDetailDeleteRequest;
use Illuminate\Http\Request;
use Modules\Admin\Repositories\FoodTypeRepository;

class DietPlanDetailController extends Controller {

    /**
     * The DietPlanDetailRepository instance.
     *
     * @var Modules\Admin\Repositories\DietPlanDetailRepository
     */
    protected $repository;
    protected $dietPlan;
    protected $dietScheduleType;
    protected $food;

    /**
     * Create a new DietPlanDetailController instance.
     *
     * @param  Modules\Admin\Repositories\DietPlanDetailRepository $repository
     * @return void
     */
    public function __construct(DietPlanDetailRepository $repository, DietPlanRepository $dietPlanRepository, DietScheduleTypeRepository $dietScheduleTypeRepository, FoodRepository $foodRepository, FoodTypeRepository $foodTypeRepository) {
        parent::__construct();
        $this->middleware('acl');
        $this->repository = $repository;
        $this->dietPlanRepository = $dietPlanRepository;
        $this->dietScheduleTypeRepository = $dietScheduleTypeRepository;
        $this->foodRepository = $foodRepository;
        $this->foodTypeRepository = $foodTypeRepository;
    }

    /**
     * Display a listing of the resource.
     *
     * @return view as response
     */
    public function index() {
        $dietPlanType = $this->dietPlanRepository->listPlanType()->toArray();
        $planType = array("1" => "Veg", "2" => "Non Veg");
        if (!empty($dietPlanType)) {
            foreach ($dietPlanType as $key => $diet) {
                $dietPlanTypeList[$diet['id']] = $diet['plan_name'] . " - " . $planType[$diet['plan_type']] . " - " . $diet['calories'];
            }
        }

        $dietScheduleType = $this->dietScheduleTypeRepository->listScheduleTypes()->toArray();

        $scheduleList = $this->dietScheduleTypeRepository->data()->toArray();
        if (!empty($scheduleList)) {
            foreach ($scheduleList as $key => $schedule) {
                $scheduleTypeList[] = $schedule['schedule_name'];
            }
        } else {
            $scheduleTypeList[] = "";
        }

        if (!empty($dietPlanType)) {
            foreach ($dietPlanType as $key => $diet) {
                $dietPlanTypeListDropdown[] = $diet['plan_name'] . " - " . $planType[$diet['plan_type']] . " - " . $diet['calories'];
            }
        }

        $foodTypeList = $this->foodTypeRepository->data()->toArray();
        if (!empty($foodTypeList)) {
            foreach ($foodTypeList as $key => $type) {
                $foodTypeLists[$type['id']] = $type['food_type_name'];
            }
        }

        $foodList = array();
        $selectedFoodTypeId = '';
        return view('admin::diet-plan-detail.index', compact('dietPlanTypeList', 'dietPlanList', 'dietScheduleType', 'foodList', 'scheduleTypeList', 'dietPlanTypeListDropdown', 'foodTypeLists', 'selectedFoodTypeId'));
    }

    /**
     * Get a listing of the resource.
     *
     * @return datatable
     */
    public function getData(Request $request) {
        $dietPlanDetails = $this->repository->data();
        $dietPlanTypeList = array("1" => "Veg", "2" => "Non Veg");
        //filter to display own records
        if (Auth::guard('admin')->user()->hasOwnView && (empty(Auth::guard('admin')->user()->hasEdit) || empty(Auth::guard('admin')->user()->hasDelete))) {
            $dietPlanDetails = $dietPlanDetails->filter(function ($row) {
                return (Str::equals($row['created_by'], Auth::guard('admin')->user()->id)) ? true : false;
            });
        }

        return Datatables::of($dietPlanDetails)
                        ->addColumn('diet_plan_name', function ($dietPlanDetails) use($dietPlanTypeList) {
                            $dietPlanName = $dietPlanDetails->dietPlan->plan_name . " - " . $dietPlanTypeList[$dietPlanDetails->dietPlan->plan_type] . " - " . $dietPlanDetails->dietPlan->calories;
                            return $dietPlanName;
                        })
//                        ->addColumn('status', function ($dietPlanDetails) {
//                            $status = ($dietPlanDetails->status == 1) ? '<span class="label label-sm label-success">' . trans('admin::messages.active') . '</span>' : '<span class="label label-sm label-danger">' . trans('admin::messages.inactive') . '</span>';
//                            return $status;
//                        })
                        ->addColumn('action', function ($dietPlanDetails) {
                            $actionList = '';
                            if (!empty(Auth::guard('admin')->user()->hasEdit) || (!empty(Auth::guard('admin')->user()->hasOwnEdit) && ($dietPlanDetails->created_by == Auth::guard('admin')->user()->id))) {
                                $actionList .= '<a href="javascript:;" data-action="edit" title="Edit" data-id="' . $dietPlanDetails->id . '" class="btn btn-xs default margin-bottom-5 yellow-gold edit-form-link" title="Edit" id="' . $dietPlanDetails->id . '"><i class="fa fa-pencil"></i></a>';
                            }
//                            if (!empty(Auth::guard('admin')->user()->hasDelete) || (!empty(Auth::guard('admin')->user()->hasOwnDelete) && ($dietPlanDetails->created_by == Auth::guard('admin')->user()->id))) {
//                                $actionList .= '<a href="javascript:;" class="btn btn-xs default delete red-thunderbird margin-bottom-5" title="Delete" id =' . $dietPlanDetails->id . ' created_by = ' . $dietPlanDetails->created_by . ' ><i class="fa fa-trash-o"></i></a>';
//                            }
                            return $actionList;
                        })
                        ->filter(function ($instance) use ($request) {
                            if ($request->has('diet_plan_name')) {
                                $instance->collection = $instance->collection->filter(function ($row) use ($request) {
                                    $dietPlanTypeList = array("1" => "Veg", "2" => "Non Veg");
//                                    dd($row['DietPlan']['calorie']);
                                    return str_contains(strtolower($row['DietPlan']['plan_name'] . " - " . $dietPlanTypeList[$row['DietPlan']['plan_type']] . " - " . $row['DietPlan']['calories']), strtolower($request->get('diet_plan_name'))) ? true : false;
                                });
                            }

                            if ($request->has('schedule_type')) {
                                $instance->collection = $instance->collection->filter(function ($row) use ($request) {
                                    return str_contains(strtolower($row['DietScheduleType']['schedule_name']), strtolower($request->get('schedule_type'))) ? true : false;
                                });
                            }
                            if ($request->has('food')) {
                                $instance->collection = $instance->collection->filter(function ($row) use ($request) {
                                    return str_contains(strtolower($row['food']), strtolower($request->get('food'))) ? true : false;
                                });
                            }
                            if ($request->has('servings_recommended')) {
                                $instance->collection = $instance->collection->filter(function ($row) use ($request) {
                                    return str_contains(strtolower($row['servings_recommended']), strtolower($request->get('servings_recommended'))) ? true : false;
                                });
                            }
//                            if ($request->has('status')) {
//                                $instance->collection = $instance->collection->filter(function ($row) use ($request) {
//                                    return str_contains(strtolower($row['status']), strtolower($request->get('status'))) ? true : false;
//                                });
//                            }
                        })
                        ->make(true);
    }

    /**
     * Display a listing of the resource.
     *
     * @return json encoded response
     */
    public function getFoodList(Request $request) {
        $params = [];
        $params['food_type_id'] = $request->input('food_type_id');
        $params['diet_plan_id'] = $request->input('diet_plan_id');
        $params['schedule_id'] = $request->input('schedule_id');

        $getSelectedFoodlist = $this->repository->getSelectedFoodlist($params);

        $foodList = $this->repository->getFoodList($getSelectedFoodlist, $params);
        $response['list'] = View('admin::diet-plan-detail.dropdown', compact('foodList'))->render();
        return response()->json($response);
    }

    /**
     * Display a form to create new diet plan detail.
     *
     * @return view as response
     */
    public function create() {
        return view('admin::diet-plan-detail.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  Modules\Admin\Http\Requests\DietPlanDetailCreateRequest $request
     * @return json encoded Response
     */
    public function store(DietPlanDetailCreateRequest $request) {
        $response = $this->repository->create($request->all());
        return response()->json($response);
    }

    /**
     * Show the form for editing the specified diet plan detail.
     *
     * @param  Modules\Admin\Models\DietPlanDetail $dietPlanDetail
     * @return json encoded Response
     */
    public function edit(DietPlanDetail $dietPlanDetail) {
        $response['success'] = true;
        $dietPlanType = $this->dietPlanRepository->listPlanType()->toArray();
        $dietPlanTypeList = [];
        $dietPlanTypeList = array("1" => "Veg", "2" => "Non Veg");
        if (!empty($dietPlanType)) {
            foreach ($dietPlanType as $key => $diet) {
                $dietPlanTypeList[$diet['id']] = $diet['plan_name'] . " - " . $dietPlanTypeList[$diet['plan_type']] . " - " . $diet['calories'];
            }
        }
        $dietScheduleType = $this->dietScheduleTypeRepository->listScheduleTypes()->toArray();
        $foodList = $this->foodRepository->listFoodData()->toArray();

        $foodTypeList = $this->foodTypeRepository->data()->toArray();
        if (!empty($foodTypeList)) {
            foreach ($foodTypeList as $key => $type) {
                $foodTypeLists[$type['id']] = $type['food_type_name'];
            }
        }
        $foodTypeId = $this->foodRepository->getFoodType($dietPlanDetail->food_id)->toArray();
        $selectedFoodTypeId = $foodTypeId['food_type_id'];
        $response['form'] = view('admin::diet-plan-detail.edit', compact('dietPlanDetail', 'dietPlanTypeList', 'dietScheduleType', 'foodList', 'foodTypeLists', 'selectedFoodTypeId'))->render();



        return response()->json($response);
    }

    /**
     * Store an updated resource in storage.
     *
     * @param  Modules\Admin\Http\Requests\DietPlanDetailCreateRequest $request, Modules\Admin\Models\DietPlanDetail $dietPlanDetail
     * @return json encoded Response
     */
    public function update(DietPlanDetailUpdateRequest $request, DietPlanDetail $dietPlanDetail) {
        $response = $this->repository->update($request->all(), $dietPlanDetail);
        return response()->json($response);
    }

    /**
     * Delete resource from storage.
     *
     * @param  Modules\Admin\Http\Requests\DietPlanDetailDeleteRequest $request
     * @return json encoded Response
     */
    public function destroy(DietPlanDetailDeleteRequest $request, DietPlanDetail $dietPlanDetail) {
        $response = [];
        $result = $this->repository->deleteAction($request->all());
        if ($result) {
            $response = ['status' => 'success', 'message' => trans('admin::messages.deleted', ['name' => trans('admin::controller/diet-plan-detail.diet-plan-detail')])];
        } else {
            $response = ['status' => 'fail', 'message' => trans('admin::messages.not-deleted', ['name' => trans('admin::controller/diet-plan-detail.diet-plan-detail')])];
        }

        return response()->json($response);
    }

}
