<?php
/**
 * The class for managing member activity recommendation specific actions.
 *
 *
 * @author Gauri Deshmukh <gaurid@iprogrammer.com>, Tushar Dahiwale <tushard@iprogrammer.com>
 * @package Admin
 * @since 1.0
 */

namespace Modules\Admin\Http\Controllers;

use Auth;
use Datatables;
use Illuminate\Support\Str;
use Modules\Admin\Models\MemberActivityRecommendation;
use Modules\Admin\Repositories\MemberActivityRecommendationRepository;
use Modules\Admin\Repositories\ActivityTypeRepository;
use Modules\Admin\Repositories\MembersRepository;
use Modules\Admin\Repositories\CenterRepository;
use Modules\Admin\Http\Requests\MemberActivityRecommendationCreateRequest;
use Session;
use Modules\Admin\Services\Helper\MemberHelper;
use Illuminate\Http\Request;

class MemberActivityRecommendationController extends Controller
{

    /**
     * The MemberActivityRecommendationRepository instance.
     *
     * @var Modules\Admin\Repositories\MemberActivityRecommendationRepository
     */
    protected $repository;

    /**
     * The CenterRepository instance.
     *
     * @var Modules\Admin\Repositories\CenterRepository
     */
    protected $centerRepository;

    /**
     * Create a new MemberActivityRecommendationController instance.
     *
     * @param  Modules\Admin\Repositories\MemberActivityRecommendationRepository $repository
     * @return void
     */
    public function __construct(MemberActivityRecommendationRepository $repository, MembersRepository $memberRepository, ActivityTypeRepository $activityTypeRepository, CenterRepository $centerRepository)
    {
        parent::__construct();
        $this->middleware('acl');
        $this->repository = $repository;
        $this->memberRepository = $memberRepository;
        $this->activityTypeRepository = $activityTypeRepository;
        $this->centerRepository = $centerRepository;
    }

    /**
     * Display a listing of the resource.
     *
     * @return view as response
     */
    public function index()
    {
        $memberHelper = new MemberHelper();
        $membersList = [];
        if (Auth::guard('admin')->user()->userType->id == 9 || Auth::guard('admin')->user()->userType->id == 5) {
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
        $activityList = $this->activityTypeRepository->getActivityList()->toArray();
        return view('admin::member-activity-recommendation.index', compact('activityList', 'membersList', 'centersList'));
    }

    /**
     * Get a listing of the resource.
     *
     * @return datatable
     */
    public function getData($customerId, Request $request)
    {
        $params['member_id'] = $customerId;
        Session::set('member_id', $customerId);
        $recommendation = $this->repository->data($params);
        //filter to display own records
        if (Auth::guard('admin')->user()->hasOwnView && (empty(Auth::guard('admin')->user()->hasEdit) || empty(Auth::guard('admin')->user()->hasDelete))) {
            $recommendation = $recommendation->filter(function ($row) {
                return (($row['created_by'] == Auth::guard('admin')->user()->id)) ? true : false;
            });
        }

        return Datatables::of($recommendation)
                ->addColumn('action', function ($recommendation) {
                    return $actionList = "";
                })
                ->addColumn('recommendation_date', function ($recommendation) {
                    return $recommendation_date = date("d-m-Y", strtotime($recommendation['recommendation_date']));
                })
                ->filter(function ($instance) use ($request) {
                    if ($request->has('activity_type')) {
                        $instance->collection = $instance->collection->filter(function ($row) use ($request) {
                            return str_contains(strtolower($row->ActivityType->activity_type), strtolower($request->get('activity_type'))) ? true : false;
                        });
                    }
                    if ($request->has('activity_time_from') && $request->has('activity_time_to')) {
                        $instance->collection = $instance->collection->filter(function ($row) use ($request) {
                            $fromDate = $request->get('activity_time_from');
                            $toDate = $request->get('activity_time_to');
                            return $row['recommendation_date'] >= $fromDate && $row['recommendation_date'] <= $toDate ? true : false;
                        });
                    }

                    if ($request->has('activity_duration')) {
                        $instance->collection = $instance->collection->filter(function ($row) use ($request) {
                            return str_contains(strtolower($row['duration']), strtolower($request->get('activity_duration'))) ? true : false;
                        });
                    }

                    if ($request->has('calories_recommended')) {
                        $instance->collection = $instance->collection->filter(function ($row) use ($request) {
                            return str_contains(strtolower($row['calories_recommended']), strtolower($request->get('calories_recommended'))) ? true : false;
                        });
                    }
                })
                ->make(true);
    }

    /**
     * Display a form to create new member activity recommendation.
     *
     * @return view as response
     */
    public function create()
    {
        return view('admin::member-activity-recommendation.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  Modules\Admin\Http\Requests\MemberActivityRecommendationCreateRequest $request
     * @return json encoded Response
     */
    public function store(MemberActivityRecommendationCreateRequest $request)
    {
        $response = $this->repository->create($request->all());
        return response()->json($response);
    }

    public function getCalories($typeId)
    {
        $response = $this->repository->getCalories($typeId);
        return response()->json($response);
    }
}
