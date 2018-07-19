<?php

/**
 * The class for managing member diet log
 *
 *
 * @author Gauri Deshmukh <gaurid@iprogrammer.com>
 * @package Admin
 * @since 1.0
 */

namespace Modules\Admin\Http\Controllers;

use Illuminate\Http\Request;
use Modules\Admin\Repositories\MemberDietLogRepository;
use Modules\Admin\Repositories\DietScheduleTypeRepository;
use Modules\Admin\Repositories\MemberDietPlanRepository;
use Modules\Admin\Repositories\FoodRepository;
use Modules\Admin\Repositories\MembersRepository;
use Modules\Admin\Repositories\DeviationRepository;
use Modules\Admin\Repositories\CenterRepository;
use Modules\Admin\Http\Requests\MemberDietLogCreateRequest;
use Modules\Admin\Models\MemberDietLog;
use Modules\Admin\Services\Helper\ImageHelper;
use Datatables;
use Illuminate\Support\Str;
use Auth;
use DB;
use Modules\Admin\Services\Helper\MemberHelper;
use Session;
use Modules\Admin\Repositories\FoodTypeRepository;

class MemberDietLogController extends Controller {

    /**
     * The MemberDietLogController instance.
     *
     * @var Modules\Admin\Repositories\MemberDietLogRepository
     */
    protected $repository;
    protected $dietScheduleType;

    /**
     * The CenterRepository instance.
     *
     * @var Modules\Admin\Repositories\CenterRepository
     */
    protected $centerRepository;

    public function __construct(MemberDietLogRepository $repository, DietScheduleTypeRepository $dietScheduleTypeRepository, MembersRepository $memberRepository, FoodRepository $foodRepository, DeviationRepository $deviationRepository, MemberDietPlanRepository $memberDietPlanRepository, FoodTypeRepository $foodTypeRepository, CenterRepository $centerRepository) {
        parent::__construct();
        $this->middleware('acl');
        $this->repository = $repository;
        $this->dietScheduleTypeRepository = $dietScheduleTypeRepository;
        $this->memberRepository = $memberRepository;
        $this->foodRepository = $foodRepository;
        $this->deviationRepository = $deviationRepository;
        $this->memberDietPlanRepository = $memberDietPlanRepository;
        $this->foodTypeRepository = $foodTypeRepository;
        $this->centerRepository = $centerRepository;
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index($parameter=0) {
        $acl_flag = !empty(Auth::guard('admin')->user()->hasAdd) ? 1 : 2;
        $logged_in_by_user_type = Auth::guard('admin')->user()->userType->id;
        //$parameter = $request->input('mid');
        if ($parameter!=0) {
            $slug = explode("-", $parameter);
            $sdate = date('Y-m-d', strtotime($slug[0]));
            $date = date('d-m-Y', strtotime($slug[0]));
            $selectedMember = $slug[1];
            Session::set('member_id', $selectedMember);
            $params['mid'] = $selectedMember;
            $params['date'] = $date;
            $TotalDeviation = $this->deviationRepository->getTotalDeviation($params);
            $deviation = $TotalDeviation->sum('calories_consumed') - $TotalDeviation->sum('calories_recommended');
            //to get member diet plan id
            $dietPlan = $this->memberDietPlanRepository->getMemberPlan($selectedMember);

            if (0 == $deviation) {
                $scheduleList = $this->dietScheduleTypeRepository->listScheduleTypes()->toArray();
            } else {
                //get last schedule type id from schedule master to compr with deviation schedule type id
                $lastScheduleType = $this->dietScheduleTypeRepository->getLastScheduleType()->toArray();
                $params['schedule_id'] = $lastScheduleType['id'];
                $scheduleList = $this->deviationRepository->checkSchedulTypeId($params)->toArray();
            }
            $currentDate = \Carbon\Carbon::now()->format('Y-m-d');
            if ($sdate == $currentDate) {
                $btn_text = 'Send Diet Recommendation';
                $style = 0;
            } else {
                $btn_text = 'View Recommendations Sent';
                $style = 1;
            }
            $button = "";
            $warnig_message = "";
            if (!empty($dietPlan)) {
                $memberDietPlan = $dietPlan->toArray();
                $memberDietPlanId = $memberDietPlan['diet_plan_id'];
            } else {
                $memberDietPlanId = "";
            }
        } else {
            $date = \Carbon\Carbon::now()->format('d-m-Y');
            $memberID = Session::get('member_id');
            if ('' != $memberID) {
                $params['mid'] = $memberID;
                $params['date'] = \Carbon\Carbon::now()->format('Y-m-d');
                $TotalDeviation = $this->deviationRepository->getTotalDeviation($params);
                $deviation = $TotalDeviation->sum('calories_consumed') - $TotalDeviation->sum('calories_recommended');
                //to get member diet plan id
                $dietPlan = $this->memberDietPlanRepository->getMemberPlan($memberID);

                if (0 == $deviation) {
                    $scheduleList = $this->dietScheduleTypeRepository->listScheduleTypes()->toArray();
                } else {
                    //get last schedule type id from schedule master to comapir with deviation schedule type id
                    $lastScheduleType = $this->dietScheduleTypeRepository->getLastScheduleType()->toArray();
                    $params['schedule_id'] = $lastScheduleType['id'];
                    $scheduleList = $this->deviationRepository->checkSchedulTypeId($params)->toArray();
                }
                if (!empty($dietPlan)) {
                    $memberDietPlan = $dietPlan->toArray();
                    $memberDietPlanId = $memberDietPlan['diet_plan_id'];
                    $button = 0;
                    $warnig_message = "";
                } else {
                    $memberDietPlanId = "";
                    $button = 1;
                    $warnig_message = "No diet plan assigned to this user.";
                }
                $btn_text = 'Send Diet Recommendation';
            } else {
                $deviation = "";
                $memberDietPlanId = "";
                $scheduleList = array();
                $button = "";
                $warnig_message = "";
                $btn_text = '';
            }
            $style = 0;
        }
        if (Auth::guard('admin')->user()->userType->id == 9 && Session::get('member_id') != '' && Session::get('center_id') == '') {
            $centerId = $this->centerRepository->getCenterId(Session::get('member_id'));
            if ($centerId[0]->center_id != '') {
                Session::set('center_id', $centerId[0]->center_id);
            }
        }

        $memberHelper = new MemberHelper();
        $membersList = [];
//        if (Auth::guard('admin')->user()->userType->id == 9) {
//            $centersList = $memberHelper->getCentersList();
//            if (Session::get('center_id') != '') {
//                $membersList = $this->centerRepository->getMembersList(Session::get('center_id'));
//            }
//        } elseif (Auth::guard('admin')->user()->userType->id == 7 || Auth::guard('admin')->user()->userType->id == 8) {
//            $centers = $memberHelper->getCentersList();
//            $centerId = key($centers);
//            if (isset($centerId) && $centerId != '') {
//                $membersList = $this->centerRepository->getMembersList($centerId);
//            }
//        } else {
//            $membersList = $memberHelper->getUserWiseMemberList();
//        }
        
       if(count($memberHelper->getCentersList()) > 1) {
            $centersList = $memberHelper->getCentersList();            
            if (Session::get('center_id') != '') {
                $membersList = $this->centerRepository->getMembersList(Session::get('center_id'));
            }
        } elseif ((Auth::guard('admin')->user()->userType->id == 4) || (Auth::guard('admin')->user()->userType->id == 5) || (Auth::guard('admin')->user()->userType->id == 7) || (Auth::guard('admin')->user()->userType->id == 8) || (Auth::guard('admin')->user()->userType->id == 9) || (Auth::guard('admin')->user()->userType->id == 11)) {
            $centersList = $memberHelper->getCentersList();
            $centerId = key($centersList);
            if (isset($centerId) && $centerId != '') {
                $membersList = $this->centerRepository->getMembersList($centerId);
            }    
        } else {
            $membersList = $memberHelper->getUserWiseMemberList();
        }

        $foodList = array();
        $rowCount = 1;
        $foodTypeLists = array();
        $foodTypeList = $this->foodTypeRepository->data()->toArray();
        if (!empty($foodTypeList)) {
            foreach ($foodTypeList as $key => $type) {
                $foodTypeLists[$type['id']] = $type['food_type_name'];
            }
        }
        return view('admin::member-diet-log.index', compact('membersList', 'date', 'selectedMember', 'foodList', 'rowCount', 'deviation', 'memberDietPlanId', 'button', 'scheduleList', 'warnig_message', 'btn_text', 'style', 'foodTypeLists', 'centersList', 'logged_in_by_user_type', 'acl_flag'));
    }

    /**
     * Display a listing of the resource.
     *
     * @param  Datatables $datatables
     * @return Response
     */
    public function getData(Request $request) {
        $params['member_id'] = $request->input('customerId');
        Session::set('member_id', $params['member_id']);
        $date = date("Y-m-d", strtotime($request->input('date')));
        $params['date'] = $date;
        $memberDietLog = $this->repository->data($params, $request->all());
        return Datatables::of($memberDietLog)->make(true);
    }

    /**
     * Display a listing of the resource.
     *
     * @return json encoded response
     */
    public function getFoodList(Request $request) {
        $params['food_type_id'] = $request->input('food_type_id');
        $foodList = $this->memberDietPlanRepository->getDieticianFoods($params)->toArray();
        $response['list'] = View('admin::member-diet-log.member-food-type-list', compact('foodList'))->render();
        return response()->json($response);
    }

    /**
     * Display a listing of the diet recommendation.
     *
     * @param  Datatables $datatables
     * @return Response
     */
    public function getMemberDietRecommendation(Request $request) {
        $params['member_id'] = $request->input('customerId');
        $memberID = $request->input('customerId');
        $date = date("Y-m-d", strtotime($request->input('date')));
        $params['date'] = $date;
        $dietPlan = $this->memberDietPlanRepository->getMemberPlan($memberID);
        if (!empty($dietPlan)) {
            $memberDietPlan = $dietPlan->toArray();
            $params['member_diet_plan_id'] = $memberDietPlan['diet_plan_id'];
        } else {
            $params['member_diet_plan_id'] = '0';
        }

        $memberDietRecommendation = $this->repository->getMemberDietRecommendations($params);
        return Datatables::of($memberDietRecommendation)
                        ->addColumn('servings_recommended', function ($memberDietRecommendation) {
                            $ServingsRecommended = $memberDietRecommendation['servings_recommended'] . ' (' . $memberDietRecommendation['schedule_name'] . ')';
                            return $ServingsRecommended;
                        })
                        ->make(true);
    }

    /**
     * Display a listing of the food details.
     *
     * @param  Datatables $datatables
     * @return Response
     */
    public function getFoodDetails($foodId) {
        $params['food_id'] = $foodId;
        $foodDetails = $this->foodRepository->getFoodDetails($params)->toArray();
        $response['measure'] = ucwords($foodDetails[0]['measure']);
        $response['calories'] = $foodDetails[0]['calories'];
        return response()->json($response);
    }

    /**
     * Display a listing of the diet plan details.
     *
     * @param  Datatables $datatables
     * @return Response
     */
    public function getDietPlanDetails($mid) {
        Session::set('member_id', $mid);
        $params['mid'] = $mid;
        $params['date'] = \Carbon\Carbon::now()->format('Y-m-d');
        $TotalDeviation = $this->deviationRepository->getTotalDeviation($params);
        $deviation = $TotalDeviation->sum('calories_consumed') - $TotalDeviation->sum('calories_recommended');
        //to get member diet plan id
        $dietPlan = $this->memberDietPlanRepository->getMemberPlan($mid);

        if (0 == $deviation) {
            $scheduleList = $this->dietScheduleTypeRepository->listScheduleTypes()->toArray();
            $response['deviation'] = 0;
        } else {
            //get last schedule type id from schedule master to comapir with deviation schedule type id
            $lastScheduleType = $this->dietScheduleTypeRepository->getLastScheduleType()->toArray();
            $params['schedule_id'] = $lastScheduleType['id'];
            $scheduleList = $this->deviationRepository->checkSchedulTypeId($params)->toArray();
            $response['deviation'] = $deviation;
        }

        if (!empty($dietPlan)) {
            $memberDietPlan = $dietPlan->toArray();
            $response['memberDietPlan'] = $memberDietPlan['diet_plan_id'];
        } else {
            $response['memberDietPlan'] = '0';
        }
        $response['rowCount'] = 1;
        $response['list'] = View('admin::member-diet-log.dropdown', compact('scheduleList'))->render();
        return response()->json($response);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  Modules\Admin\Http\Requests\MemberDietLogCreateRequest $request
     * @return json encoded Response
     */
    public function store(MemberDietLogCreateRequest $request) {
        $response = $this->repository->create($request->all());

        return response()->json($response);
    }

}
