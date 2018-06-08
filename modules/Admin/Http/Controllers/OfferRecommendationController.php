<?php

/**
 * The class for managing offer recommendation specific actions.
 *
 *
 * @author Sopan Zinjurde <sopanz@iprogrammer.com>
 * @package Admin
 * @since 1.0
 */

namespace Modules\Admin\Http\Controllers;

use Auth;
use Datatables;
use Session;
use Modules\Admin\Models\OfferRecommendation;
use Modules\Admin\Repositories\OfferRecommendationRepository;
use Modules\Admin\Repositories\CenterRepository;
use Modules\Admin\Http\Requests\OfferRecommendationCreateRequest;
use Modules\Admin\Http\Requests\OfferRecommendationUpdateRequest;
use Modules\Admin\Services\Helper\MemberHelper;
use Illuminate\Http\Request;

class OfferRecommendationController extends Controller {

    /**
     * The OfferRecommendationRepository instance.
     *
     * @var Modules\Admin\Repositories\OfferRecommendationRepository
     */
    protected $repository;

    /**
     * The CenterRepository instance.
     *
     * @var Modules\Admin\Repositories\CenterRepository
     */
    protected $centerRepository;

    /**
     * Create a new OfferRecommendationController instance.
     *
     * @param  Modules\Admin\Repositories\OfferRecommendationRepository $repository
     * @return void
     */
    public function __construct(OfferRecommendationRepository $repository, CenterRepository $centerRepository) {
        parent::__construct();
        $this->middleware('acl');
        $this->repository = $repository;
        $this->centerRepository = $centerRepository;
    }

    /**
     * Display a listing of the resource.
     *
     * @return view as response
     */
    public function index() {
        $acl_flag = !empty(Auth::guard('admin')->user()->hasAdd) ? 1 : 2;
        $memberHelper = new MemberHelper();
        $user_type_id = Auth::guard('admin')->user()->user_type_id;
        $offerList = $this->repository->getOfferList()->toArray();
        $selectedOffer = '';
        $membersList = [];
        if (Auth::guard('admin')->user()->userType->id == 9) {
            $centersList = $memberHelper->getCentersList();
            if (Session::get('center_id') != '') {
                $membersList = $this->centerRepository->getMembersList(Session::get('center_id'));
            }
        } elseif (Auth::guard('admin')->user()->userType->id == 1) {
            unset($membersList);
        } elseif (Auth::guard('admin')->user()->userType->id == 7 || Auth::guard('admin')->user()->userType->id == 8) {
            $centers = $memberHelper->getCentersList();
            $centerId = key($centers);
            if (isset($centerId) && $centerId != '') {
                $membersList = $this->centerRepository->getMembersList($centerId);
            }
        } else {
            $membersList = $memberHelper->getUserWiseMemberList();
        }
        return view('admin::offer-recommendation.index', compact('offerList', 'selectedOffer', 'user_type_id', 'membersList', 'centersList', 'acl_flag'));
//        if ($user_type_id == '4') {
//            $memberHelper = new MemberHelper();
//            $membersList = $memberHelper->getUserWiseMemberList();
//            return view('admin::offer-recommendation.index', compact('offerList', 'selectedOffer', 'user_type_id', 'membersList'));
//        } else {
//            return view('admin::offer-recommendation.index', compact('offerList', 'selectedOffer', 'user_type_id'));
//        }
    }

    /**
     * Get a listing of the resource.
     *
     * @return datatable
     */
    public function getData($mid, Request $request) {
        $params['member_id'] = $mid;
        Session::set('member_id', $mid);
        $offerRecommendations = $this->repository->data($params);
        //filter to display own records
        if (Auth::guard('admin')->user()->hasOwnView && (empty(Auth::guard('admin')->user()->hasEdit) || empty(Auth::guard('admin')->user()->hasDelete))) {
            $offerRecommendations = $offerRecommendations->filter(function ($row) {
                return ($row['created_by'] == Auth::guard('admin')->user()->id) ? true : false;
            });
        }
        return Datatables::of($offerRecommendations)
                        ->addColumn('action', function ($offerRecommendations) {
                            $actionList = '';
                            if (!empty(Auth::guard('admin')->user()->hasEdit) || (!empty(Auth::guard('admin')->user()->hasOwnEdit) && ($offerRecommendations->created_by == Auth::guard('admin')->user()->id))) {
                                $actionList .= '<a href="javascript:;" data-action="edit" title="Edit" data-id="' . $offerRecommendations->id . '" class="btn btn-xs default margin-bottom-5 yellow-gold edit-form-link" title="Edit" id="' . $offerRecommendations->id . '"><i class="fa fa-pencil"></i></a>';
                            }
                            return $actionList;
                        })
                        ->addColumn('status', function ($offerRecommendations) {

                            $status = ($offerRecommendations->status == 1) ? '<span class="label label-sm label-success">' . trans('admin::messages.active') . '</span>' : '<span class="label label-sm label-danger">' . trans('admin::messages.inactive') . '</span>';
                            return $status;
                        })
                        ->make(true);
    }

    /**
     * Display a form to create new offer recommendation
     *
     * @return view as response
     */
    public function create() {
        return view('admin::offer-recommendation.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  Modules\Admin\Http\Requests\OfferRecommendationCreateRequest $request
     * @return json encoded Response
     */
    public function store(OfferRecommendationCreateRequest $request) {
        $response = $this->repository->create($request->all());
        return response()->json($response);
    }

    /**
     * Show the form for editing the specified offer recommandation.
     *
     * @param  Modules\Admin\Models\OfferRecommendation $offerRecommendation
     * @return json encoded Response
     */
    public function edit(OfferRecommendation $offerRecommendation) {
        $data = $offerRecommendation->toArray();
        $response['success'] = true;
        $offerList = $this->repository->getOfferList()->toArray();
        $selectedOffer = $data['offer_id'];
        $response['form'] = view('admin::offer-recommendation.edit', compact('offerRecommendation', 'selectedOffer', 'offerList'))->render();
        return response()->json($response);
    }

    /**
     * Store an updated resource in storage.
     *
     * @param  Modules\Admin\Http\Requests\OfferRecommendationUpdateRequest $request, Modules\Admin\Models\OfferRecommendation $offer
     * @return json encoded Response
     */
    public function update(OfferRecommendationUpdateRequest $request, OfferRecommendation $offer) {
        $response = $this->repository->update($request->all(), $offer);
        return response()->json($response);
    }

}
