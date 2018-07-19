<?php

/**
 * The class for managing member activity log details specific actions.
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
use Modules\Admin\Models\MemberActivityLog;
use Modules\Admin\Repositories\MemberActivityLogRepository;
use Modules\Admin\Repositories\MembersRepository;
use Modules\Admin\Repositories\ActivityTypeRepository;
use Modules\Admin\Repositories\CenterRepository;
use Modules\Admin\Http\Requests\MemberActivityLogCreateRequest;
use Modules\Admin\Http\Requests\MemberActivityLogUpdateRequest;
use Modules\Admin\Http\Requests\MemberActivityLogDeleteRequest;
use Illuminate\Http\Request;
use Modules\Admin\Services\Helper\MemberHelper;
use Session;

class MemberActivityLogController extends Controller {

    /**
     * The MemberActivityLogRepository instance.
     *
     * @var Modules\Admin\Repositories\MemberActivityLogRepository
     */
    protected $repository;
    protected $activityType;
    /**
     * The CenterRepository instance.
     *
     * @var Modules\Admin\Repositories\CenterRepository
     */
    protected $centerRepository;
    /**
     * Create a new MemberActivityLogController instance.
     *
     * @param  Modules\Admin\Repositories\MemberActivityLogRepository $repository
     * @return void
     */
    public function __construct(MemberActivityLogRepository $repository, ActivityTypeRepository $activityTypeRepository, MembersRepository $memberRepository, CenterRepository $centerRepository) {
        parent::__construct();
        $this->middleware('acl');
        $this->repository = $repository;
        $this->activityTypeRepository = $activityTypeRepository;
        $this->memberRepository = $memberRepository;
        $this->centerRepository = $centerRepository;
    }

    /**
     * Display a listing of the resource.
     *
     * @return view as response
     */
    public function index() {
        $memberHelper = new MemberHelper();
        $membersList = [];
//        if(Auth::guard('admin')->user()->userType->id == 9 || Auth::guard('admin')->user()->userType->id == 5) {
//            $centersList = $memberHelper->getCentersList();
//            if (Session::get('center_id') != '') {
//                $membersList = $this->centerRepository->getMembersList(Session::get('center_id'));
//            }
//        }  elseif (Auth::guard('admin')->user()->userType->id == 7 || Auth::guard('admin')->user()->userType->id == 8) {
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
        
        $date = \Carbon\Carbon::now()->format('d-m-Y');
        return view('admin::member-activity-log.index', compact('membersList', 'date', 'centersList'));
    }

    /**
     * Get a listing of the resource.
     *
     * @return datatable
     */
    public function getData(Request $request) {
        $memberID = $request->input('customerId');
        $date = date("Y-m-d", strtotime($request->input('date')));
        Session::set('member_id', $memberID);
        $params['member_id'] = $memberID;
        $params['date'] = $date;

        $memberActivityLogs = $this->repository->data($params);
        $activityTypes = $this->activityTypeRepository->data()->toArray();
        //filter to display own records
        if (Auth::guard('admin')->user()->hasOwnView && (empty(Auth::guard('admin')->user()->hasEdit) || empty(Auth::guard('admin')->user()->hasDelete))) {
            $memberActivityLogs = $memberActivityLogs->filter(function ($row) {
                return (Str::equals($row['created_by'], Auth::guard('admin')->user()->id)) ? true : false;
            });
        }

        return Datatables::of($memberActivityLogs)
                        ->filter(function ($instance) use ($request) {
                            if ($request->has('activity')) {
                                $instance->collection = $instance->collection->filter(function ($row) use ($request) {
                                    return str_contains(strtolower($row['activity']), strtolower($request->get('activity'))) ? true : false;
                                });
                            }
                            if ($request->has('duration')) {
                                $instance->collection = $instance->collection->filter(function ($row) use ($request) {
                                    return str_contains(strtolower($row['duration']), strtolower($request->get('duration'))) ? true : false;
                                });
                            }

                            if ($request->has('login_in_time_from') && $request->has('login_in_time_to')) {
                                $instance->collection = $instance->collection->filter(function ($row) use ($request) {
//                                    $fromDateArray = explode(" ", $request->get('login_in_time_from'));
//                                    $toDateArray = explode(" ", $request->get('login_in_time_to'));
                                    $fromDateArray = $request->get('login_in_time_from');
                                    $toDateArray = $request->get('login_in_time_to');
                                    $fromDate = date('Y-m-d', strtotime($fromDateArray));
                                    $toDate = date('Y-m-d', strtotime($toDateArray));
                                    return $row['activity_date'] >= $fromDate && $row['activity_date'] <= $toDate ? true : false;
                                });
                            }
                        })
                        ->addColumn('activity_date', function ($memberActivityLogs) use($activityTypes) {
                            $startTime = date('d/m/Y', strtotime($memberActivityLogs->activity_date)) . " - " . date('h:i A', strtotime($memberActivityLogs->start_time));
                            return $startTime;
                        })
                        ->addColumn('status', function ($memberActivityLogs) {
                            $status = ($memberActivityLogs->status == 1) ? '<span class="label label-sm label-success">' . trans('admin::messages.active') . '</span>' : '<span class="label label-sm label-danger">' . trans('admin::messages.inactive') . '</span>';
                            return $status;
                        })
                        ->addColumn('action', function ($memberActivityLogs) {
                            $actionList = '';
                            return $actionList;
                        })
                        ->make(true);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  Modules\Admin\Http\Requests\MemberActivityLogCreateRequest $request
     * @return json encoded Response
     */
    public function getDeviation(Request $request) {
        $params['client_id'] = $request->input('client_id');
        $params['date'] = $request->input('date');
        $memberCalories = $this->repository->getMemberCalories($params);
        $recommendedCalories = $this->repository->getRecommendedCalories($params);
        $response = $recommendedCalories->sum('calories_recommended') - $memberCalories->sum('calories_burned');
        return response()->json($response);
    }

}
