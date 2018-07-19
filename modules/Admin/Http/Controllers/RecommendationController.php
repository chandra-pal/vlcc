<?php

/**
 * The class for managing recommendation specific actions.
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
use Modules\Admin\Models\Recommendation;
use Modules\Admin\Repositories\RecommendationRepository;
use Modules\Admin\Repositories\CenterRepository;
use Modules\Admin\Repositories\MembersRepository;
use Modules\Admin\Http\Requests\RecommendationCreateRequest;
use Modules\Admin\Http\Requests\RecommendationUpdateRequest;
use Modules\Admin\Http\Requests\RecommendationDeleteRequest;
use Session;
use Modules\Admin\Services\Helper\MemberHelper;
use Illuminate\Http\Request;

class RecommendationController extends Controller {

    /**
     * The RecommendationRepository instance.
     *
     * @var Modules\Admin\Repositories\RecommendationRepository
     */
    protected $repository;

    /**
     * The CenterRepository instance.
     *
     * @var Modules\Admin\Repositories\CenterRepository
     */
    protected $centerRepository;

    /**
     * Create a new RecommendationController instance.
     *
     * @param  Modules\Admin\Repositories\RecommendationRepository $repository
     * @return void
     */
    public function __construct(RecommendationRepository $repository, MembersRepository $memberRepository, CenterRepository $centerRepository) {
        parent::__construct(['except' => ['sendNotification']]);
        $this->middleware('acl', ['except' => ['sendNotification']]);
        $this->repository = $repository;
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
//        if (Auth::guard('admin')->user()->userType->id == 9 || Auth::guard('admin')->user()->userType->id == 5) {
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
        
        $messageType = array('1' => 'General Notification', '2' => 'Activity Recommendation', '3' => 'Diet Recommendation');
        return view('admin::recommendation.index', compact('messageType', 'membersList', 'centersList'));
    }

    /**
     * Get a listing of the resource.
     *
     * @return datatable
     */
    public function getData($mid, Request $request) {
        $params['member_id'] = $mid;
        Session::set('member_id', $mid);
        $recommendation = $this->repository->data($params);
        //filter to display own records
        if (Auth::guard('admin')->user()->hasOwnView && (empty(Auth::guard('admin')->user()->hasEdit) || empty(Auth::guard('admin')->user()->hasDelete))) {
            $recommendation = $recommendation->filter(function ($row) {
                return (Str::equals($row['created_by'], Auth::guard('admin')->user()->id)) ? true : false;
            });
        }

        return Datatables::of($recommendation)
                        ->addColumn('status', function ($recommendation) {
                            $status = ($recommendation->status == 1) ? '<span class="label label-sm label-success">' . trans('admin::messages.active') . '</span>' : '<span class="label label-sm label-danger">' . trans('admin::messages.inactive') . '</span>';
                            return $status;
                        })
                        ->addColumn('message_type', function ($recommendation) {
                            $message_type = '';

                            switch ($recommendation->message_type) {
                                case 1:
                                    $message_type = 'General Notification';
                                    break;
                                case 2:
                                    $message_type = 'Activity Recommendation';
                                    break;
                                case 3:
                                    $message_type = 'Diet Recommendation';
                                    break;
                                case 4:
                                    $message_type = 'Session Recommendation';
                                    break;
                                default:
                                    $message_type = 'General Notification';
                            };

                            return $message_type;
                        })
                        ->addColumn('action', function ($dietPlanDetails) {
                            $actionList = '';
                            return $actionList;
                        })
                        ->filter(function ($instance) use ($request) {
                            if ($request->has('message_type_dropdown')) {
                                $instance->collection = $instance->collection->filter(function ($row) use ($request) {
                                    return str_contains(strtolower($row['message_type']), strtolower($request->get('message_type_dropdown'))) ? true : false;
                                });
                            }
                            if ($request->has('message_text')) {
                                $instance->collection = $instance->collection->filter(function ($row) use ($request) {
                                    return str_contains(strtolower($row['message_text']), strtolower($request->get('message_text'))) ? true : false;
                                });
                            }
                        })
                        ->make(true);
    }

    /**
     * Display a form to create new recommendation.
     *
     * @return view as response
     */
    public function create() {
        return view('admin::recommendation.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  Modules\Admin\Http\Requests\RecommendationCreateRequest $request
     * @return json encoded Response
     */
    public function store(RecommendationCreateRequest $request) {
        $response = $this->repository->create($request->all());

        return response()->json($response);
    }

    /**
     * Save and send notification - triggered from an open (unauthenticated route)
     * @param array containing input params
     * @return json encoded Response
     */
    public function sendNotification(Request $request) {
        $params = $request->toArray();
        if (empty($params['token']) || $params['token'] != 'BoRIqf9PoJQFa7q7lI92bmrUXpUhHmgy') {
            $response['status'] = 'error';
            $response['message'] = 'Invalid request';
        } else {
            $response = $this->repository->create($params);
        }

        return response()->json($response);
    }

    /**
     * Show the form for editing the specified recommendation.
     *
     * @param  Modules\Admin\Models\Recommendation $recommendation
     * @return json encoded Response
     */
    public function edit(Recommendation $recommendation) {
        $response['success'] = true;
        $messageType = array('1' => 'General Notification', '2' => 'Activity Recommendation', '3' => 'Diet Recommendation');
        $response['form'] = view('admin::recommendation.edit', compact('recommendation', 'messageType'))->render();

        return response()->json($response);
    }

    /**
     * Store an updated resource in storage.
     *
     * @param  Modules\Admin\Http\Requests\RecommendationCreateRequest $request, Modules\Admin\Models\Recommendation $recommendation
     * @return json encoded Response
     */
    public function update(RecommendationUpdateRequest $request, Recommendation $recommendation) {
        $response = $this->repository->update($request->all(), $recommendation);

        return response()->json($response);
    }

    /**
     * Delete resource from storage.
     *
     * @param  Modules\Admin\Http\Requests\RecommendationDeleteRequest $request
     * @return json encoded Response
     */
    public function destroy(RecommendationDeleteRequest $request, Recommendation $recommendation) {
        $response = [];
        $result = $this->repository->deleteAction($request->all());
        if ($result) {
            $response = ['status' => 'success', 'message' => trans('admin::messages.deleted', ['name' => trans('admin::controller/recommendation.recommendation')])];
        } else {
            $response = ['status' => 'fail', 'message' => trans('admin::messages.not-deleted', ['name' => trans('admin::controller/recommendation.recommendation')])];
        }

        return response()->json($response);
    }

}
