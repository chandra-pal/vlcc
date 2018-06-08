<?php

/**
 * The class for managing product recommendation specific actions.
 *
 *
 * @author Gauri Deshmukh <gaurid@iprogrammer.com>
 * @package Admin
 * @since 1.0
 */

namespace Modules\Admin\Http\Controllers;

use Auth;
use Datatables;
use Session;
use Illuminate\Support\Str;
use Modules\Admin\Models\ProductRecommendation;
use Modules\Admin\Repositories\ProductRecommendationRepository;
use Modules\Admin\Repositories\CenterRepository;
use Modules\Admin\Http\Requests\ProductRecommendationCreateRequest;
use Modules\Admin\Http\Requests\ProductRecommendationUpdateRequest;
use Modules\Admin\Services\Helper\MemberHelper;
use Illuminate\Http\Request;

class ProductRecommendationController extends Controller {

    /**
     * The ProductRecommendationRepository instance.
     *
     * @var Modules\Admin\Repositories\ProductRecommendationRepository
     */
    protected $repository;

    /**
     * The CenterRepository instance.
     *
     * @var Modules\Admin\Repositories\CenterRepository
     */
    protected $centerRepository;

    /**
     * Create a new ProductRecommendationController instance.
     *
     * @param  Modules\Admin\Repositories\ProductRecommendationRepository $repository
     * @return void
     */
    public function __construct(ProductRecommendationRepository $repository, CenterRepository $centerRepository) {
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
        $memberHelper = new MemberHelper();
        $user_type_id = Auth::guard('admin')->user()->user_type_id;
        $productList = $this->repository->getProductList()->toArray();
        $selectedProduct = '';
        $membersList = [];
        if (Auth::guard('admin')->user()->userType->id == 9) {
            $centersList = $memberHelper->getCentersList();
            if (Session::get('center_id') != '') {
                $membersList = $this->centerRepository->getMembersList(Session::get('center_id'));
            }
        } elseif (Auth::guard('admin')->user()->userType->id == 1) {
            unset($membersList);
        } elseif (Auth::guard('admin')->user()->userType->id == 7) {
            $centers = $memberHelper->getCentersList();
            $centerId = key($centers);
            if (isset($centerId) && $centerId != '') {
                $membersList = $this->centerRepository->getMembersList($centerId);
            }
        } else {
            $membersList = $memberHelper->getUserWiseMemberList();
        }
        return view('admin::product-recommendation.index', compact('productList', 'selectedProduct', 'user_type_id', 'centersList', 'membersList'));

//        if ($user_type_id == '4') {
//            $memberHelper = new MemberHelper();
//            $membersList = $memberHelper->getUserWiseMemberList();
//            return view('admin::product-recommendation.index', compact('productList', 'selectedProduct', 'user_type_id', 'membersList'));
//        } else {
//            return view('admin::product-recommendation.index', compact('productList', 'selectedProduct', 'user_type_id'));
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
        $productRecommendations = $this->repository->data($params);
        //filter to display own records
        if (Auth::guard('admin')->user()->hasOwnView && (empty(Auth::guard('admin')->user()->hasEdit) || empty(Auth::guard('admin')->user()->hasDelete))) {
            $productRecommendations = $productRecommendations->filter(function ($row) {
                return ($row['created_by'] == Auth::guard('admin')->user()->id) ? true : false;
            });
        }
        return Datatables::of($productRecommendations)
                        ->addColumn('action', function ($productRecommendations) {
                            $actionList = '';
                            if (!empty(Auth::guard('admin')->user()->hasEdit) || (!empty(Auth::guard('admin')->user()->hasOwnEdit) && ($productRecommendations->created_by == Auth::guard('admin')->user()->id))) {
                                $actionList .= '<a href="javascript:;" data-action="edit" title="Edit" data-id="' . $productRecommendations->id . '" class="btn btn-xs default margin-bottom-5 yellow-gold edit-form-link" title="Edit" id="' . $productRecommendations->id . '"><i class="fa fa-pencil"></i></a>';
                            }
                            return $actionList;
                        })
                        ->addColumn('status', function ($productRecommendations) {

                            $status = ($productRecommendations->status == 1) ? '<span class="label label-sm label-success">' . trans('admin::messages.active') . '</span>' : '<span class="label label-sm label-danger">' . trans('admin::messages.inactive') . '</span>';
                            return $status;
                        })
                        ->make(true);
    }

    /**
     * Display a form to create new product recommendation
     *
     * @return view as response
     */
    public function create() {
        return view('admin::product-recommendation.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  Modules\Admin\Http\Requests\ProductRecommendationCreateRequest $request
     * @return json encoded Response
     */
    public function store(ProductRecommendationCreateRequest $request) {
        $response = $this->repository->create($request->all());
        return response()->json($response);
    }

    /**
     * Show the form for editing the specified diet plan.
     *
     * @param  Modules\Admin\Models\DietPlan $dietPlan
     * @return json encoded Response
     */
    public function edit(ProductRecommendation $productRecommendation) {
        $data = $productRecommendation->toArray();
        $response['success'] = true;
        $productList = $this->repository->getProductList()->toArray();
        $selectedProduct = $data['product_id'];
        $response['form'] = view('admin::product-recommendation.edit', compact('productRecommendation', 'selectedProduct', 'productList'))->render();
        return response()->json($response);
    }

    /**
     * Store an updated resource in storage.
     *
     * @param  Modules\Admin\Http\Requests\ProductRecommendationUpdateRequest $request, Modules\Admin\Models\ProductRecommendation $productRecommendation
     * @return json encoded Response
     */
    public function update(ProductRecommendationUpdateRequest $request, ProductRecommendation $productRecommendation) {
        $response = $this->repository->update($request->all(), $productRecommendation);

        return response()->json($response);
    }

}
