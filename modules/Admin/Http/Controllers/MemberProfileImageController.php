<?php

/**
 * The class for managing member profile image specific actions.
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
use Modules\Admin\Models\MemberProfileImage;
use Modules\Admin\Repositories\MemberProfileImageRepository;
use Modules\Admin\Repositories\CenterRepository;
use Modules\Admin\Repositories\MembersRepository;
use Modules\Admin\Http\Requests\MemberProfileImageCreateRequest;
use Illuminate\Http\Request;
use Modules\Admin\Services\Helper\MemberHelper;
use Session;
use Modules\Admin\Services\Helper\ImageHelper;
use Approached\LaravelImageOptimizer\ImageOptimizer;

class MemberProfileImageController extends Controller {

    /**
     * The MemberProfileImageRepository instance.
     *
     * @var Modules\Admin\Repositories\MemberProfileImageRepository
     */
    protected $repository;

    /**
     * The CenterRepository instance.
     *
     * @var Modules\Admin\Repositories\CenterRepository
     */
    protected $centerRepository;

    /**
     * Create a new MemberProfileImageController instance.
     *
     * @param  Modules\Admin\Repositories\MemberProfileImageRepository $repository
     * @return void
     */
    public function __construct(MemberProfileImageRepository $repository, MembersRepository $memberRepository, CenterRepository $centerRepository) {
        parent::__construct();
        $this->middleware('acl');
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
        $memberID = Session::get('member_id');
        if ('' != $memberID) {
            $params['member_id'] = $memberID;
            $memberPackages = $this->memberRepository->getMemberpackages($params);

            if (!empty($memberPackages->toArray())) {
                $memberImages = $this->repository->data($params)->toArray();
                if (!empty($memberImages)) {
                    $id = $memberImages[0]['id'];
                    $before_image = $memberImages[0]['before_image'];
                    $after_image = $memberImages[0]['after_image'];
                    $style = '';
                } else {
                    $id = '';
                    $before_image = '';
                    $after_image = '';
                    $style = '';
                }
            } else {
                $id = '';
                $before_image = '';
                $after_image = '';
                $memberPackages = '';
                $style = 'style="display: none;"';
            }
        } else {
            $memberPackages = "";
            $id = "";
            $before_image = "";
            $after_image = "";
            $style = '';
        }
        return view('admin::member-profile-image.index', compact('membersList', 'memberPackages', 'id', 'before_image', 'after_image', 'style', 'centersList'));
    }

    /**
     * Get a listing of the resource.
     *
     * @return datatable
     */
    public function getData(Request $request, $mid) {
        $params['member_id'] = $mid;
        $params['id'] = $mid;
        Session::set('member_id', $mid);
        $memberProfileImage = $this->repository->data($params);

        //filter to display own records
        if (Auth::guard('admin')->user()->hasOwnView && (empty(Auth::guard('admin')->user()->hasEdit) || empty(Auth::guard('admin')->user()->hasDelete))) {
            $memberProfileImage = $memberProfileImage->filter(function ($row) {
                return (Str::equals($row['created_by'], Auth::guard('admin')->user()->id)) ? true : false;
            });
        }

        return Datatables::of($memberProfileImage)
                        ->filter(function ($instance) use ($request) {
                            if ($request->has('package_name')) {
                                $instance->collection = $instance->collection->filter(function ($row) use ($request) {
                                    return str_contains(strtolower($row['package_name']), strtolower($request->get('package_name'))) ? true : false;
                                });
                            }

                            if ($request->has('login_in_time_from') && $request->has('login_in_time_to')) {
                                $instance->collection = $instance->collection->filter(function ($row) use ($request) {
                                    $fromDateArray = $request->get('login_in_time_from');
                                    $toDateArray = $request->get('login_in_time_to');
                                    $fromDate = date('Y-m-d', strtotime($fromDateArray));
                                    $toDate = date('Y-m-d', strtotime($toDateArray));
                                    return $row['package_validity'] >= $fromDate && $row['package_validity'] <= $toDate ? true : false;
                                });
                            }
                        })
                        ->addColumn('before_image', function ($memberProfileImage) {
                            return '<div class="user-listing-img">' . ImageHelper::getUserBeforeImage($memberProfileImage->id, $memberProfileImage->before_image) . '</div>';
                        })
                        ->addColumn('after_image', function ($memberProfileImage) {
                            return '<div class="user-listing-img">' . ImageHelper::getUserAfterImage($memberProfileImage->id, $memberProfileImage->after_image) . '</div>';
                        })
                        ->make(true);
    }

    public function getPackages($customerId) {
        $params['member_id'] = $customerId;
        Session::set('member_id', $customerId);
        $memberPackages = $this->memberRepository->getMemberpackages($params);
        if (!empty($memberPackages)) {
            $response['memberPackages'] = $memberPackages;
            $memberImages = $this->repository->data($params)->toArray();
            if (!empty($memberPackages->toArray())) {
                if (!empty($memberImages)) {
                    $response['id'] = $memberImages[0]['id'];
                    $response['before_image'] = $memberImages[0]['before_image'];
                    $response['after_image'] = $memberImages[0]['after_image'];
                } else {
                    $response['id'] = '';
                    $response['before_image'] = '';
                    $response['after_image'] = '';
                }
            } else {
                $response['id'] = '';
                $response['before_image'] = '';
                $response['after_image'] = '';
            }
        } else {
            $response['id'] = '';
            $response['before_image'] = '';
            $response['after_image'] = '';
            $response['memberPackages'] = $memberPackages->toArray();
        }
        return response()->json($response);
    }

    /* function compareDeepValue($val1, $val2) {

      } */

    /**
     * Display a form to create new member profile image category.
     *
     * @return view as response
     */
    public function create() {

        return view('admin::member-profile-image.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  Modules\Admin\Http\Requests\MemberProfileImageCreateRequest $request
     * @return json encoded Response
     */
    public function store(MemberProfileImageCreateRequest $request, ImageOptimizer $imageOptimizer) {
        $response = $this->repository->create($request->all(), $imageOptimizer);
        return response()->json($response);
    }

}
