<?php
/**
 * The class for managing member specific actions.
 *
 *
 * @author Tushar Dahiwale <tushard@iprogrammer.com>
 * @package Admin
 * @since 1.0
 */

namespace Modules\Admin\Http\Controllers;

use Auth;
use Datatables;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Modules\Admin\Repositories\MembersRepository;
use Modules\Admin\Repositories\CenterRepository;
use Modules\Admin\Services\Helper\MemberHelper;
use Modules\Admin\Services\Helper\ImportHelper;
use Session;
use Modules\Admin\Services\Helper\UserInfoHelper;

class MembersController extends Controller
{

    /**
     * The MemberRepository instance.
     *
     * @var Modules\Admin\Repositories\MembersRepository
     */
    protected $repository;

    /**
     * Create a new MembersController instance.
     *
     * @param  Modules\Admin\Repositories\MembersRepository $repository
     * @return void
     */
    public function __construct(MembersRepository $repository, MemberHelper $memberHelper, ImportHelper $importHelper, CenterRepository $centerRepository)
    {
        parent::__construct();
        $this->middleware('acl');
        $this->repository = $repository;
        $this->centerRepository = $centerRepository;
        $this->memberHelper = $memberHelper;
        $this->importHelper = $importHelper;
    }

    /**
     * List all the data
     * @param Modules\Admin\Repositories\MemberRepository $countryRepository
     * @return response
     */
    public function index()
    {
        ini_set('memory_limit','512M');
        $memberHelper = new MemberHelper();
        if (Auth::guard('admin')->user()->userType->id == 9 || Auth::guard('admin')->user()->userType->id == 5) {
            $centersList = $memberHelper->getCentersList();
        }
        return view('admin::members.index', compact('centersList'));
    }

    /**
     * Display a listing of the resource.
     *
     * @return datatable
     */
    public function getData(Request $request)
    {
        $params['centerId'] = 0;
        if ($request->input('centerId') != '' || $request->input('centerId') != 0) {
            $params['centerId'] = $request->input('centerId');
        }
        $params['username'] = Auth::guard('admin')->user()->username;
        $params['user_type_id'] = Auth::guard('admin')->user()->user_type_id;
        $params['user_id'] = Auth::guard('admin')->user()->id;

        $memberList = $this->repository->data($request->all(), $params); //->toArray();
        $member = $memberList->toArray();
        Session::set('memberListGlobal', $memberList);
//filter to display own records
        if (Auth::guard('admin')->user()->hasOwnView && (empty(Auth::guard('admin')->user()->hasEdit) || empty(Auth::guard('admin')->user()->hasDelete))) {
            $countries = $countries->filter(function ($row) {
                return (Str::equals($row['created_by'], Auth::guard('admin')->user()->id)) ? true : false;
            });
        }
        return Datatables::of($memberList)
                ->addColumn('full_name', function ($member) {
                    $fullName = $member['first_name'];
                    return ucwords($fullName);
                })
                /* ->addColumn('package_name', function ($member) {
                  $package_name = isset($member['packagedata'][0]['package_name']) ? $member['packagedata'][0]['package_name'] : '';
                  return $package_name;
                  }) */
                ->addColumn('age', function ($member) {
                    $age = "";
                    if (!empty($member->date_of_birth) && $member->date_of_birth != '0000-00-00') {
                        $age = $this->calculateAge($member->date_of_birth);
                    }
                    return $age;
                })
                ->addColumn('gender', function ($member) {
                    $gender = ($member->gender == 1) ? 'Male' : 'Female';
                    return $gender;
                })
                ->addColumn('package_name', function ($member) {
                    $package_details = $this->repository->getMemberLatestPackage($member->id);
                    $package_name = (isset($package_details->toArray()[0])) ? $package_details->toArray()[0]["crm_package_id"] : '';
                    if (empty($package_name)) {
                        return 'NA';
                    } else {
                        $package_start_from = (isset($package_details->toArray()[0]) && ($package_details->toArray()[0]['start_date'] != '0000-00-00')) ? date('d-M-Y', strtotime($package_details->toArray()[0]["start_date"])) : 'NA';
                        $package_upto = (isset($package_details->toArray()[0]) && ($package_details->toArray()[0]['end_date'] != '0000-00-00')) ? date('d-M-Y', strtotime($package_details->toArray()[0]["end_date"])) : 'NA';
                        $package_details = "<br> <b>From : </b>" . $package_start_from . " <br> <b>Upto : </b>" . $package_upto;
                        return $package_name . " " . $package_details;
                    }
                })
                ->addColumn('action', function ($member) {
                    $actionList = '';
                    
                    $actionList = '<a href="javascript:;" data-action="view" data-id="' . $member['id'] . '"  id="' . $member['id'] . '" class="btn btn-xs default margin-bottom-5 blue view-link" title="Edit">VIEW</a>';
                    if(Auth::guard('admin')->user()->user_type_id == 7 || Auth::guard('admin')->user()->user_type_id == 8){
                    $actionList .= '<a href="javascript:;" data-action="edit" data-id="' . $member['id'] . '" id="' . $member['id'] . '" class="btn btn-xs default margin-bottom-5 blue edit-link" title="Assign Dietician">Assign Dietician</a>';
                    }
                    return $actionList;
                })
                
                ->make(true);
    }

    /**
     * Display a form to create new recommendation.
     *
     * @return view as response
     */
    public function display(Request $request, $memberId)
    {
        Session::set('member_id', $memberId);
        $memberId = Session::get('member_id');
        $params['id'] = $memberId;
        $memberDetails = $this->memberHelper->getMemberDetailsById($memberId);
        $memberDetails["age"] = "";
        if (!empty($memberDetails["date_of_birth"]) && $memberDetails["date_of_birth"] != '0000-00-00') {
            $memberDetails["age"] = $this->calculateAge($memberDetails["date_of_birth"]);
        }
        $memberDetails["gender"] = ($memberDetails["gender"] == 1) ? 'Male' : 'Female';
        $memberData = $this->repository->getMemberData($request, $params)->toArray();
        $params['memberDietPlan'] = $memberData['diet_plan_id'];
        $param['mobile_number'] = $memberDetails['mobile_number'];
        $memberBcaData = $this->repository->getMemberBcaData($memberId);
        //$bcaData = $memberBcaData['response']['bca_data'];
        $bcaData = (!empty($memberBcaData)) ? $memberBcaData->toArray() : [];
        $bcaData["fat_mass"] = isset($bcaData["fat_weight"]) ? $bcaData["fat_weight"] : '';
        $bcaData["protein"] = isset($bcaData["protein"]) ? $bcaData["protein"] : '';
        $bcaData["mineral"] = isset($bcaData["mineral"]) ? $bcaData["mineral"] : '';
        $bcaData["water"] = isset($bcaData["water_weight"]) ? $bcaData["water_weight"] : '';
        $bcaData["lean_body_mass"] = isset($bcaData["lean_body_mass_weight"]) ? $bcaData["lean_body_mass_weight"] : '';
        $bcaData["percent_body_fat"] = isset($bcaData["fat_percent"]) ? $bcaData["fat_percent"] : '';
        $bcaData["current_weight"] = $this->repository->getMemberLatestWeight($memberId);
        $recommendedCalories = $this->repository->getRecommendedCalories($params);
        $latestActivity = $this->repository->getLatestActivity($params);
        //return redirect('admin/recommendation');
        return view('admin::members.view', compact('memberData', 'memberDetails', 'bcaData', 'recommendedCalories', 'latestActivity', 'memberId'));
    }

    // Function to Calculate Age
    public function calculateAge($birthDate)
    {
        $birthDate = date('d/m/Y', strtotime($birthDate));
        //explode the date to get month, day and year
        $birthDate = explode("/", $birthDate);
        //get age from date or birthdate
        $age = (date("md", date("U", mktime(0, 0, 0, $birthDate[0], $birthDate[1], $birthDate[2]))) > date("md") ? ((date("Y") - $birthDate[2]) - 1) : (date("Y") - $birthDate[2]));
        return $age;
    }

    public function memberPackages(Request $request)
    {
        $packageList = $this->repository->getMemberpackagesWithServices($request->all()["memberId"]); //->toArray();        
        return Datatables::of($packageList)
                ->addColumn('start_date', function ($package) {
                    $start_date = date('d-M-Y', strtotime($package['start_date']));
                    return $start_date;
                })
                ->addColumn('end_date', function ($package) {
                    $end_date = date('d-M-Y', strtotime($package['end_date']));
                    return $end_date;
                })
                ->make(true);
    }
    

    // Function to get Members list of selected center
    public function getCenterWiseMembersList(Request $request) {
       // $center_id = filter_var($request->all()["center_id"], FILTER_SANITIZE_NUMBER_INT);
        $userInfoHelper = new UserInfoHelper();
        $memberHelper = new MemberHelper();
        $countCenters = $memberHelper->getCentersList();           
        if(isset($countCenters) && count($countCenters) > 1) {
            $center_id = filter_var($request->all()["center_id"], FILTER_SANITIZE_NUMBER_INT);
        } else {
            $user_center = $userInfoHelper->getLoggedInUserCenter(Auth::guard('admin')->user()->id);
            $center_id = $user_center[0]['center_id'];
        }
        
        Session::set('center_id', $center_id);
        
        if((isset($request->all()["customer_gender"]) && !empty($request->all()["customer_gender"]))  || (isset($request->all()["customer_service_cat"]) && !empty($request->all()["customer_service_cat"])) ){
            $memGender = filter_var($request->all()["customer_gender"], FILTER_SANITIZE_NUMBER_INT);
            $serviceCategory = filter_var($request->all()["customer_service_cat"], FILTER_SANITIZE_NUMBER_INT);
            //$membersList = $this->centerRepository->getMembersListWithGender($center_id,$memGender);
             $membersList = $this->centerRepository->getMembersListWithGender(Session::get('center_id'),$memGender,$serviceCategory);
        }else{
            //$membersList = $this->centerRepository->getMembersList($center_id);
            $membersList = $this->centerRepository->getMembersList(Session::get('center_id'));
        }
        
        $response['members_list'] = View('admin::partials.customer-dropdown', compact('membersList'))->render();
        return response()->json($response);
    }
    
    //Function to display member details on pop up on Assign Dietician link click(Slimming head & center head only)
    public function displayMemberDetails(Request $request, $memberId){
        Session::set('member_id', $memberId);
        $memberId = Session::get('member_id');
        $params['id'] = $memberId;
        $memberData = $this->repository->dataMemberDetails($request, $params)->toArray();
      
        if(!empty($memberData)){
            foreach($memberData as $key=>$dieticianName){               
                $dieticianDropdown[$dieticianName->id]=$dieticianName->AdminFullName . "(" . $dieticianName->username . ")";
            }            
        }
        
        $existingDietician = $this->repository->getExistingDietician($memberId)->toArray();
        
        if(isset($existingDietician) && !empty($existingDietician)){
            $dieticianUserName = $existingDietician[0]->id;
        }else{
            $dieticianUserName = " ";
        }
        
        $response['success'] = true;
        $response['form'] = view('admin::members.view-member-details', compact('memberData','dieticianDropdown','dieticianUserName'))->render();

        return response()->json($response);
    }
    
    //Edit member dietician on select from dropdown
    public function editMember(Request $request, $memberId){
       
        $requestParams = $request->all();
        $memberId=$requestParams['member_id'];
        if(isset($requestParams['dietician_id']) && !empty($requestParams['dietician_id'])){
        $DieticianId=$requestParams['dietician_id'];
        }
        //dd($DieticianId);
        $response = $this->repository->editMemberDetails($request, $requestParams);
        return response()->json($response);
        
    }

}
