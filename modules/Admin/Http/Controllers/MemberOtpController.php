<?php

/**
 * The class for managing member otp specific actions.
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
use Modules\Admin\Models\MemberOtp;
use Modules\Admin\Repositories\MemberOtpRepository;
use Modules\Admin\Repositories\MembersRepository;
use Modules\Admin\Repositories\CenterRepository;
use Modules\Admin\Services\Helper\MemberHelper;
use Session;

class MemberOtpController extends Controller {

    /**
     * The MemberOtpRepository instance.
     *
     * @var Modules\Admin\Repositories\MemberOtpRepository
     */
    protected $repository;

    /**
     * The CenterRepository instance.
     *
     * @var Modules\Admin\Repositories\CenterRepository
     */
    protected $centerRepository;

    /**
     * Create a new MemberOtpController instance.
     *
     * @param  Modules\Admin\Repositories\MemberOtpRepository $repository
     * @return void
     */
    public function __construct(MemberOtpRepository $repository, MembersRepository $memberRepository, CenterRepository $centerRepository) {
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
        
        return view('admin::member-otp.index', compact('membersList', 'centersList'));
    }

    /**
     * Get a listing of the resource.
     *
     * @return datatable
     */
    public function getData($mid) {
        Session::set('member_id', $mid);
        $params['username'] = Auth::guard('admin')->user()->username;
        $params['member_id'] = $mid;
        $memberContact = $this->repository->getMemberContact($params)->toArray();
        $params['contact'] = $memberContact[0];
        $memberOtp = $this->repository->data($params);
        $deliveryType = array("0" => "No", "1" => "Yes");
        $otpUsed = array("0" => "No", "1" => "Yes");


        //filter to display own records
        if (Auth::guard('admin')->user()->hasOwnView && (empty(Auth::guard('admin')->user()->hasEdit) || empty(Auth::guard('admin')->user()->hasDelete))) {
            $memberOtp = $memberOtp->filter(function ($row) {
                return (Str::equals($row['created_by'], Auth::guard('admin')->user()->id)) ? true : false;
            });
        }

        return Datatables::of($memberOtp)
                        ->addColumn('sms_delivered', function ($memberOtp) use($deliveryType) {
                            $plan_types = $deliveryType[$memberOtp->sms_delivered];
                            return $plan_types;
                        })
                        ->addColumn('otp_used', function ($memberOtp) use($otpUsed) {
                            $plan_types = $otpUsed[$memberOtp->otp_used];
                            return $plan_types;
                        })
                        ->make(true);
    }

}
