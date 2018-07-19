<?php
/**
 * The class for managing food specific actions.
 *
 *
 * @author Sachin Gend <saching@iprogrammer.com>
 * @package Admin
 * @since 1.0
 */

namespace Modules\Admin\Http\Controllers;

use Auth;
use Datatables;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Modules\Admin\Models\CPR;
use Modules\Admin\Repositories\CPRRepository;
use Modules\Admin\Repositories\EscalationMatrixRepository;
use Modules\Admin\Repositories\CenterRepository;
use Modules\Admin\Http\Requests\CPRCreateRequest;
use Modules\Admin\Http\Requests\BcaCreateRequest;
use Modules\Admin\Http\Requests\MeasurementRecordCreateRequest;
use Modules\Admin\Http\Requests\SessionRecordCreateRequest;
use Modules\Admin\Services\Helper\ConfigConstantHelper;
use Modules\Admin\Http\Requests\DietaryAssessmentCreateRequest;
use Modules\Admin\Http\Requests\FitnessAssessmentCreateRequest;
use Validator;
use Modules\Admin\Services\Helper\ImageHelper;
use File;
use Excel;
use \Carbon\Carbon;
use Illuminate\Support\Collection;
use Approached\LaravelImageOptimizer\ImageOptimizer;
use Modules\Admin\Http\Requests\MedicalAssessmentCreateRequest;
use Modules\Admin\Http\Requests\SkinHairAnalysisCreateRequest;
use Modules\Admin\Http\Requests\ReviewFitnessActivityCreateRequest;
use Session;
use Modules\Admin\Services\Helper\MemberHelper;
use Modules\Admin\Http\Requests\MemberMeasurementRecordCreateRequest;
use Modules\Admin\Http\Requests\MemberMedicalreviewCreateRequest;
use Modules\Admin\Repositories\SessionBookingsRepository;

class CPRController extends Controller
{

    /**
     * The CPRRepository instance.
     *
     * @var Modules\Admin\Repositories\CPRRepository
     */
    protected $repository;

    /**
     * Create a new CPRController instance.
     *
     * @param  Modules\Admin\Repositories\CPRRepository $repository
     * @return void
     */
    public function __construct(CPRRepository $repository, EscalationMatrixRepository $escalation_matrix, CenterRepository $centerRepository, SessionBookingsRepository $sessionBookings)
    {
        parent::__construct();
        $this->middleware('acl');
        $this->repository = $repository;
        $this->escalation_matrix = $escalation_matrix;
        $this->centerRepository = $centerRepository;
        $this->sessionBooking = $sessionBookings;
    }

    /**
     * Display a listing of the resource.
     *
     * @return view as response
     */
    public function index($session_id = 0)
    {
        $acl_flag = !empty(Auth::guard('admin')->user()->hasAdd) ? 1 : 2;
        $logged_in_by_user_type = Auth::guard('admin')->user()->userType->id;
        $logged_in_user_id = Auth::guard('admin')->user()->id;
        $params['dietician_id'] = filter_var($logged_in_user_id, FILTER_SANITIZE_NUMBER_INT);
        $disableSubmit = 1;
        $sessionData = [];
        $memberHelper = new MemberHelper();
        $membersList = [];
        $memberPackages = [];
        $selectedPackage = '';
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
        
        if ($session_id == 0) {
            // Check if Member id is present in session
            $memberID = Session::get('member_id');
            if (!empty($memberID)) {
                $latest_session_id = 0;
                $center_id = (Session::get('center_id') != "") ? Session::get('center_id') : 0;
                $latest_session_id = $this->repository->getLatestSessionId($memberID, $center_id);
                if ($latest_session_id == 0 || empty($latest_session_id)) {
                    // Session id not available. Display only personal info & BCA data & disable all submit buttons
                    $disableSubmit = 0;
                } else {
                    $params["session_id"] = $latest_session_id;
                    $cprData = $this->repository->validateSession($params);
                    $session_id = $latest_session_id;
                    $sessionData = $cprData->toArray();
                    $selectedPackage = $sessionData[0]['package_id'];
                }
                // Fetch Member packages of selected member
                $params["member_id"] = $memberID;
                $memberPackages = $this->sessionBooking->getMemberPackages($params)->toArray();
            } else {
                // Member id not present in session
                // Retrieve Customer list of logged in dietician & display customer list in Dropdown & Disable CPR form
                $memberID = 0;
                return view('admin::cpr.customer-dropdown', compact('membersList', 'centersList'));
            }
        } else {
            $params['session_id'] = $session_id;
            $cprData = $this->repository->validateSession($params);
            $sessionData = $cprData->toArray();
            Session::set('member_id', $sessionData[0]['member_id']);
            if (Auth::guard('admin')->user()->userType->id == 9) {
                // Get Center of Member
                $centerId = $this->repository->getMemberCenter($sessionData[0]['member_id']);
                Session::set('center_id', $centerId);
                $membersList = $this->centerRepository->getMembersList(Session::get('center_id'));
            }
            // Fetch Member packages of selected member
            $params["member_id"] = $sessionData[0]['member_id'];
            $memberPackages = $this->sessionBooking->getMemberPackages($params)->toArray();
            $memberPackageGuids = $this->sessionBooking->getPackageGuids($memberPackages)->toArray();
            // Get packages of member whose status is inactive
            $inaciveMemberPackages = $this->sessionBooking->getInactiveMemberPackages($sessionData[0]['member_id'])->toArray();
            if (!empty($inaciveMemberPackages)) {
                foreach ($inaciveMemberPackages as $inaciveMemberPackage) {
                    if (!in_array($inaciveMemberPackage["crm_package_guid"], $memberPackageGuids)) {
                        $memberPackages[$inaciveMemberPackage["id"]] = $inaciveMemberPackage["package_title"];
                    }
                }
            }
            $selectedPackage = $sessionData[0]['package_id'];
            //$memberPackages[$selectedPackage] = $sessionData[0]['member_package']['package_title'];
        }

        //if ($cprData->toArray()) {
        $member_id = isset($sessionData[0]['member_id']) ? $sessionData[0]['member_id'] : $memberID;
        $package_id = isset($sessionData[0]['package_id']) ? $sessionData[0]['package_id'] : $this->repository->getPackageId($member_id);
        $session_center_id = isset($sessionData[0]['crm_center_id']) ? $sessionData[0]['crm_center_id'] : '';

        $slimmingProgrammeRecordCount = $this->repository->getSlimmingProgrammeRecordCount($member_id);
        $checkHypertension = $this->repository->checkHypertension($member_id);
        if ($slimmingProgrammeRecordCount > 0) {
            $checkHypertension = $this->repository->checkHypertension($member_id);
            if ($checkHypertension == 0) {
                $required = 'false';
            } else {
                $required = 'true';
            }
        } else {
            $required = 'true';
        }
        $food_habbit = config('settings.APP_FOOD_HABIT');
        $food_habbit_decode = html_entity_decode($food_habbit);
        $food_habbit_types = json_decode($food_habbit_decode, true);

        $medical_problem = config('settings.APP_MEDICAL_PROBLEM');
        $medical_problem_decode = html_entity_decode($medical_problem);
        $medical_problem_types = json_decode($medical_problem_decode, true);

        $measurement_record = config('settings.APP_MEASUREMENT_RECORD_FIELDS');
        $measurement_record_fields = json_decode(html_entity_decode($measurement_record), true);
        $measurement_record_fields_str = json_encode($measurement_record_fields);

        $session_date = $this->repository->getSessionDateList($member_id);
        //Get List of Therapists
        $therapistList = $this->repository->getTherapistList($member_id);

        if (!empty($session_date)) {
            $sessionDateList = $session_date->toArray();
        } else {
            $sessionDateList = array();
        }

        //Body Measurements
        if (isset($member_id) && !empty($member_id)) {
            $sessionDateData = $this->repository->getSessionInfo($member_id);
        }

        //Spot Reduction Measurements
        if (isset($member_id) && !empty($member_id)) {
            $sessionDateDataSpot = $this->repository->getSessionInfoSpot($member_id);
        }
        return view('admin::cpr.index', compact('session_id', 'food_habbit_types', 'package_id', 'member_id', 'medical_problem_types', 'logged_in_by_user_type', 'logged_in_user_id', 'measurement_record_fields', 'measurement_record_fields_str', 'required', 'membersList', 'centersList', 'sessionDateList', 'acl_flag', 'sessionDateData', 'sessionDateDataSpot', 'therapistList', 'memberPackages', 'selectedPackage', 'session_center_id'));
        //} else {
        //return abort(403, 'Unauthorized action.');
        //}
    }

    /**
     * Display a listing of the resource.
     *
     * @return datatable
     */
    public function getData(Request $request)
    {
        $cprData = $this->repository->data($request->all());
        return response()->json($cprData);
    }

    /**
     * Display a form to create new cpr.
     *
     * @return view as response
     */
    public function create()
    {
        return view('admin::cpr.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  Modules\Admin\Http\Requests\CPRCreateRequest $request
     * @return json encoded Response
     */
    public function store(CPRCreateRequest $request)
    {
        $response = $this->repository->create($request->all());
        return response()->json($response);
    }

    /**
     * Show the form for editing the specified cpr.
     *
     * @param  Modules\Admin\Models\CPR $cpr
     * @return json encoded Response
     */
    public function edit(CPR $cpr)
    {
        $attributes['cpr_date'] = date('d-m-Y', strtotime($cpr['attributes']['cpr_date']));
        $attributes['start_time'] = date('h:i A', strtotime($cpr['attributes']['start_time']));
        $attributes['end_time'] = date('h:i A', strtotime($cpr['attributes']['end_time']));
        $attributes['break_time'] = date('h:i A', strtotime($cpr['attributes']['break_time']));
        $attributes['carry_forward_cpr'] = $cpr['attributes']['carry_forward_cpr'];
        $response['success'] = true;
        $response['attributes'] = $attributes;
        $response['form'] = view('admin::cpr.edit', compact('cpr'))->render();
        return response()->json($response);
    }

    /**
     * Store an updated resource in storage.
     *
     * @param  Modules\Admin\Http\Requests\CPRUpdateRequest $request, Modules\Admin\Models\CPR $cpr
     * @return json encoded Response
     */
    public function update(CPRUpdateRequest $request, CPR $cpr)
    {
        $response = $this->repository->update($request->all(), $cpr);

        return response()->json($response);
    }

    public function getNewRow(Request $request)
    {
        $rowCount = $request->input('row_count');
        $response['form'] = view('admin::cpr.add-new-row', compact('rowCount'))->render();
        return response()->json($response);
    }

    /**
     * store bca record
     *
     */
    public function storeBcaRecords(BcaCreateRequest $request, ImageOptimizer $imageOptimizer)
    {
        $response = $this->repository->storeBcaRecord($request->all(), $imageOptimizer);
        return response()->json($response);
    }

    /**
     * fetch bca record
     *
     */
    public function getBcaRecord(Request $request)
    {
        $params['member_id'] = $request->input('member_id');
        $params['package_id'] = $request->input('package_id');
        $bcaRecord = $this->repository->getBcaRecord($params);
        return Datatables::of($bcaRecord)
                ->addColumn('bca_image', function ($bcaRecord) {
                    return '<div class="user-listing-img">' . ImageHelper::getUserBcaImage($bcaRecord->id, $bcaRecord->bca_image) . '</div>';
                })
                ->addColumn('recorded_date', function ($bcaRecord) {
                    $recorded_date = date('d-m-Y', strtotime($bcaRecord->recorded_date));
                    return $recorded_date;
                })
                ->make(true);
    }

    /**
     * store measurement record
     *
     */
    public function storeMeasurementRecords(MeasurementRecordCreateRequest $request)
    {
        $response = $this->repository->storeMeasurementRecord($request->all());
        return response()->json($response);
    }

    /**
     * fetch measurement record
     *
     */
    public function getMeasurementRecord(Request $request)
    {
        $measurementRecord = $this->repository->getMeasurementRecord($request->all());
        return Datatables::of($measurementRecord)
                ->addColumn('recorded_date', function ($measurementRecord) {
                    $recorded_date = date('d-m-Y', strtotime($measurementRecord->recorded_date));
                    return $recorded_date;
                })
                ->make(true);
    }

    /**
     * store session record summary
     *
     */
    public function storeSessionRecords(SessionRecordCreateRequest $request)
    {
        $response = $this->repository->storeSessionRecord($request->all());
        return response()->json($response);
    }

    /**
     * fetch session record summary
     *
     */
    public function getSessionRecord(Request $request)
    {
        $sessionRecord = $this->repository->getSessionRecord($request->all());
        return Datatables::of($sessionRecord)
                ->addColumn('recorded_date', function ($sessionRecord) {
                    $recorded_date = date('d-m-Y', strtotime($sessionRecord->recorded_date));
                    return $recorded_date;
                })
                ->addColumn('bp', function ($sessionRecord) {
                    if ($sessionRecord->bp == '') {
                        return "N/A";
                    } else {
                        return $sessionRecord->bp;
                    }
                })
                ->addColumn('ath_comment', function ($sessionRecord) {
                    $ath_comment_result = $this->escalation_matrix->getAthComment($sessionRecord->toArray());
                    if ($ath_comment_result == 0) {
                        $ath_comment = "";
                    } else {
                        $ath_comment = $ath_comment_result["ath_comment"];
                    }
                    return $ath_comment;
                })
                ->addColumn('net_weight_loss', function ($sessionRecord) {
                    $params = [
                        'session_id' => $sessionRecord->session_id,
                        'member_id' => $sessionRecord->member_id,
                        'package_id' => $sessionRecord->package_id,
                        'select' => 'net_weight_loss'
                    ];

                    $sessionRecordSummary = $this->repository->getSessionRecordSummary($params);
                    return (isset($sessionRecordSummary[0]->net_weight_loss)) ? $sessionRecordSummary[0]->net_weight_loss : '';
                })
                ->addColumn('net_weight_gain', function ($sessionRecord) {
                    $params = [
                        'session_id' => $sessionRecord->session_id,
                        'member_id' => $sessionRecord->member_id,
                        'package_id' => $sessionRecord->package_id,
                        'select' => 'net_weight_gain'
                    ];

                    $sessionRecordSummary = $this->repository->getSessionRecordSummary($params);
                    return (isset($sessionRecordSummary[0]->net_weight_gain)) ? $sessionRecordSummary[0]->net_weight_gain : '';
                })
                ->addColumn('balance_programme_kg', function ($sessionRecord) {
                    $params = [
                        'session_id' => $sessionRecord->session_id,
                        'member_id' => $sessionRecord->member_id,
                        'package_id' => $sessionRecord->package_id,
                        'select' => 'balance_programme_kg'
                    ];

                    $sessionRecordSummary = $this->repository->getSessionRecordSummary($params);
                    return (isset($sessionRecordSummary[0]->balance_programme_kg)) ? $sessionRecordSummary[0]->balance_programme_kg : '';
                })
                ->addColumn('otp_verified', function ($sessionRecord) {
                    if ($sessionRecord->otp_verified) {
                        $actionList = "<p class='session_wrapper" . $sessionRecord->id . "'><span id='.$sessionRecord->id.''>Verified<span></p>";
                    } else {
                        if (!empty(Auth::guard('admin')->user()->hasAdd) && Auth::guard('admin')->user()->id == $sessionRecord->created_by) {
                            $actionList = '<p class="session_wrapper' . $sessionRecord->id . '"><a href="javascript:;" class="btn default margin-bottom-5 green verify_otp"  id =' . $sessionRecord->id . '>Verify Otp</a></p>';
                        } else {
                            $actionList = "Not verified";
                        }
                    }
                    $actionList .= '<input type="hidden" id="before_weight' . $sessionRecord->id . '" name="before_weight' . $sessionRecord->id . '" value=' . $sessionRecord->before_weight . '>';
                    $actionList .= '<input type="hidden" id="after_weight' . $sessionRecord->id . '" name="after_weight' . $sessionRecord->id . '" value=' . $sessionRecord->after_weight . '>';
                    return $actionList;
                })
                ->addColumn('service_executed', function ($sessionRecord) {
                    if ($sessionRecord->service_executed == 2) {
                        $message = $this->repository->getServiceExecutionResponse($sessionRecord->session_id, $sessionRecord->id, $sessionRecord->member_id);
                        // If service execution flag is 2. Failed  CLM Service execution failed
                        $actionList = "<p class=''><span>Failed</span>";
                        if (!empty(Auth::guard('admin')->user()->hasAdd) && Auth::guard('admin')->user()->id == $sessionRecord->created_by) {
                            $actionList .= '<a style="margin-left: 10px;" href="javascript:;" id ="retry-clm-execution-' . $sessionRecord->id . '" class="btn btn-xs default retry-clm-execution-call" title="Retry"><i class="fa fa-refresh"></i></a></p>';
                        }
                        $actionList .= '<p>' . $message . '</p>';
                    } else if ($sessionRecord->service_executed == 1) {
                        // If service execution flag is 1 - .txt File is created and status is pending
                        $actionList = "<p class=''><span>Pending</span>";
                        if (!empty(Auth::guard('admin')->user()->hasAdd) && Auth::guard('admin')->user()->id == $sessionRecord->created_by) {
                            $actionList .= '<a style="margin-left: 10px;" href="javascript:;" id ="refresh-clm-execution-' . $sessionRecord->id . '"  class="btn btn-xs default refresh-session-record" title="refresh"><i class="fa fa-refresh"></i></a></p>';
                        }
                    } else if ($sessionRecord->service_executed == 3) {
                        $actionList = "<p class=''><span>Executed<span></p>";
                    } else {
                        // If service execution flag is 0 - Not executed yet
                        if (!empty(Auth::guard('admin')->user()->hasAdd) && Auth::guard('admin')->user()->id == $sessionRecord->created_by) {
                            $actionList = '<p class="service_execution_' . $sessionRecord->id . '"><a href="javascript:;" class="btn default margin-bottom-5 green service_execution"  id =' . $sessionRecord->id . '>Service Execution</a></p>';
                        } else {
                            $actionList = "Not Executed";
                        }
                    }
                    $actionList .= '<input type="hidden" id="session_id' . $sessionRecord->id . '" name="session_id' . $sessionRecord->id . '" value=' . $sessionRecord->session_id . '>';
                    return $actionList;
                })
                ->addColumn('therapist_id', function ($sessionRecord) {
                    $therapist_name = $this->repository->getTherapistName($sessionRecord->therapist_id);
                    $name = $therapist_name["first_name"] . " " . $therapist_name["last_name"] . " - " . $therapist_name["username"];
                    $name .= '<input type="hidden" id="therapist' . $sessionRecord->id . '" name="therapist' . $sessionRecord->id . '" value=' . $therapist_name["username"] . '>';
                    return $name;
                })
                ->addColumn('session_comment', function ($sessionRecord) {
                    return $sessionRecord->session_comment  = ($sessionRecord->session_comment == '') ?  'N/A'  :   $sessionRecord->session_comment;
                })
                ->make(true);
    }

    /**
     * store session record summary
     *
     */
    public function storeSessionRecordsSummary(Request $request)
    {
        $response = $this->repository->calculateSessionSummary($request->all());
        return response()->json($response);
    }

    /**
     * store dietary assessment data
     *
     */
    public function storeDietaryAssessment(DietaryAssessmentCreateRequest $request)
    {
        $response = $this->repository->storeDietaryAssessment($request->all());
        return response()->json($response);
    }

    /**
     * store fitness assessment data
     *
     */
    public function storeFitnessAssessment(FitnessAssessmentCreateRequest $request)
    {
        $response = $this->repository->storeFitnessAssessment($request->all());
        return response()->json($response);
    }

    /**
     * store BCA CSV
     *
     */
    public function uploadCsv(Request $request)
    {
        $requestData = $request->all();
        $filename = ImageHelper::uploadBcaFile($requestData['bca_csv_file'], $requestData['member_id'], $requestData['package_id']);
        $file = public_path('member_bca_data/' . $requestData['member_id'] . '-' . $requestData['package_id'] . '/' . $filename);
        $bcaArrays = array();

        $csv_reponse = Excel::load($file)->each(function (Collection $csvLine) {
            
        });

        $response = array();
        foreach ($csv_reponse as $k) {
            $data['reg_date'] = date('Y-m-d', strtotime($k['14._test_date_time']));
            $data['bmi'] = $k['36._bmi_body_mass_index'];
            $data['bmr'] = $k['66._bmr_basal_metabolic_rate'];
            $data['fat_weight'] = $k['27._bfm_body_fat_mass'];
            $data['fat_percent'] = $k['39._pbf_percent_body_fat'];
            $data['lean_body_mass_weight'] = $k['30._ffm_fat_free_mass'];
            $data['lean_body_mass_percent'] = round((($k['30._ffm_fat_free_mass'] / $k['15._weight']) * 100), 2);
            $data['water_weight'] = $k['18._tbw_total_body_water'];
            $data['water_percent'] = round((($k['18._tbw_total_body_water'] / $k['15._weight']) * 100), 2);
            $data['visceral_fat_level'] = $k['70._vfl_visceral_fat_level'];
            $data['target_weight'] = $k['62._target_weight'];
            $data['protein'] = $k['21._protein'];
            $data['mineral'] = $k['24._minerals'];
            array_push($response, $data);
        }

        return response()->json($response);
    }

    /**
     * check latest bca store date
     *
     */
    public function checkBcaDate(Request $request)
    {
        $requestData = $request->all();
        $param['member_id'] = $requestData['member_id'];
        $param['package_id'] = $requestData['package_id'];
        $response = $this->repository->getLatestBcaDate($param);
        if (!empty($response)) {
            $date1 = \DateTime::createFromFormat('Y-m-d', $response['recorded_date']);
            $date2 = \DateTime::createFromFormat('Y-m-d', date("Y-m-d"));

            $interval = $date2->diff($date1);
            return $month = $interval->format('%m');
        } else {
            $month = '';
        }

        return response()->json($month);
    }

    /**
     * fetch dietary assessment data.
     *
     */
    public function fetchDietaryAssessmentData(Request $request)
    {
        $requestData = $request->all();
        $param['member_id'] = $requestData['member_id'];
        $dietaryAssessmentData = $this->repository->getDietaryAssessmentData($param);
        if (!empty($dietaryAssessmentData)) {

            $dietary['food_allergy'] = $dietaryAssessmentData['food_allergy'];
            $dietary['smoking'] = $dietaryAssessmentData['smoking'];
            $dietary['smoking_frequency'] = $dietaryAssessmentData['smoking_frequency'];
            $dietary['meals_per_day'] = $dietaryAssessmentData['meals_per_day'];
            $dietary['food_habbit'] = $dietaryAssessmentData['food_habbit'];
            $dietary['eat_out_per_week'] = $dietaryAssessmentData['eat_out_per_week'];
            $dietary['fasting'] = $dietaryAssessmentData['fasting'];
            $dietary['alcohol'] = $dietaryAssessmentData['alcohol'];
            $dietary['alcohol_frequency'] = $dietaryAssessmentData['alcohol_frequency'];
            $dietary['diet_total_calories'] = $dietaryAssessmentData['diet_total_calories'];
            $dietary['diet_cho'] = $dietaryAssessmentData['diet_cho'];
            $dietary['diet_protein'] = $dietaryAssessmentData['diet_protein'];
            $dietary['diet_fat'] = $dietaryAssessmentData['diet_fat'];
            $dietary['remark'] = $dietaryAssessmentData['remark'];
            $dietary['wellness_counsellor_name'] = $dietaryAssessmentData['wellness_counsellor_name'];
        } else {
            $dietary['food_allergy'] = '';
            $dietary['smoking'] = '';
            $dietary['smoking_frequency'] = '';
            $dietary['meals_per_day'] = '';
            $dietary['food_habbit'] = '';
            $dietary['eat_out_per_week'] = '';
            $dietary['fasting'] = '';
            $dietary['alcohol'] = '';
            $dietary['alcohol_frequency'] = '';
            $dietary['diet_total_calories'] = '';
            $dietary['diet_cho'] = '';
            $dietary['diet_protein'] = '';
            $dietary['diet_fat'] = '';
            $dietary['remark'] = '';
            $dietary['wellness_counsellor_name'] = '';
        }
        return response()->json($dietary);
    }

    /**
     * fetch fitness assessment data.
     *
     */
    public function fetchfitnessAssessmentData(Request $request)
    {
        $requestData = $request->all();
        $param['member_id'] = $requestData['member_id'];
        $fitnessAssessmentData = $this->repository->getFitnessAssessmentData($param);
        if (!empty($fitnessAssessmentData)) {
            $fitness['static_posture'] = $fitnessAssessmentData['static_posture'];
            $fitness['sit_and_reach_test'] = $fitnessAssessmentData['sit_and_reach_test'];
            $fitness['shoulder_flexibility_right'] = $fitnessAssessmentData['shoulder_flexibility_right'];
            $fitness['shoulder_flexibility_left'] = $fitnessAssessmentData['shoulder_flexibility_left'];
            $fitness['pulse'] = $fitnessAssessmentData['pulse'];
            $fitness['back_problem_test'] = $fitnessAssessmentData['back_problem_test'];
            $fitness['current_activity_pattern'] = $fitnessAssessmentData['current_activity_pattern'];
            $fitness['current_activity_type'] = $fitnessAssessmentData['current_activity_type'];
            $fitness['current_activity_frequency'] = $fitnessAssessmentData['current_activity_frequency'];
            $fitness['current_activity_duration'] = $fitnessAssessmentData['current_activity_duration'];
            $fitness['remark'] = $fitnessAssessmentData['remark'];
            $fitness['home_care_kit'] = $fitnessAssessmentData['home_care_kit'];
            $fitness['physiotherapist_name'] = $fitnessAssessmentData['physiotherapist_name'];
        } else {
            $fitness['static_posture'] = '';
            $fitness['sit_and_reach_test'] = '';
            $fitness['shoulder_flexibility_right'] = '';
            $fitness['shoulder_flexibility_left'] = '';
            $fitness['pulse'] = '';
            $fitness['back_problem_test'] = '';
            $fitness['current_activity_pattern'] = '';
            $fitness['current_activity_type'] = '';
            $fitness['current_activity_frequency'] = '';
            $fitness['current_activity_duration'] = '';
            $fitness['remark'] = '';
            $fitness['home_care_kit'] = '';
            $fitness['physiotherapist_name'] = '';
        }
        return response()->json($fitness);
    }

    /**
     * Store medical assessment data.
     *
     */
    public function storeMedicalAssessment(MedicalAssessmentCreateRequest $request)
    {
        $response = $this->repository->storeMedicalAssessment($request->all());
        return response()->json($response);
    }

    /**
     * Fetch medical assessment data.
     *
     */
    public function fetchMedicalAssessmentData(Request $request)
    {
        $requestData = $request->all();
        $param['member_id'] = $requestData['member_id'];
        $medicalAssessmentData = $this->repository->fetchMedicalAssessmentData($param);
        $biochemicalCondition = $this->repository->fetchBiochemicalCondition($param);
        $medical['biochemical_profile'] = view('admin::cpr.biochemical-profile', compact('biochemicalCondition'))->render();

        $biochemicalConditionTest = $this->repository->fetchBiochemicalConditionTestData($param);

        $medical_problem = config('settings.APP_MEDICAL_PROBLEM');
        $medical_problem_decode = html_entity_decode($medical_problem);
        $medical_problem_types = json_decode($medical_problem_decode, true);

        if (!empty($medicalAssessmentData)) {
            $medical_problem = explode(",", $medicalAssessmentData['current_associated_medical_problem']);
            $medical['form'] = view('admin::cpr.medical-problem', compact('medical_problem', 'medical_problem_types'))->render();
            $medical['current_associated_medical_problem'] = $medical_problem;
            $medical['epilepsy'] = $medicalAssessmentData['epilepsy'];
            $medical['other'] = $medicalAssessmentData['other'];
            $medical['physical_finding'] = $medicalAssessmentData['physical_finding'];
            $medical['systemic_examination'] = $medicalAssessmentData['systemic_examination'];
            $medical['gynae_obstetrics_history'] = $medicalAssessmentData['gynae_obstetrics_history'];
            $medical['clients_birth_weight'] = $medicalAssessmentData['clients_birth_weight'];
            $medical['sleeping_pattern'] = $medicalAssessmentData['sleeping_pattern'];
            $medical['past_mediacl_history'] = $medicalAssessmentData['past_mediacl_history'];
            $medical['family_history_of_diabetes_obesity'] = $medicalAssessmentData['family_history_of_diabetes_obesity'];
            $medical['detailed_history'] = $medicalAssessmentData['detailed_history'];
            $medical['treatment_history'] = $medicalAssessmentData['treatment_history'];
            $medical['suggested_investigation'] = $medicalAssessmentData['suggested_investigation'];
            $medical['followup_date'] = $medicalAssessmentData['followup_date'];
            $medical['doctors_name'] = $medicalAssessmentData['doctors_name'];
            $medical['assessment_date'] = $medicalAssessmentData['assessment_date'];
        } else {
            $medical_problem = array();
            $medical['current_associated_medical_problem'] = $medical_problem;
            $medical['form'] = view('admin::cpr.medical-problem', compact('medical_problem', 'medical_problem_types'))->render();
            $medical['epilepsy'] = '';
            $medical['other'] = '';
            $medical['physical_finding'] = '';
            $medical['systemic_examination'] = '';
            $medical['gynae_obstetrics_history'] = '';
            $medical['clients_birth_weight'] = '';
            $medical['sleeping_pattern'] = '';
            $medical['past_mediacl_history'] = '';
            $medical['family_history_of_diabetes_obesity'] = '';
            $medical['detailed_history'] = '';
            $medical['treatment_history'] = '';
            $medical['suggested_investigation'] = '';
            $medical['followup_date'] = '';
            $medical['doctors_name'] = '';
            $medical['assessment_date'] = '';
        }

        if (!empty($biochemicalConditionTest)) {
            $medical['biochemicalConditionTest'] = $biochemicalConditionTest->toArray();
        } else {
            $medical['biochemicalConditionTest'] = '';
        }
        return response()->json($medical);
    }

    /**
     * Store skin & hair analysis data.
     *
     */
    public function storeSkinHairAnalysis(SkinHairAnalysisCreateRequest $request)
    {
        $response = $this->repository->storeSkinHairAnalysis($request->all());
        return response()->json($response);
    }

    /**
     * Fetch skin & hair analysis data.
     *
     */
    public function fetchSkinHairAnalysisData(Request $request)
    {
        $requestData = $request->all();
        $param['member_id'] = $requestData['member_id'];
        $skinHairAnalysisData = $this->repository->fetchSkinHairAnalysisData($param);
        if (!empty($skinHairAnalysisData)) {
            $analysis['skin_type'] = explode(",", $skinHairAnalysisData['skin_type']);
            $analysis['skin_condition'] = explode(",", $skinHairAnalysisData['skin_condition']);
            $analysis['hyperpigmentation_type'] = $skinHairAnalysisData['hyperpigmentation_type'];
            $analysis['hyperpigmentation_size'] = $skinHairAnalysisData['hyperpigmentation_size'];
            $analysis['hyperpigmentation_depth'] = $skinHairAnalysisData['hyperpigmentation_depth'];
            $analysis['scars_depth'] = $skinHairAnalysisData['scars_depth'];
            $analysis['scars_size'] = $skinHairAnalysisData['scars_size'];
            $analysis['scars_pigmented'] = $skinHairAnalysisData['scars_pigmented'];
            $analysis['fine_lines_and_wrinkles'] = $skinHairAnalysisData['fine_lines_and_wrinkles'];
            $analysis['skin_curvature'] = $skinHairAnalysisData['skin_curvature'];
            $analysis['other_marks'] = explode(",", $skinHairAnalysisData['other_marks']);
            $analysis['hair_type'] = explode(",", $skinHairAnalysisData['hair_type']);
            $analysis['condition_of_scalp'] = explode(",", $skinHairAnalysisData['condition_of_scalp']);
            $analysis['hair_density'] = $skinHairAnalysisData['hair_density'];
            $analysis['condition_of_hair_shaft'] = explode(",", $skinHairAnalysisData['condition_of_hair_shaft']);
            $analysis['history_of_allergy'] = $skinHairAnalysisData['history_of_allergy'];
            $analysis['conclusion'] = $skinHairAnalysisData['conclusion'];
            $analysis['skin_and_hair_specialist_name'] = $skinHairAnalysisData['skin_and_hair_specialist_name'];
            $analysis['analysis_date'] = $skinHairAnalysisData['analysis_date'];
        } else {
            $analysis['skin_type'] = '';
            $analysis['skin_condition'] = '';
            $analysis['hyperpigmentation_type'] = '';
            $analysis['hyperpigmentation_size'] = '';
            $analysis['hyperpigmentation_depth'] = '';
            $analysis['scars_depth'] = '';
            $analysis['scars_size'] = '';
            $analysis['scars_pigmented'] = '';
            $analysis['fine_lines_and_wrinkles'] = '';
            $analysis['skin_curvature'] = '';
            $analysis['other_marks'] = '';
            $analysis['hair_type'] = '';
            $analysis['condition_of_scalp'] = '';
            $analysis['hair_density'] = '';
            $analysis['condition_of_hair_shaft'] = '';
            $analysis['history_of_allergy'] = '';
            $analysis['conclusion'] = '';
            $analysis['skin_and_hair_specialist_name'] = '';
            $analysis['analysis_date'] = '';
        }
        return response()->json($analysis);
    }

    /**
     * Store Review of fitness assessment & activity pattern.
     *
     */
    public function storeReviewFitnessActivity(ReviewFitnessActivityCreateRequest $request)
    {
        $response = $this->repository->storeReviewFitnessActivity($request->all());
        return response()->json($response);
    }

    /**
     * Fetch Review of fitness assessment & activity pattern.
     */
    public function getReviewRecord(Request $request)
    {
        $requestData = $request->all();
        $param['member_id'] = $requestData['member_id'];
        $response = $this->repository->getReviewRecord($param);
        return Datatables::of($response)
                ->addColumn('review_date', function ($response) {
                    $review_date = date('d-m-Y', strtotime($response->review_date));
                    return $review_date;
                })->make(true);
    }

    // Function to fetch CPR form onchange of member
    public function fetchCprForm(Request $request)
    {
        $response["status"] = "error";
        if (isset($request->all()["member_id"]) && !empty($request->all()["member_id"])) {
            $acl_flag = !empty(Auth::guard('admin')->user()->hasAdd) ? 1 : 2;
            $member_id = $request->all()["member_id"];
            Session::set('member_id', $member_id);
            $logged_in_by_user_type = Auth::guard('admin')->user()->userType->id;
            $logged_in_user_id = Auth::guard('admin')->user()->id;
            $params['dietician_id'] = filter_var($logged_in_user_id, FILTER_SANITIZE_NUMBER_INT);
            $disableSubmit = 1;
            $sessionData = [];
            $selectedPackage = '';
            $memberHelper = new MemberHelper();
            if (Auth::guard('admin')->user()->userType->id == 9 || Auth::guard('admin')->user()->userType->id == 5) {
                $centersList = $memberHelper->getCentersList();
            }
            $membersList = $memberHelper->getUserWiseMemberList();

            // Fetch latest session if of selected member
            $latest_session_id = 0;
            $array["member_id"] = $request->all()["member_id"];
            $memberPackages = $this->sessionBooking->getMemberPackages($array, 0)->toArray();
            $center_id = (Session::get('center_id') != "") ? Session::get('center_id') : 0;
            $latest_session_id = $this->repository->getLatestSessionId($member_id, $center_id);
            $session_id = $latest_session_id;
            if ($latest_session_id == 0 || empty($latest_session_id)) {
                // Session id not available. Display only personal info & BCA data & disable all submit buttons
                $disableSubmit = 0;
            } else {
                $params["session_id"] = $latest_session_id;
                $cprData = $this->repository->validateSession($params);
                $sessionData = $cprData->toArray();
                $selectedPackage = $sessionData[0]['package_id'];

                $memberPackageGuids = $this->sessionBooking->getPackageGuids($memberPackages)->toArray();
                // Get packages of member whose status is inactive
                $inaciveMemberPackages = $this->sessionBooking->getInactiveMemberPackages($sessionData[0]['member_id'])->toArray();
                if (!empty($inaciveMemberPackages)) {
                    foreach ($inaciveMemberPackages as $inaciveMemberPackage) {
                        if (!in_array($inaciveMemberPackage["crm_package_guid"], $memberPackageGuids)) {
                            $memberPackages[$inaciveMemberPackage["id"]] = $inaciveMemberPackage["package_title"];
                        }
                    }
                }
                //$memberPackages[$selectedPackage] = $sessionData[0]['member_package']['package_title'];
            }

            $package_id = isset($sessionData[0]['package_id']) ? $sessionData[0]['package_id'] : 0;
            $member_id = isset($sessionData[0]['member_id']) ? $sessionData[0]['member_id'] : $member_id;
            $session_center_id = isset($sessionData[0]['crm_center_id']) ? $sessionData[0]['crm_center_id'] : '';

            $slimmingProgrammeRecordCount = $this->repository->getSlimmingProgrammeRecordCount($member_id);
            $checkHypertension = $this->repository->checkHypertension($member_id);
            if ($slimmingProgrammeRecordCount > 0) {
                $checkHypertension = $this->repository->checkHypertension($member_id);
                if ($checkHypertension == 0) {
                    $required = 'false';
                } else {
                    $required = 'true';
                }
            } else {
                $required = 'true';
            }

            $food_habbit = config('settings.APP_FOOD_HABIT');
            $food_habbit_decode = html_entity_decode($food_habbit);
            $food_habbit_types = json_decode($food_habbit_decode, true);

            $medical_problem = config('settings.APP_MEDICAL_PROBLEM');
            $medical_problem_decode = html_entity_decode($medical_problem);
            $medical_problem_types = json_decode($medical_problem_decode, true);

            $measurement_record = config('settings.APP_MEASUREMENT_RECORD_FIELDS');
            $measurement_record_fields = json_decode(html_entity_decode($measurement_record), true);
            $measurement_record_fields_str = json_encode($measurement_record_fields);

            $session_date = $this->repository->getSessionDateList($member_id);
            //Get List of Therapists
            $therapistList = $this->repository->getTherapistList($member_id);

            if (!empty($session_date)) {
                $sessionDateList = $session_date->toArray();
            } else {
                $sessionDateList = array();
            }

            //Body Measurements
            if (isset($member_id) && !empty($member_id)) {
                $sessionDateData = $this->repository->getSessionInfo($member_id);
            }

            //Spot Reduction Measurements
            if (isset($member_id) && !empty($member_id)) {
                $sessionDateDataSpot = $this->repository->getSessionInfoSpot($member_id);
            }

            $response['status'] = "success";
            $response['latest_session_id'] = $session_id;
            $response['form'] = view('admin::cpr.cpr-ajax', compact('session_id', 'food_habbit_types', 'package_id', 'member_id', 'medical_problem_types', 'logged_in_by_user_type', 'logged_in_user_id', 'measurement_record_fields', 'measurement_record_fields_str', 'required', 'membersList', 'centersList', 'sessionDateList', 'acl_flag', 'sessionDateData', 'sessionDateDataSpot', 'therapistList', 'memberPackages', 'selectedPackage', 'session_center_id'))->render();
            return response()->json($response);
        } else {
            
        }
    }

    public function storeMemberMeasurementRecord(MemberMeasurementRecordCreateRequest $request)
    {
        $response = $this->repository->storeMemberMeasurementRecord($request->all());
        return response()->json($response);
    }

    public function getMemberMeasurementRecord(Request $request)
    {
        $requestData = $request->all();
        $param['member_id'] = $requestData['member_id'];
        $response = $this->repository->getMemberMeasurementRecord($param);
        $valueArray = array();
        foreach ($response as $responseVal) {
            $valueArray[$responseVal['type']][$responseVal['date']][$responseVal['sub_type']] = $responseVal['value'];
        }
        return response()->json($valueArray);
    }

    public function storeMedicalReview(MemberMedicalreviewCreateRequest $request)
    {
        $response = $this->repository->storeMedicalReview($request->all());
        return response()->json($response);
    }

    public function fetchMedicalReviewRecord(Request $request)
    {
        $requestData = $request->all();
        $param['member_id'] = $requestData['member_id'];
        $response = $this->repository->getMedicalreviewRecord($param);
        return Datatables::of($response)
                ->addColumn('date', function ($response) {
                    $date = date('d-M-Y', strtotime($response->date));
                    return $date;
                })->make(true);
    }

    public function sendOtp(Request $request)
    {
        $member_id = $request->all()["member_id"];
        $mobileNumber = $this->repository->getMemberMobileNumber($member_id);
        //$mobileNumber = "9168073232";
        $otpNumber = $this->repository->generateOtp();
        $validateData['otp'] = $otpNumber;
        $validateData['mobile_number'] = $mobileNumber;
//        $messageText = "Welcome to VLCC Slimming Programme Record verification. Your sessions before weight is ".$request->all()["before_weight"]." kg and after weight is ".$request->all()["after_weight"]." kg. Please use OTP " . $otpNumber . " to complete your Slimming Programme Record verification. This OTP is valid till 15 mins.";        
        $messageText = "Hello, your weight before today's session was " . $request->all()["before_weight"] . " kg and weight after session is " . $request->all()["after_weight"] . " kg. Provide OTP " . $otpNumber . " in order to verify this information. This OTP is valid till 15 minutes.";
        $validateData['message_text'] = $messageText;
        $sendMessage = $this->repository->sendOtp($mobileNumber, $validateData);
        $otpId = 0;
        if ($sendMessage) {
            $params = [];
            $params["mobile_number"] = $mobileNumber;
            $params["otp"] = $otpNumber;
            $params["sms_delivered"] = 1;
            $params["error_message"] = "";
            $params["otp_used"] = 0;
            $params["platform_generated_for"] = 3;
            $params["otp_generated_for"] = 202;
            $otpId = $this->repository->saveOtp($params);
        }
        $response["otp_id"] = $otpId;
        return response()->json($response);
    }

    public function verifyOtp(Request $request)
    {
        $smsValidityTime = 15;
        $params = $request->all();
        $mobileNumber = $this->repository->getMemberMobileNumber($params["member_id"]);
        $params["mobile_number"] = $mobileNumber;
        //$params["mobile_number"] = "9168073232";
        $getOtpSendTime = $this->repository->getOtpSendTime($params);
        $response["success"] = "error";
        if ($getOtpSendTime != 0) {
            $send_time = explode(" ", $getOtpSendTime);
            $send_time = strtotime($send_time[1]);
            $current_time = strtotime(date("H:i:s"));
            $difference = round(abs($current_time - $send_time) / 60, 2);
            if ($difference < $smsValidityTime) {
                $validOtp = $this->repository->validateOtp($params);
                if ($validOtp != 0) {
                    if ($validOtp == 1) {
                        $update_session_otp = $this->repository->updateSessionOtpFlag($params);
                        $response["message"] = "OTP Verified successfully.";
                        $response["success"] = "success";
                    } else {
                        $response["message"] = "OTP is incorrect.Please try again.";
                    }
                } else {
                    $response["message"] = "This OTP is already used.";
                }
            } else {
                $response["message"] = "Your OTP has expired.Please generate new OTP.";
            }
        } else {
            $response["message"] = "OTP is incorrect.Please try again";
        }
        return response()->json($response);
    }

    // Function to Call CLM Service Execution API if all Measurements specifications are present in database
    public function serviceExecution(Request $request)
    {
        $response["status"] = "error";
        $response["message"] = "Technical Error occured";
        $error_messages = [];
        $measurement_record_fields = [];
        $member_id = filter_var($request->all()["member_id"], FILTER_VALIDATE_INT);
        $session_id = filter_var($request->all()["session_id"], FILTER_VALIDATE_INT);
        $before_weight = $request->all()["before_weight"];
        $after_weight = $request->all()["after_weight"];
        $therapist = $request->all()["therapist"];
        $member_session_record_id = $request->all()["member_session_record_id"];
        // Get Session service from session id, get session data 
        $session_service = $this->sessionBooking->getSessionData($session_id);
        if (isset($session_service["service_id"]) && !empty($session_service["service_id"])) {
            // Function to get Area Specification values in array format
            $area_specifications = $this->repository->getServiceAreaSpecification($session_service["service_id"]);
            //$area_specifications[] = "Whole Body";
            if (!empty($area_specifications)) {
                $measurement_record = config('settings.APP_MEASUREMENT_RECORD_FIELDS');
                $measurement_record_fields = json_decode(html_entity_decode($measurement_record), true);
                // Check area specification fields
                foreach ($area_specifications as $specification) {
                    if ($specification == "Whole Body") {
                        // check if all form fields for Whole Body (Body Measurements) are added in database or not
                        $check_whole_body = $this->repository->checkWholeBodyFields($member_id, $session_id);
                        if ($check_whole_body["count"] == 0) {
                            array_push($error_messages, 'Please enter all measurements in Body Measurements in CPR.');
                        }
                    } else {
                        $measurement_records = $measurement_record_fields[$specification];
                        $check_spot_reduction_measurements = $this->repository->checkSpotReductionFields($member_id, $session_id, $measurement_records);
                        if (!$check_spot_reduction_measurements["flag"]) {
                            array_push($error_messages, 'Please enter all measurements for \'' . $specification . '\' in Spot Reduction Measurements in CPR.');
                        }
                    }
                }
            }
            if (empty($error_messages)) {
                // All Values are present in database & Call CLM Api for Service execution
                // Get Session service data
                $service_count = explode(",", $session_service["service_id"]);

                // Get Session programme record Service execution status. Generate only those files for which status Code is not 200
                $executed_service_ids = [];
                $service_execution_status = $this->repository->getMemberSessionRecordData($member_session_record_id);
                if (!empty($service_execution_status->service_execution_status)) {
                    $data = json_decode($service_execution_status->service_execution_status, true);
                    foreach ($data as $datakey => $datavalue) {
                        if ($data[$datakey]["code"] == 200) {
                            array_push($executed_service_ids, $data[$datakey]["service_id"]);
                        }
                    }
                }

                // If session booked against multiple services, call crm api multiple times
                foreach ($service_count as $service_id) {
                    if (!in_array($service_id, $executed_service_ids)) {
                        $service_execution_params = [];
                        // Get service_code, service_guid data
                        $service_data = $this->repository->getServiceData($service_id);
                        // Convert area specifications to array
                        $specifications = explode(",", $service_data["area_specification"]);
                        // Create Json string
                        $service_execution_params["PackageID"] = $session_service["crm_package_guid"];
                        $service_execution_params["SessionDate"] = Carbon::createFromFormat('Y-m-d', $session_service["session_date"])->format('m/d/Y');
                        $service_execution_params["ServiceCode"] = $service_data["service_code"];
                        $service_execution_params["ClientID"] = $session_service["crm_customer_id"];
                        $service_execution_params["PackageExecutionID"] = $service_data["crm_service_guid"];
                        $service_execution_params["CenterCode"] = $session_service["crm_center_id"];
                        $service_execution_params["WeightBefore"] = $before_weight;
                        $service_execution_params["WeightAfter"] = $after_weight;
                        $service_execution_params["Therapist"] = $therapist;

                        // Pass key Dietician when slimming service is used
                        if ($service_data["service_category"] == "100000001") {
                            $service_execution_params["Dietition"] = $session_service["dietician_username"];
                            $service_execution_params["Counsellor"] = $session_service["dietician_username"];
                        }

                        if ($service_data["service_category"] == "100000001" && $measurement_record_fields) {
                            $service_execution_params["AreaSpecification1"] = isset($specifications[0]) ? $specifications[0] : '';
                            $service_execution_params["AreaSpecification2"] = isset($specifications[1]) ? $specifications[1] : '';
                            $service_execution_params["AreaSpecification3"] = isset($specifications[2]) ? $specifications[2] : '';
                            // Call function to create area specifications json
                            $service_execution_params = $this->repository->createSpecificationJson($specifications, $member_id, $session_id, $service_execution_params, $measurement_record_fields);
                        }

                        // Save json encoded code in file in folder public/service_execution
                        //$file = fopen("/var/www/html/vlcc-admin/public/service_execution_files/".$session_id."-".$member_id."-".$service_id."-".$session_service["dietician_username"].".txt","w");
                        $file = fopen("/var/www/html/vlcc-admin/public/service_execution_files/" . $session_service["dietician_username"] . "-" . $member_id . "-" . $session_id . "-" . $service_id . "-" . $member_session_record_id . ".txt", "w");
                        fwrite($file, json_encode($service_execution_params));
                        fclose($file);
                        $msg = "File saved successfully.";
                    }
                }
                // Update flag in member_session_record as 1
                $this->repository->updateServiceExecutedFlag($member_session_record_id, 0);
                $response["status"] = "success";
                $response["message"] = "Service Executed successfully.";
            } else {
                $response["message"] = $error_messages;
            }
            //} else {
            //    // Error message area specifications not present.
            //    $response["message"] = "Service area specifications not available.";
            //}
        } else {
            // Error message Technical error occured!
            $response["message"] = "Session service not available.";
        }
        return response()->json($response);
    }

    // Update Service Execution Flag
    public function updateServiceExecutionFlag(Request $request)
    {
        $response["status"] = "error";
        $session_programme_record = filter_var($request->all()["session_record_id"], FILTER_VALIDATE_INT);
        $flag = isset($request->all()["flag"]) ? $request->all()["flag"] : '';
        if ($flag == "retry") {
            $result = $this->repository->updateServiceExecutedFlag($session_programme_record, 2);
        } else {
            $result = $this->repository->updateServiceExecutedFlag($session_programme_record, 1);
        }
        if ($result) {
            $response["status"] = "success";
        }
        return response()->json($response);
    }
}
