<?php

/**
 * The class for managing member diet plan specific actions.
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
use Modules\Admin\Models\MemberDietPlan;
use Modules\Admin\Repositories\MemberDietPlanRepository;
use Modules\Admin\Repositories\DietScheduleTypeRepository;
use Modules\Admin\Repositories\DietPlanRepository;
use Modules\Admin\Repositories\MembersRepository;
use Modules\Admin\Repositories\FoodTypeRepository;
use Modules\Admin\Repositories\FoodRepository;
use Modules\Admin\Repositories\CenterRepository;
use Modules\Admin\Http\Requests\MemberDietPlanCreateRequest;
use Modules\Admin\Http\Requests\MemberDietPlanUpdateRequest;
use Modules\Admin\Services\Helper\MemberHelper;
use Session;
use Illuminate\Http\Request;
use Validator;

//use GuzzleHttp\Exception\GuzzleException;
//use GuzzleHttp\Client;
//use GuzzleHttp\Psr7;
//use GuzzleHttp\Psr7\Stream;

class MemberDietPlanController extends Controller {

    /**
     * The MemberDietPlanRepository instance.
     *
     * @var Modules\Admin\Repositories\MemberDietPlanRepository
     */
    protected $repository;

    /**
     * The CenterRepository instance.
     *
     * @var Modules\Admin\Repositories\CenterRepository
     */
    protected $centerRepository;

    /**
     * Create a new MemberDietPlanController instance.
     *
     * @param  Modules\Admin\Repositories\MemberDietPlanRepository $repository
     * @return void
     */
    public function __construct(MemberDietPlanRepository $repository, DietPlanRepository $dietPlanRepository, MembersRepository $memberRepository, DietScheduleTypeRepository $dietScheduleTypeRepository, FoodRepository $foodRepository, FoodTypeRepository $foodTyperepository, CenterRepository $centerRepository) {
        parent::__construct();
        $this->middleware('acl');
        $this->repository = $repository;
        $this->dietPlanRepository = $dietPlanRepository;
        $this->memberRepository = $memberRepository;
        $this->dietScheduleTypeRepository = $dietScheduleTypeRepository;
        $this->foodRepository = $foodRepository;
        $this->foodTyperepository = $foodTyperepository;
        $this->centerRepository = $centerRepository;
    }

    /**
     * Display a listing of the resource.
     *
     * @return view as response
     */
    public function index() {
        $acl_flag = !empty(Auth::guard('admin')->user()->hasAdd) ? 1 : 2;
        $userTypeId = Auth::guard('admin')->user()->userType->id;
        $dietPlanType = $this->dietPlanRepository->listPlanType()->toArray();
        $planType = array("1" => "Veg", "2" => "Non Veg");
        if (!empty($dietPlanType)) {
            foreach ($dietPlanType as $key => $diet) {
                $dietPlanTypeList[$diet['id']] = $diet['plan_name'] . " - " . $planType[$diet['plan_type']] . " - " . $diet['calories'];
            }
        }
        $memberID = Session::get('member_id');
        if ("" != $memberID) {
            $dietPlan = $this->repository->getMemberPlan($memberID);
            if (null == $dietPlan || "" == $dietPlan) {
                $dietPlanId = "";
            } else {
                $plan = $dietPlan->toArray();
                $dietPlanId = $plan['diet_plan_id'];
            }
        } else {
            $dietPlanId = "";
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
        $memberHelper = new MemberHelper();
        $membersList = [];
        if (Auth::guard('admin')->user()->userType->id == 9) {
            $centersList = $memberHelper->getCentersList();
            if (Session::get('center_id') != '') {
                $membersList = $this->centerRepository->getMembersList(Session::get('center_id'));
            }
        } elseif (Auth::guard('admin')->user()->userType->id == 7 || Auth::guard('admin')->user()->userType->id == 8) {
            $centers = $memberHelper->getCentersList();
            $centerId = key($centers);
            if (isset($centerId) && $centerId != '') {
                $membersList = $this->centerRepository->getMembersList($centerId);
            }
        } else {
            $membersList = $memberHelper->getUserWiseMemberList();
        }

        $dietPlanCalories = 0;
//        if(!empty($dietPlanId)) {
//            $dietPlanCalories = $this->repository->getDietPlanCalories($dietPlanId);
//            $dietPlanCalories = $dietPlanCalories["calories"];
//        }
        return view('admin::member-diet-plan.index', compact('dietPlanTypeList', 'dietPlanId', 'membersList', 'scheduleTypeList', 'dietPlanCalories', 'centersList', 'userTypeId', 'acl_flag'));
    }

    /**
     * Get a listing of the resource.
     *
     * @return datatable
     */
    public function getData(Request $request) {
        $params["member_id"] = $request->all()["customerId"];
        $params['diet_plan_id'] = $request->all()["dietPlanid"];
        $memberDietPlanDetails = $this->repository->data($params);


        //filter to display own records
        if (Auth::guard('admin')->user()->hasOwnView && (empty(Auth::guard('admin')->user()->hasEdit) || empty(Auth::guard('admin')->user()->hasDelete))) {
            $memberDietPlanDetails = $memberDietPlanDetails->filter(function ($row) {
                return (Str::equals($row['created_by'], Auth::guard('admin')->user()->id)) ? true : false;
            });
        }


        return Datatables::of($memberDietPlanDetails)
                        ->addColumn('check_food', function ($memberDietPlanDetail) {
                            $isChecked = $memberDietPlanDetail->active == 1 ? 'checked="checked"' : NULL;
                            $action = '<input type="checkbox" class="member_diet_plan_items" id="check_food_' . $memberDietPlanDetail->id . '"  name="member_diet_plan[' . $memberDietPlanDetail->id . ']"  ' . $isChecked . ' value=' . $memberDietPlanDetail->active . ' />';
                            return $action;
                        })
                        ->addColumn('action', function ($memberDietPlanDetail) {
//                    dd($memberDietPlanDetail);
                            $actionList = '';
                        if (!empty(Auth::guard('admin')->user()->hasEdit) || (!empty(Auth::guard('admin')->user()->hasOwnEdit) && ($memberDietPlanDetail->created_by == Auth::guard('admin')->user()->id))) {
                           $actionList = '<a href="javascript:;" data-action="edit" data-id="' . $memberDietPlanDetail->id . '" id="' . $memberDietPlanDetail->id . '" class="btn btn-xs default margin-bottom-5 yellow-gold edit-form-link " title="Edit"><i class="fa fa-pencil"></i></a>';
                    }
                           return $actionList;
                        })
                        ->addColumn('food_name', function ($memberDietPlanDetail) {
                            $isChecked = $memberDietPlanDetail->active == 1 ? 'checked="checked"' : NULL;
                            $food_name = $memberDietPlanDetail->food_name;
                            //$action = '<input type="text" name="member_diet_plan[]"  value='.$memberDietPlanDetail->active.' />';
                            $schedule_name = str_replace(' ', '_', $memberDietPlanDetail->schedule_name);
                            $action = "";
                            $action .= '<input type="hidden" class="diet_schedule_type_id" name="diet_schedule_type_id[' . $memberDietPlanDetail->id . ']" value=' . $memberDietPlanDetail->diet_schedule_type_id . '>';
                            $action .= '<input type="hidden" class="unique_diet_plan_id" name="diet_plan_row_id[' . $memberDietPlanDetail->id . ']" value=' . $memberDietPlanDetail->id . '>';
                            $action .= '<input type="hidden" class="food_id" name="food_id[' . $memberDietPlanDetail->id . ']" value=' . $memberDietPlanDetail->food_id . '>';
                            $action .= '<input type="hidden" class="unit_calories unit_calories_' . $memberDietPlanDetail->id . '" name="calories[' . $memberDietPlanDetail->id . ']" value=' . $memberDietPlanDetail->calories . '>';
                            $action .= '<tr class="schedule_' . $schedule_name . '"></tr>';
                            return $action . " " . $food_name;
                        })
                        ->addColumn('servings_recommended', function ($memberDietPlanDetail) {
                            $action = '<input type="text" class="form-control servings_recommended unit_servings_' . $memberDietPlanDetail->id . '" name="servings_recommended[' . $memberDietPlanDetail->id . ']"  value=' . $memberDietPlanDetail->servings_recommended . '>';
                            return $action;
                        })
                        ->addColumn('total_calories', function ($memberDietPlanDetail) {
                            $total_calories = ($memberDietPlanDetail->calories) * ($memberDietPlanDetail->servings_recommended);
                            $action = "<span class='total_calories'>" . $total_calories . "</span>";
                            return $action;
                        })
                        ->make(true);

//        return Datatables::of($memberDietPlanDetails)
//                        ->addColumn('action', function ($dietPlanDetails) {
//                            $actionList = '';
//                            return $actionList;
//                        })
//                        ->filter(function ($instance) use ($request) {
//                            if ($request->has('schedule_dropdown')) {
//                                $instance->collection = $instance->collection->filter(function ($row) use ($request) {
//                                    return str_contains(strtolower($row['DietScheduleType']['schedule_name']), strtolower($request->get('schedule_dropdown'))) ? true : false;
//                                });
//                            }
//
//                            if ($request->has('food_name')) {
//                                $instance->collection = $instance->collection->filter(function ($row) use ($request) {
//                                    return str_contains(strtolower($row['Food']['food_name']), strtolower($request->get('food_name'))) ? true : false;
//                                });
//                            }
//                            if ($request->has('servings_recommended')) {
//                                $instance->collection = $instance->collection->filter(function ($row) use ($request) {
//                                    return str_contains(strtolower($row['servings_recommended']), strtolower($request->get('servings_recommended'))) ? true : false;
//                                });
//                            }
//                        })
//                        ->make(true);
    }

    /**
     * Display a listing of the resource.
     *
     * @return json encoded response
     */
    public function getPlan($memberID) {
        Session::set('member_id', $memberID);
        $dietPlanType = $this->dietPlanRepository->listPlanType()->toArray();
        $planType = array("1" => "Veg", "2" => "Non Veg");
        if (!empty($dietPlanType)) {
            foreach ($dietPlanType as $key => $diet) {
                $dietPlanTypeList[$diet['id']] = $diet['plan_name'] . " - " . $planType[$diet['plan_type']] . " - " . $diet['calories'];
            }
        }
        $dietPlan = $this->repository->getMemberPlan($memberID);
        if (!empty($dietPlan)) {
            $dietPlan = $dietPlan->toArray();
            $dietPlanId = $dietPlan['diet_plan_id'];            
        } else {
            $dietPlanId = "";
        }
        $dietPlanCalories = 0;
        if (!empty($dietPlanId)) {
            $dietPlanCalories = $this->repository->getDietPlanCalories($dietPlanId);
            $dietPlanCalories = $dietPlanCalories["calories"];
        }

        $created_At = $this->repository->getDietPlanDate($memberID);
        $response['list'] = View('admin::member-diet-plan.plandropdown', compact('dietPlanId', 'dietPlanTypeList', 'dietPlanCalories'))->render();
        $response['diet_plan_id'] = $dietPlanId;
        $response['diet_plan_calories'] = $dietPlanCalories;
        $response['created_at'] = $created_At;
        return response()->json($response);
    }

    public function getCalories(Request $request) {
        $dietPlanId = $request->all()["diet_plan_id"];
        if (!empty($dietPlanId)) {
            $dietPlanCalories = $this->repository->getDietPlanCalories($dietPlanId);
            $dietPlanCalories = $dietPlanCalories["calories"];
        }
        $response["diet_plan_calories"] = $dietPlanCalories;
        return response()->json($response);
    }

    /**
     * Display a form to create new member diet plan
     *
     * @return view as response
     */
    public function create() {
        return view('admin::member-diet-plan.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  Modules\Admin\Http\Requests\MemberDietPlanUpdateRequest $request
     * @return json encoded Response
     */
    public function store(Request $request, MemberDietPlan $memberDietPlan) {

        $requestParamas = $request->all();
        $serving = config('settings.APP_SERVING_SIZE_LIMIT');
        print_r($requestParamas['member_diet_plan']);
        $checked_diet_food_items = explode(",", $requestParamas['member_diet_plan']);

        $requestParamas['diet_schedule_type_id'] = array_values($requestParamas['diet_schedule_type_id']);
        $requestParamas['food_id'] = array_values($requestParamas['food_id']);
        $requestParamas['servings_recommended'] = array_values($requestParamas['servings_recommended']);


        $rules['id'] = 'required';
        $rules['diet_plan_id'] = 'required';

        // Create Rules for food_id
        foreach ($requestParamas['food_id'] as $key => $val) {
            if (isset($checked_diet_food_items[$key]) && $checked_diet_food_items[$key]) {
                $rules['food_id.' . $key] = 'required';
            }
        }

        // Create Rules for servings_recommended
        foreach ($requestParamas['servings_recommended'] as $key => $val) {
            if (isset($checked_diet_food_items[$key]) && $checked_diet_food_items[$key]) {
                $rules['servings_recommended.' . $key] = 'required|numeric';
                $rules['servings_recommended.' . $key] = 'required|integer|between:1,' . $serving;
            }
        }

        $messages = [];
        $messages['id.required'] = 'Please Select Customer.';
        $messages['diet_plan_id.required'] = 'Please Select Diet Plan.';

        foreach ($requestParamas['food_id'] as $key => $val) {
            if (isset($checked_diet_food_items[$key]) && $checked_diet_food_items[$key]) {
                $messages['food_id.' . $key . '.required'] = 'Please Select Food.';
            }
        }

        foreach ($requestParamas['servings_recommended'] as $key => $val) {
            if (isset($checked_diet_food_items[$key]) && $checked_diet_food_items[$key]) {
                $messages['servings_recommended.' . $key . '.required'] = 'Please Enter Servings Recommended.';
                $messages['servings_recommended.' . $key . '.integer'] = 'Servings Recommended should be integer.';
                $messages['servings_recommended.' . $key . '.between'] = 'Servings Recommended should not be greater than ' . $serving;
            }
        }

        $validationArray['id'] = $requestParamas['id'];
        $validationArray['diet_plan_id'] = $requestParamas['diet_plan_id'];
        $validationArray['servings_recommended'] = $requestParamas['servings_recommended'];
        $validationArray['food_id'] = $requestParamas['food_id'];


        //$validator=Validator::make($request->only('id', 'diet_plan_id', 'servings_recommended', 'food_id'), $rules, $messages);
        $validator = Validator::make($validationArray, $rules, $messages);

        if ($validator->fails()) {
            $validation_message = $validator->errors()->first();
            $response['status'] = 'error';
            $response['message'] = $validation_message;
        } else {
            $response = $this->repository->update($requestParamas, $memberDietPlan);
        }
        return response()->json($response);
    }

    /**
     * Show the form for editing the specified member diet plan.
     *
     * @param  Modules\Admin\Models\MemberDietPlan $memberDietPlan
     * @return json encoded Response
     */
    public function edit(MemberDietPlan $memberDietPlan) {

        $response['success'] = true;
        $dietPlanType = $this->dietPlanRepository->listPlanType()->toArray();
        $foodTypeList = $this->foodTyperepository->listFoodTypesData()->toArray();

        $planType = array("1" => "Veg", "2" => "Non Veg");
        if (!empty($dietPlanType)) {
            foreach ($dietPlanType as $key => $diet) {
                $dietPlanTypeList[$diet['id']] = $diet['plan_name'] . " - " . $planType[$diet['plan_type']] . " - " . $diet['calories'];
            }
        }
        $RowId = $memberDietPlan->id;
        $dietPlan = $this->repository->getDietPlan($RowId);
        $dietPlanId = $dietPlan->diet_plan_id;
        $dietScheduleTypeId = $dietPlan->diet_schedule_type_id;
        $foodTypeId = $dietPlan->foodtypeid;
        $foodId = $dietPlan->food_id;
        $servingsRecommended = $dietPlan->servings_recommended;
        $foodDetails = $this->repository->getFoodDetails($foodId);
        $measure= $foodDetails->measure;
        $calories = $foodDetails->calories;
        $total_calories = $servingsRecommended * $calories;
        $listMemberData = $this->repository->listMemberDietPlanDetails()->toArray();
        foreach ($listMemberData as $key => $member) {
            $memberList[$member['id']] = $member['first_name'] . " " . $member['last_name'];
        }
        $response['form'] = view('admin::member-diet-plan.edit', compact('memberDietPlan', 'dietPlanTypeList', 'memberList','dietScheduleTypeId', 'dietPlanId', 'RowId', 'foodTypeList','foodTypeId','foodId', 'servingsRecommended','measure','calories','total_calories'))->render();
        $response['foodId'] = $foodId;
        return response()->json($response);
    }

    public function update(Request $request, MemberDietPlan $memberDietPlan)
    {
        $requestParamas = $request->all();

        $serving = config('settings.APP_SERVING_SIZE_LIMIT');

        $rules['id'] = 'required';
        $rules['diet_plan_id'] = 'required';
        $rules['food_id'] = 'required';
        $rules['servings_recommended'] = 'required|numeric';
        $rules['servings_recommended'] = 'required|integer|between:1,'.$serving;

//        // Create Rules for food_id
//        foreach ($requestParamas['food_id'] as $key => $val) {
//            if (isset($checked_diet_food_items[$key]) && $checked_diet_food_items[$key]) {
//                $rules['food_id.' . $key] = 'required';
//            }
//        }
//
//        // Create Rules for servings_recommended
//        foreach ($requestParamas['servings_recommended'] as $key => $val) {
//            if (isset($checked_diet_food_items[$key]) && $checked_diet_food_items[$key]) {
//                $rules['servings_recommended.' . $key] = 'required|numeric';
//                $rules['servings_recommended.' . $key] = 'required|integer|between:1,' . $serving;
//            }
//        }

        $messages = [];
        $messages['id.required'] = 'Please Select Customer.';
        $messages['diet_plan_id.required'] = 'Please Select Diet Plan.';
        $messages['food_id.required'] = 'Please Select Food.';
        $messages['servings_recommended.required'] = 'Please Enter Servings Recommended.';
        $messages['servings_recommended.integer'] = 'Servings Recommended should be integer.';
        $messages['servings_recommended.between'] = 'Servings Recommended should not be greater than ' . $serving;

//        foreach ($requestParamas['food_id'] as $key => $val) {
//            if (isset($checked_diet_food_items[$key]) && $checked_diet_food_items[$key]) {
//                $messages['food_id.' . $key . '.required'] = 'Please Select Food.';
//            }
//        }
//
//        foreach ($requestParamas['servings_recommended'] as $key => $val) {
//            if (isset($checked_diet_food_items[$key]) && $checked_diet_food_items[$key]) {
//                $messages['servings_recommended.' . $key . '.required'] = 'Please Enter Servings Recommended.';
//                $messages['servings_recommended.' . $key . '.integer'] = 'Servings Recommended should be integer.';
//                $messages['servings_recommended.' . $key . '.between'] = 'Servings Recommended should not be greater than ' . $serving;
//            }
//        }

        $validationArray['id'] = $requestParamas['id'];
        $validationArray['diet_plan_id'] = $requestParamas['diet_plan_id'];
        $validationArray['servings_recommended'] = $requestParamas['servings_recommended'];
        $validationArray['food_id'] = $requestParamas['food_id'];


        //$validator=Validator::make($request->only('id', 'diet_plan_id', 'servings_recommended', 'food_id'), $rules, $messages);
        $validator = Validator::make($validationArray, $rules, $messages);

        if ($validator->fails()) {
            $validation_message = $validator->errors()->first();
            $response['status'] = 'error';
            $response['message'] = $validation_message;
        } else {
            $response = $this->repository->updateDietPlan($requestParamas, $memberDietPlan);
        }
        return response()->json($response);
    }

    public function getFoodList(Request $request) {
        $dietScheduleTypeId = $request->all()["diet_schedule_type_id"];
        $myRowId = $request->all()["myRowId"];
        $maxDietPlanRowId = $request->all()["maxDietPlanRowId"];

        //$dieticianFoodList = $this->repository->getDieticianFoods()->toArray();
        //$dieticianFoodList[0] = "Other";

        $foodTypeList = $this->foodTyperepository->listFoodTypesData()->toArray();


        $response['success'] = true;
        $response['form'] = view('admin::member-diet-plan.select-newfood', compact('foodTypeList', 'dietScheduleTypeId', 'myRowId', 'maxDietPlanRowId'))->render();

        return response()->json($response);
    }

    public function getFoodListByFoodType(Request $request) {
        $params['food_type_id'] = $request->all()["food_type_id"];
        $uniqueId = $request->all()["unique_id"];
        $foodId = $request->all()["type"];
        $foodList = $this->repository->getDieticianFoods($params)->toArray();
        if($foodId == ''){
            $foodListId = "";
        }else{
            $foodListId = $foodId;
        }

        //$foodList = json_decode(json_encode($foodList), true);
        $foodList[0] = "Other";
        $response['food_list'] = View('admin::member-diet-plan.foodlist', compact('foodList', 'uniqueId','foodListId'))->render();
        return response()->json($response);
    }

    public function addDieticianFood(Request $request) {
        $dietScheduleTypeId = $request->all()["diet_schedule_type_id"];
        $maxDietPlanRowId = $request->all()["max_diet_plan_row_id"];
        $selected_food_type_id = $request->all()["selected_food_type_id"];
        $foodId = "";
        $dieticianId = Auth::guard('admin')->user()->id;
        $foodTypeList = $this->foodTyperepository->listFoodTypesData()->toArray();
        $response['success'] = true;
        $response['form'] = view('admin::member-diet-plan.add-newfood', compact('dieticianId', 'dietScheduleTypeId', 'foodId', 'maxDietPlanRowId', 'foodTypeList', 'selected_food_type_id'))->render();
        return response()->json($response);
    }

    public function getFoodDetails($foodId) {
        $params['food_id'] = $foodId;
        $foodDetails = $this->foodRepository->getFoodDetails($params)->toArray();
        $response['measure'] = ucwords($foodDetails[0]['measure']);
        $response['calories'] = $foodDetails[0]['calories'];
        $response['serving_size'] = $foodDetails[0]['serving_size'];
        $response['serving_unit'] = $foodDetails[0]['serving_unit'];
        return response()->json($response);
    }



}
