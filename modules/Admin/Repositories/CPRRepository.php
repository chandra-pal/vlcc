<?php
/**
 * The repository class for managing activity type specific actions.
 *
 *
 * @author Sachin Gend <saching@iprogrammer.com>
 * @package Admin
 * @since 1.0
 */

namespace Modules\Admin\Repositories;

use Modules\Admin\Models\SessionBookings;
use Modules\Admin\Models\CPR;
use Modules\Admin\Models\Member;
use Modules\Admin\Models\MemberPackage;
use Modules\Admin\Models\MemberBcaDetails;
use Modules\Admin\Models\MemberMeasurementDetails;
use Modules\Admin\Models\MemberSessionRecord;
use Modules\Admin\Models\MemberSessionRecordSummary;
use Modules\Admin\Models\MemberDietaryAssessment;
use Modules\Admin\Models\MemberFitnessAssessment;
use Modules\Admin\Models\MemberMedicalAssessment;
use Modules\Admin\Models\MemberOtp;
use Exception;
use Route;
use Log;
use Cache;
use Auth;
use Modules\Admin\Services\Helper\ImageHelper;
use Approached\LaravelImageOptimizer\ImageOptimizer;
use DB;
use PDO;
use Modules\Admin\Models\MemberSkinHairAnalysis;
use Modules\Admin\Models\BiochemicalCondition;
use Modules\Admin\Models\BiochemicalConditionTest;
use Modules\Admin\Models\MemberBiochemicalProfile;
use Modules\Admin\Models\MemberActivityFitnessReview;
use Modules\Admin\Models\MemberMeasurementRecords;
use Modules\Admin\Models\MemberMedicalReview;
use Carbon\Carbon;
use Modules\Admin\Models\Recommendation;
use Modules\Admin\Repositories\RecommendationRepository;
use Modules\Admin\Repositories\SessionBookingsRepository;
use Config;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Client;
use Modules\Admin\Models\User;

class CPRRepository extends BaseRepository
{

    /**
     * Create a new CPRRepository instance.
     *
     * @param  Modules\Admin\Models\CPR $model
     * @return void
     */
    public function __construct(Member $memberModel, MemberPackage $memberPackageModel, MemberBcaDetails $memberBcaDetails, MemberMeasurementDetails $memberMeasurementDetails, MemberSessionRecord $memberSessionRecord, MemberSessionRecordSummary $memberSessionRecordSummary, MemberDietaryAssessment $memberDietaryAssessment, MemberFitnessAssessment $memberFitnessAssessment, MemberMedicalAssessment $memberMedicalAssessment, MemberSkinHairAnalysis $memberSkinHairAnalysis, MemberBiochemicalProfile $memberBiochemicalProfile, MemberActivityFitnessReview $memberActivityFitnessReview, MemberMeasurementRecords $memberMeasurementRecords, MemberMedicalReview $memberMedicalReview, RecommendationRepository $recommendationRepository, Recommendation $recommendation, MemberOtp $memberOtp, SessionBookingsRepository $sessionBookingsRepository)
    {
        $this->memberModel = $memberModel;
        $this->memberPackageModel = $memberPackageModel;
        $this->memberBcaDetails = $memberBcaDetails;
        $this->memberMeasurementDetails = $memberMeasurementDetails;
        $this->memberSessionRecord = $memberSessionRecord;
        $this->memberSessionRecordSummary = $memberSessionRecordSummary;
        $this->memberDietaryAssessment = $memberDietaryAssessment;
        $this->memberFitnessAssessment = $memberFitnessAssessment;
        $this->memberMedicalAssessment = $memberMedicalAssessment;
        $this->memberSkinHairAnalysis = $memberSkinHairAnalysis;
        $this->memberBiochemicalProfile = $memberBiochemicalProfile;
        $this->memberActivityFitnessReview = $memberActivityFitnessReview;
        $this->memberMeasurementRecords = $memberMeasurementRecords;
        $this->memberMedicalReview = $memberMedicalReview;
        $this->recommendationRepository = $recommendationRepository;
        $this->sessionBookingsRepository = $sessionBookingsRepository;
        $this->recommendation = $recommendation;
        $this->memberOtp = $memberOtp;
    }

    /**
     * Get a listing of the resource.
     *
     * @return Response
     */
    public function data($params = [])
    {
        if ($params["session_id"] != 0) {
            $response = SessionBookings::with(['member', 'memberPackage'])->where('id', $params['session_id'])->get();
            $response['todaySessionRecord'] = $this->memberSessionRecord->where('session_id', $params['session_id'])->get();
        } else {
            $result = Member::with(['memberPackageOne'])->where('id', $params["member_id"])->first();
            $response["0"]["member"] = $result->toArray();
            $response["0"]["member_package"] = isset($result->toArray()['member_package_one']) ? $result->toArray()['member_package_one'] : [];
            $response['todaySessionRecord'] = [];
            $response = collect($response);
        }
        return $response;
    }

    /**
     * Get a listing of the resource.
     *
     * @return Response
     */
    public function validateSession($params = [])
    {
        //$response = SessionBookings::with(['member', 'memberPackage'])->where('id', $params['session_id'])->where('dietician_id', $params['dietician_id'])->get();
        $response = SessionBookings::with(['member', 'memberPackage'])->where('id', $params['session_id'])->get();
        return $response;
    }

    public function getBcaRecord($params)
    {
//        return MemberBcaDetails::where('member_id', $params['member_id'])->where('package_id', $params['package_id'])->orderBy('recorded_date', 'desc')->get();
        return MemberBcaDetails::where('member_id', $params['member_id'])->orderBy('recorded_date', 'desc')->get();
    }

    public function getMeasurementRecord($params)
    {
        return MemberMeasurementDetails::where('member_id', $params['member_id'])->where('package_id', $params['package_id'])->orderBy('recorded_date', 'asc')->get();
    }

    public function getSessionRecord($params)
    {
        return MemberSessionRecord::where('member_id', $params['member_id'])->where('package_id', $params['package_id'])->orderBy('recorded_date', 'asc')->get();
    }

    public function getSessionRecordSummary($params)
    {
        return MemberSessionRecordSummary::select((string) $params['select'])->where('member_id', $params['member_id'])->where('package_id', $params['package_id'])->where('session_id', $params['session_id'])->orderBy('recorded_date', 'asc')->get();
    }

    public function getDietaryAssessmentData($params)
    {
        return MemberDietaryAssessment::where('member_id', $params['member_id'])->first();
    }

    public function getFitnessAssessmentData($params)
    {
        return MemberFitnessAssessment::where('member_id', $params['member_id'])->first();
    }

    public function getLatestBcaDate($params)
    {
        return MemberBcaDetails::select('recorded_date')->whereMemberId($params['member_id'])->wherePackageId($params['package_id'])->orderBy('recorded_date', 'DESC')->first();
    }

    public function fetchMedicalAssessmentData($params)
    {
        return MemberMedicalAssessment::where('member_id', $params['member_id'])->first();
    }

    public function fetchSkinHairAnalysisData($params)
    {
        return MemberSkinHairAnalysis::where('member_id', $params['member_id'])->first();
    }

    public function fetchBiochemicalCondition()
    {
        return BiochemicalCondition::with('ConditionTest')->groupBy('id')->get()->toArray();
    }

    public function fetchBiochemicalConditionTestData($params)
    {
        return MemberBiochemicalProfile::where('member_id', $params['member_id'])->get();
    }

    public function getReviewRecord($params)
    {
        return MemberActivityFitnessReview::where('member_id', $params['member_id'])->get();
    }

    public function getMemberMeasurementRecord($params)
    {
        DB::setFetchMode(PDO::FETCH_ASSOC);
        $response = DB::select("SELECT id,member_id, date ,type,sub_type,value FROM member_measurement_records WHERE member_id=" . $params["member_id"] . " ORDER BY date ASC");
        DB::setFetchMode(PDO::FETCH_CLASS);
        return $response;
    }

    public function getMedicalreviewRecord($params)
    {
        return MemberMedicalReview::where('member_id', $params['member_id'])->orderBy('date', 'DESC')->orderBy('created_at', 'DESC')->get();
    }

    public function getSlimmingProgrammeRecordCount($member_id)
    {
        return MemberSessionRecord::where('member_id', $member_id)->count();
    }

    public function checkHypertension($member_id)
    {
        $get_data = MemberMedicalAssessment::select('current_associated_medical_problem')->where('member_id', $member_id)->first();
        if (!empty($get_data)) {
            $mediacl_prob = $get_data->toArray();
            if (in_array('2', explode(',', $mediacl_prob['current_associated_medical_problem']))) {
                return 1;
            } else {
                return 0;
            }
        } else {
            return 0;
        }
    }

    public function getSessionDateList($member_id)
    {
        $current_date = \Carbon\Carbon::now()->format('Y-m-d');
        return SessionBookings::orderBY('session_date', 'DESC')->whereMemberId($member_id)->where('session_date', '<=', $current_date)->where('doctor_comment', '=', '')->lists('session_date', 'id');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  Form data posted from ajax $inputs
     * @return $result array with status and message elements
     */
    public function create($inputs, $user_id = null)
    {
        try {
//            member update
            $member = new $this->memberModel;
            $memberData = [
                'first_name' => $inputs['first_name'],
                'last_name' => $inputs['last_name'],
                'email' => $inputs['email'] ? $inputs['email'] : '',
                'alternate_phone_number' => $inputs['alternate_phone_number'],
                'gender' => $inputs['gender'],
                'date_of_birth' => date('Y-m-d', strtotime($inputs['dob'])),
                'address' => $inputs['address'],
                'profession' => $inputs['profession'],
                'family_physician_name' => $inputs['family_physician_name'],
                'family_physician_number' => $inputs['family_physician_number'],
                'existing_medical_problem' => $inputs['existing_medical_problem'],
                'therapies' => $inputs['therapies'],
                'services_to_be_avoided' => $inputs['services_to_be_avoided'],
                'category_code' => $inputs['category_code'],
                'updated_by' => $inputs['created_by']
            ];
            $saveMember = $member->where('id', $inputs['member_id'])->update($memberData);
//            member package update

            $memberPackage = new $this->memberPackageModel;
            $memberPackageData = [
                'height' => $inputs['height'],
                'weight' => $inputs['weight'],
                'waist' => $inputs['waist'],
                'programme_booked' => $inputs['programme_booked'],
                'programme_booked_by' => $inputs['programme_booked_by'],
                'programme_needed' => $inputs['programme_needed'],
                'conversion' => $inputs['conversion'],
                'programme_re_booked' => $inputs['programme_re_booked'],
                'remarks' => $inputs['remarks'],
                'updated_by' => $inputs['created_by']
            ];

            $saveMemberPackage = $memberPackage->where('id', $inputs['package_no'])
                ->where('member_id', $inputs['member_id'])
                ->update($memberPackageData);


            if ($saveMember && $saveMemberPackage) {
                $response['status'] = 'success';
                $response['message'] = trans('admin::messages.updated', ['name' => trans('admin::controller/cpr.personal-info')]);
            } else {
                $response['status'] = 'error';
                $response['message'] = trans('admin::messages.not-updated', ['name' => trans('admin::controller/cpr.personal-info')]);
            }
            return $response;
        } catch (Exception $e) {
            $exceptionDetails = $e->getMessage();
            $response['status'] = 'error';
            $response['message'] = trans('admin::messages.not-uodated', ['name' => trans('admin::controller/cpr.personal-info')]) . "<br /><b> Error Details</b> - " . $exceptionDetails;
            Log::error(trans('admin::messages.not-added', ['name' => trans('admin::controller/cpr.personal-info')]), ['Error Message' => $exceptionDetails, 'Current Action' => Route::getCurrentRoute()->getActionName()]);

            return $response;
        }
    }

    /**
     * Update an activity type.
     *
     * @param  Form data posted from ajax $inputs, Modules\Admin\Models\CPR $cpr
     * @return $result array with status and message elements
     * @return void
     */
    public function update($inputs, $cpr)
    {
        try {
            $save = [];
            $cprInsertOrUpdate = [
                'updated_by' => (int) $inputs['updated_by'],
                'start_time' => date('H:i:s', strtotime($inputs['start_time'])),
                'end_time' => date('H:i:s', strtotime($inputs['end_time'])),
                'break_time' => date('H:i:s', strtotime($inputs['break_time'])),
                'carry_forward_cpr' => (int) $inputs['carry_forward_cpr'],
                'carry_forward_cpr_days' => ($inputs['carry_forward_cpr'] == 1) ? (int) $inputs['carry_forward_cpr_days'] : 0
            ];
            if ($inputs['carry_forward_cpr'] != 1) {
                foreach ($cprInsertOrUpdate as $key => $value) {
                    if (isset($cpr->$key)) {
                        $cpr->$key = $value;
                    }
                }
                $save[] = $cpr->save();
            } else {
                $endDate = date('Y-m-d', strtotime('+14 Days'));
                for ($i = 0; $i <= $inputs['carry_forward_cpr_days']; $i++) {
                    $cprDate = date('Y-m-d', strtotime($cpr->cpr_date . ' +' . $i . ' Days'));
                    if ($cprDate >= date('Y-m-d', strtotime($cpr->cpr_date)) && $cprDate <= $endDate) {
                        $cprNew = new $this->model;
                        $whereClause = [
                            'dietician_id' => $cpr->dietician_id,
                            'cpr_date' => $cprDate
                        ];
                        $cprInsertOrUpdate ['dietician_id'] = (int) $cpr->dietician_id;
                        $cprInsertOrUpdate ['cpr_date'] = $cprDate;
                        $cprInsertOrUpdate ['created_by'] = (int) $inputs['updated_by'];
                        $save[] = $cprNew->updateOrCreate($whereClause, $cprInsertOrUpdate);
                    }
                }
            }
            if (!empty($save)) {
                $response['status'] = 'success';
                $response['message'] = trans('admin::messages.updated', ['name' => trans('admin::controller/cpr.cpr')]);
            } else {
                $response['status'] = 'error';
                $response['message'] = trans('admin::messages.not-updated', ['name' => trans('admin::controller/cpr.cpr')]);
            }
            return $response;
        } catch (Exception $e) {
            $exceptionDetails = $e->getMessage();
            $response['status'] = 'error';
            $response['message'] = trans('admin::messages.not-updated', ['name' => trans('admin::controller/cpr.cpr')]) . "<br /><b> Error Details</b> - " . $exceptionDetails;
            Log::error(trans('admin::messages.not-updated', ['name' => trans('admin::controller/cpr.cpr')]), ['Error Message' => $exceptionDetails, 'Current Action' => Route::getCurrentRoute()->getActionName()]);
            return $response;
        }
    }

    public function storeBcaRecord($inputs, ImageOptimizer $imageOptimizer)
    {

        try {
            $memberBcaDetails = new $this->memberBcaDetails;

            $last_inserted_id = DB::table('member_bca_details')->insertGetId(
                [
                    'package_id' => $inputs['package_id'],
                    'member_id' => $inputs['member_id'],
                    'recorded_date' => date('Y-m-d', strtotime($inputs['recorded_date'])),
                    'body_mass_index' => $inputs['body_mass_index'],
                    'body_mass_index' => $inputs['body_mass_index'],
                    'basal_metabolic_rate' => $inputs['basal_metabolic_rate'],
                    'fat_weight' => $inputs['fat_weight'],
                    'fat_percent' => $inputs['fat_percent'],
                    'lean_body_mass_weight' => $inputs['lean_body_mass_weight'],
                    'lean_body_mass_percent' => $inputs['lean_body_mass_percent'],
                    'water_weight' => $inputs['water_weight'],
                    'water_percent' => $inputs['water_percent'],
                    'visceral_fat_level' => $inputs['visceral_fat_level'],
                    'visceral_fat_area' => $inputs['visceral_fat_area'],
                    'target_weight' => $inputs['target_weight'],
                    'target_fat_percent' => $inputs['target_fat_percent'],
                    'bca_image' => $inputs['bca_image'],
                    'protein' => $inputs['protein'],
                    'mineral' => $inputs['mineral'],
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now()
                ]
            );

            $this->updateBcaImage($inputs, $memberBcaDetails, $last_inserted_id, $imageOptimizer);
            $checkrecord = MemberBcaDetails::select('recorded_date')->whereMemberId($inputs['member_id'])->wherePackageId($inputs['package_id'])->orderBy('recorded_date', 'DESC')->first();
            if (!empty($checkrecord)) {
                $date1 = \DateTime::createFromFormat('Y-m-d', $checkrecord['recorded_date']);
                $date2 = \DateTime::createFromFormat('Y-m-d', date("Y-m-d"));

                $interval = $date2->diff($date1);
                $month = $interval->format('%m');
            } else {
                $month = 'empty';
            }

            if ($last_inserted_id) {
                $response['status'] = 'success';
                $response['message'] = trans('admin::messages.added', ['name' => trans('admin::controller/cpr.bca-data')]);
                $response['month'] = $month;
            } else {
                $response['status'] = 'error';
                $response['message'] = trans('admin::messages.not-added', ['name' => trans('admin::controller/cpr.bca-data')]);
                $response['month'] = '';
            }

            return $response;
        } catch (Exception $e) {
            $exceptionDetails = $e->getMessage();
            $response['status'] = 'error';
            $response['message'] = trans('admin::messages.not-added', ['name' => trans('admin::controller/cpr.bca-data')]) . "<br /><b> Error Details</b> - " . $exceptionDetails;
            Log::error(trans('admin::messages.not-added', ['name' => trans('admin::controller/cpr.bca-data')]), ['Error Message' => $exceptionDetails, 'Current Action' => Route::getCurrentRoute()->getActionName()]);

            return $response;
        }
    }

    public function updateBcaImage($inputs, $memberBcaDetails, $last_inserted_id, $imageOptimizer)
    {
        if (!empty($inputs['bca_image'])) {
            //unlink old file
            if (!empty($memberBcaDetails->bca_image)) {
                File::Delete(public_path() . ImageHelper::getBcaUploadedFolder($memberBcaDetails->member_id) . $memberBcaDetails->bca_image);
            }
            $imageOptimizer = new ImageOptimizer;
            $updated_image = ImageHelper::updateBcaImage($inputs['bca_image'], $memberBcaDetails, $imageOptimizer);
            DB::table('member_bca_details')
                ->where('id', $last_inserted_id)
                ->update(['bca_image' => $updated_image]);
        } else {
            $memberBcaDetails->save();
        }
    }

    public function storeMeasurementRecord($inputs)
    {
        try {
            $memberMeasurementDetails = new $this->memberMeasurementDetails;

            $allColumns = $memberMeasurementDetails->getTableColumns($memberMeasurementDetails->getTable());

            foreach ($inputs as $key => $value) {
                if (in_array($key, $allColumns)) {
                    $memberMeasurementDetails->$key = $value;
                }
            }

            //$memberMeasurementDetails->recorded_date = date('Y-m-d', strtotime($inputs['recorded_date']));
            $memberMeasurementDetails->session_id = $inputs['recorded_date'];
            $sessionDateSelected = $this->sessionBookingsRepository->getSessionData($inputs['recorded_date']);
            $memberMeasurementDetails->recorded_date = $sessionDateSelected['session_date'];

            $save = $memberMeasurementDetails->save();

            if ($save) {
                $response['status'] = 'success';
                $response['message'] = trans('admin::messages.added', ['name' => trans('admin::controller/cpr.measurements-records')]);
            } else {
                $response['status'] = 'error';
                $response['message'] = trans('admin::messages.not-added', ['name' => trans('admin::controller/cpr.measurements-records')]);
            }

            return $response;
        } catch (Exception $e) {
            $exceptionDetails = $e->getMessage();
            $response['status'] = 'error';
            $response['message'] = trans('admin::messages.not-added', ['name' => trans('admin::controller/cpr.measurements-records')]) . "<br /><b> Error Details</b> - " . $exceptionDetails;
            Log::error(trans('admin::messages.not-added', ['name' => trans('admin::controller/cpr.measurements-records')]), ['Error Message' => $exceptionDetails, 'Current Action' => Route::getCurrentRoute()->getActionName()]);

            return $response;
        }
    }

    public function storeSessionRecord($inputs)
    {
        try {
            $memberSessionRecord = new $this->memberSessionRecord;

            $allColumns = $memberSessionRecord->getTableColumns($memberSessionRecord->getTable());

            foreach ($inputs as $key => $value) {
                if (in_array($key, $allColumns)) {
                    $memberSessionRecord->$key = $value;
                }
            }

            $memberSessionRecord->recorded_date = date('Y-m-d', strtotime($inputs['recorded_date']));

            $save = $memberSessionRecord->save();
            SessionBookings::where('id', $inputs['session_id'])->where('member_id', $inputs['member_id'])->update(['status' => 5]);
            $inputs['isForceAction'] = 0;
            $saveSummery = $this->calculateSessionSummary($inputs);
            
            if (Auth::guard('admin')->user()->userType->id == 4 || Auth::guard('admin')->user()->userType->id == 8) {
                // Update Members Dietician Username 
                DB::table('members')->where('id', $inputs["member_id"])->update(['dietician_username' => Auth::guard('admin')->user()->username]);
            }

            // Update members table weekday column
            $week_day = Member::select('week_day')
                ->where('id', $inputs['member_id'])
                ->first();
            if (empty($week_day->week_day)) {
                //Update week day column
                $todays_day = date('w');
                Member::where('id', $inputs['member_id'])->update(['week_day' => $todays_day]);
            } 

            if ($save) {
                $response['status'] = 'success';
                $response['message'] = trans('admin::messages.added', ['name' => trans('admin::controller/cpr.session-records')]);
            } else {
                $response['status'] = 'error';
                $response['message'] = trans('admin::messages.not-added', ['name' => trans('admin::controller/cpr.session-records')]);
            }

            return $response;
        } catch (Exception $e) {
            $exceptionDetails = $e->getMessage();
            $response['status'] = 'error';
            $response['message'] = trans('admin::messages.not-added', ['name' => trans('admin::controller/cpr.session-records')]) . "<br /><b> Error Details</b> - " . $exceptionDetails;
            Log::error(trans('admin::messages.not-added', ['name' => trans('admin::controller/cpr.session-records-')]), ['Error Message' => $exceptionDetails, 'Current Action' => Route::getCurrentRoute()->getActionName()]);

            return $response;
        }
    }

    public function calculateSessionSummary($inputs)
    {
        try {
            if (!isset($inputs['created_by'])) {
                $inputs['created_by'] = filter_var(Auth::guard('admin')->user()->id, FILTER_SANITIZE_NUMBER_INT);
            }
            $memberSessionRecordSummary = new $this->memberSessionRecordSummary;
            $packageData = MemberPackage::select('programme_booked')
                ->where('id', $inputs['package_id'])
                ->where('member_id', $inputs['member_id'])
                ->get();
            $lastSummaryRecord = MemberSessionRecordSummary::where('package_id', $inputs['package_id'])
                ->where('member_id', $inputs['member_id'])
                ->orderBy('recorded_date', 'desc')
                ->limit(1)
                ->get();
            if (isset($lastSummaryRecord[0])) {
                $sessionRecords = MemberSessionRecord::where('package_id', $inputs['package_id'])
                    ->where('member_id', $inputs['member_id'])
                    ->where('recorded_date', '>', $lastSummaryRecord[0]->recorded_date)
                    ->orderBy('recorded_date', 'asc')
                    ->get();
            } else {
                $sessionRecords = MemberSessionRecord::where('package_id', $inputs['package_id'])
                    ->where('member_id', $inputs['member_id'])
                    ->orderBy('recorded_date', 'asc')
                    ->limit(3)
                    ->get();
            }
            $weightLoss = 0;
            $weigthGain = 0;
            $beforeWeight = 0;
            $afterWeight = 0;
            //calculations of weight loss and weight gain are done
            if (count($sessionRecords) == 3) {
                $beforeWeight = $sessionRecords[0]->before_weight;
                $afterWeight = $sessionRecords[2]->after_weight;
            } else if (count($sessionRecords) == 2) {
                $beforeWeight = $sessionRecords[0]->before_weight;
                $afterWeight = $sessionRecords[1]->after_weight;
            } else if (count($sessionRecords) == 1) {
                $beforeWeight = $sessionRecords[0]->before_weight;
                $afterWeight = $sessionRecords[0]->after_weight;
            } else {
                throw new Exception('Summary for all previous records is already calculated.', 2001);
            }
            if ($beforeWeight > $afterWeight) {
                $weigthGain = 0;
                $weightLoss = $beforeWeight - $afterWeight;
            } elseif ($beforeWeight < $afterWeight) {
                $weigthGain = $afterWeight - $beforeWeight;
                $weightLoss = 0;
            } else {
                $weigthGain = 0;
                $weightLoss = 0;
            }

            //depending upon from where this function get called
            $balanceProgrammeKg = 0;
            if (isset($lastSummaryRecord[0])) {
                $balanceProgrammeKg = ($lastSummaryRecord[0]->balance_programme_kg > $weightLoss) ? $lastSummaryRecord[0]->balance_programme_kg - $weightLoss : 0;
            } else {
                $balanceProgrammeKg = ($packageData[0]->programme_booked > $weightLoss) ? $packageData[0]->programme_booked - $weightLoss : 0;
            }
            $memberSessionRecordSummary->package_id = $inputs['package_id'];
            $memberSessionRecordSummary->member_id = $inputs['member_id'];
            if ($inputs['session_id'] == $sessionRecords[count($sessionRecords) - 1]->session_id) {
                $memberSessionRecordSummary->session_id = $inputs['session_id'];
                $memberSessionRecordSummary->recorded_date = date('Y-m-d', strtotime($inputs['recorded_date']));
            } else {
                $memberSessionRecordSummary->session_id = $sessionRecords[count($sessionRecords) - 1]->session_id;
                $memberSessionRecordSummary->recorded_date = $sessionRecords[count($sessionRecords) - 1]->recorded_date;
            }
            $memberSessionRecordSummary->net_weight_loss = $weightLoss;
            $memberSessionRecordSummary->net_weight_gain = $weigthGain;
            $memberSessionRecordSummary->balance_programme_kg = $balanceProgrammeKg;
            $memberSessionRecordSummary->created_by = $inputs['created_by'];
            if ($inputs['isForceAction'] == 1) {
                $save = $memberSessionRecordSummary->save();
            } else {
                if (count($sessionRecords) == 3) {
                    $save = $memberSessionRecordSummary->save();
                } else {
                    return true;
                }
                if ($save) {
                    // call fn for escalation matrix
                    if ($weightLoss < 1) {
                        $this->escalateMember($inputs, $weightLoss, $weigthGain);
                        return true;
                    }
                } else {
                    return false;
                }
            }
            if (isset($save)) {
                // call fn for escalation matrix
                if ($save) {
                    if ($weightLoss < 1) {
                        $this->escalateMember($inputs, $weightLoss, $weigthGain);
                    }
                    $response['status'] = 'success';
                    $response['message'] = trans('admin::messages.added', ['name' => trans('admin::controller/cpr.session-records-summary')]);
                } else {
                    $response['status'] = 'error';
                    $response['message'] = trans('admin::messages.not-added', ['name' => trans('admin::controller/cpr.session-records-summary')]);
                }
            }
            return $response;
        } catch (Exception $e) {
            if ($inputs['isForceAction'] != 1) {
                return false;
            }
            $exceptionDetails = $e->getMessage();
            $response['status'] = 'error';
            if ($e->getCode() == 2001) {
                $response['message'] = $exceptionDetails;
            } else {
                $response['message'] = trans('admin::messages.not-added', ['name' => trans('admin::controller/cpr.session-records-summary')]);
            }
            Log::error(trans('admin::messages.not-added', ['name' => trans('admin::controller/cpr.session-records-summary')]), ['Error Message' => $exceptionDetails, 'Current Action' => Route::getCurrentRoute()->getActionName()]);
            return $response;
        }
    }

    public function storeDietaryAssessment($inputs)
    {
        try {
            $memberDietaryAssessment = new $this->memberDietaryAssessment;

            if (isset($inputs['smoking']) && 1 == $inputs['smoking']) {
                $smoking_frequency = $inputs['smoking_frequency'];
            } else {
                $smoking_frequency = '';
            }


            if (isset($inputs['alcohol']) && 1 == $inputs['alcohol']) {
                $alcohol_frequency = $inputs['alcohol_frequency'];
            } else {
                $alcohol_frequency = '';
            }

            $whereClause = [
                'member_id' => $inputs['member_id']
            ];
            $dietaryAssessmentInsertOrUpdate = [
                'food_allergy' => $inputs['food_allergy'],
                'smoking' => isset($inputs['smoking']) ? $inputs['smoking'] : 0,
                'smoking_frequency' => $smoking_frequency,
                'meals_per_day' => $inputs['meals_per_day'],
                'food_habbit' => isset($inputs['food_habbit']) ? $inputs['food_habbit'] : 0,
                'eat_out_per_week' => $inputs['eat_out_per_week'],
                'fasting' => $inputs['fasting'],
                'alcohol' => isset($inputs['alcohol']) ? $inputs['alcohol'] : 0,
                'alcohol_frequency' => $alcohol_frequency,
                'diet_total_calories' => $inputs['diet_total_calories'],
                'diet_cho' => $inputs['diet_cho'],
                'diet_protein' => $inputs['diet_protein'],
                'diet_fat' => $inputs['diet_fat'],
                'remark' => $inputs['remark'],
                'wellness_counsellor_name' => $inputs['wellness_counsellor_name'],
            ];
            $save = $memberDietaryAssessment->updateOrCreate($whereClause, $dietaryAssessmentInsertOrUpdate);

            if (!empty($save)) {
                $response['status'] = 'success';
                $response['message'] = trans('admin::messages.added', ['name' => trans('admin::controller/cpr.dietary-assessment')]);
            } else {
                $response['status'] = 'error';
                $response['message'] = trans('admin::messages.not-added', ['name' => trans('admin::controller/cpr.dietary-assessment')]);
            }
            return $response;
        } catch (Exception $e) {
            $exceptionDetails = $e->getMessage();
            $response['status'] = 'error';
            $response['message'] = trans('admin::messages.not-added', ['name' => trans('admin::controller/cpr.dietary-assessment')]) . "<br /><b> Error Details</b> - " . $exceptionDetails;
            Log::error(trans('admin::messages.not-added', ['name' => trans('admin::controller/cpr.dietary-assessment')]), ['Error Message' => $exceptionDetails, 'Current Action' => Route::getCurrentRoute()->getActionName()]);

            return $response;
        }
    }

    public function storeFitnessAssessment($inputs)
    {
        try {
            $memberFitnessAssessmentt = new $this->memberFitnessAssessment;
            $whereClause = [
                'member_id' => $inputs['member_id']
            ];
            $fitnessAssessmentInsertOrUpdate = [
                'static_posture' => $inputs['static_posture'],
                'sit_and_reach_test' => $inputs['sit_and_reach_test'],
                'shoulder_flexibility_right' => $inputs['shoulder_flexibility_right'],
                'shoulder_flexibility_left' => $inputs['shoulder_flexibility_left'],
                'pulse' => $inputs['pulse'],
                'back_problem_test' => isset($inputs['back_problem_test']) ? $inputs['back_problem_test'] : 0,
                'current_activity_pattern' => $inputs['current_activity_pattern'],
                'current_activity_type' => $inputs['current_activity_type'],
                'current_activity_frequency' => $inputs['current_activity_frequency'],
                'current_activity_duration' => $inputs['current_activity_duration'],
                'remark' => $inputs['remark'],
                'home_care_kit' => isset($inputs['home_care_kit']) ? $inputs['home_care_kit'] : 0,
                'physiotherapist_name' => $inputs['physiotherapist_name'],
            ];
            $save = $memberFitnessAssessmentt->updateOrCreate($whereClause, $fitnessAssessmentInsertOrUpdate);

            if (!empty($save)) {
                $response['status'] = 'success';
                $response['message'] = trans('admin::messages.added', ['name' => trans('admin::controller/cpr.fitness-assessment')]);
            } else {
                $response['status'] = 'error';
                $response['message'] = trans('admin::messages.not-added', ['name' => trans('admin::controller/cpr.fitness-assessment')]);
            }
            return $response;
        } catch (Exception $e) {
            $exceptionDetails = $e->getMessage();
            $response['status'] = 'error';
            $response['message'] = trans('admin::messages.not-added', ['name' => trans('admin::controller/cpr.fitness-assessment')]) . "<br /><b> Error Details</b> - " . $exceptionDetails;
            Log::error(trans('admin::messages.not-added', ['name' => trans('admin::controller/cpr.fitness-assessment')]), ['Error Message' => $exceptionDetails, 'Current Action' => Route::getCurrentRoute()->getActionName()]);

            return $response;
        }
    }

    public function storeMedicalAssessment($inputs)
    {
        try {
            if (isset($inputs['current_associated_medical_problem'])) {
                $medical_problem = implode(",", $inputs['current_associated_medical_problem']);
            } else {
                $medical_problem = '';
            }
            $memberMedicalAssessment = new $this->memberMedicalAssessment;
            $whereClause = [
                'member_id' => $inputs['member_id']
            ];
            $medicalAssessmentInsertOrUpdate = [
                'current_associated_medical_problem' => $medical_problem,
                'epilepsy' => isset($inputs['epilepsy']) ? $inputs['epilepsy'] : '',
                'other' => isset($inputs['other']) ? $inputs['other'] : '',
                'physical_finding' => isset($inputs['physical_finding']) ? $inputs['physical_finding'] : '',
                'systemic_examination' => isset($inputs['systemic_examination']) ? $inputs['systemic_examination'] : '',
                'gynae_obstetrics_history' => isset($inputs['gynae_obstetrics_history']) ? $inputs['gynae_obstetrics_history'] : '',
                'clients_birth_weight' => isset($inputs['clients_birth_weight']) ? $inputs['clients_birth_weight'] : '',
                'sleeping_pattern' => isset($inputs['sleeping_pattern']) ? $inputs['sleeping_pattern'] : '',
                'past_mediacl_history' => isset($inputs['past_mediacl_history']) ? $inputs['past_mediacl_history'] : '',
                'family_history_of_diabetes_obesity' => isset($inputs['family_history_of_diabetes_obesity']) ? $inputs['family_history_of_diabetes_obesity'] : '',
                'detailed_history' => isset($inputs['detailed_history']) ? $inputs['detailed_history'] : '',
                'treatment_history' => isset($inputs['treatment_history']) ? $inputs['treatment_history'] : '',
                'suggested_investigation' => isset($inputs['suggested_investigation']) ? $inputs['suggested_investigation'] : '',
                'followup_date' => (isset($inputs['followup_date']) && $inputs['followup_date'] != '') ? $inputs['followup_date'] : '',
                'doctors_name' => isset($inputs['doctors_name']) ? $inputs['doctors_name'] : '',
                'assessment_date' => (isset($inputs['assessment_date']) && $inputs['assessment_date'] != '') ? date('Y-m-d', strtotime($inputs['assessment_date'])) : '',
            ];

            $save = $memberMedicalAssessment->updateOrCreate($whereClause, $medicalAssessmentInsertOrUpdate);
            $memberBiochemicalProfile = new $this->memberBiochemicalProfile;

            if ($inputs['test_id'] != '' || $inputs['test_id'] != null) {
                DB::table('member_biochemical_profile')->where('member_id', $inputs['member_id'])->delete();
                foreach ($inputs['test_id'] as $k => $v) {
                    if ($inputs['initial_' . $v] != '' || $inputs['final_' . $v] != '') {
                        $biochemical = DB::table('member_biochemical_profile')->insert(
                            ['member_id' => $inputs['member_id'], 'biochemical_condition_test_id' => $v, 'initial' => $inputs['initial_' . $v], 'final' => $inputs['final_' . $v]]
                        );
                    }
                }
            } else {
                DB::table('member_biochemical_profile')->where('member_id', $inputs['member_id'])->delete();
            }

            if (!empty($save) || !empty($biochemical)) {
                $response['status'] = 'success';
                $response['message'] = trans('admin::messages.added', ['name' => trans('admin::controller/cpr.medical-assessment')]);
            } else {
                $response['status'] = 'error';
                $response['message'] = trans('admin::messages.not-added', ['name' => trans('admin::controller/cpr.medical-assessment')]);
            }
            return $response;
        } catch (Exception $e) {
            $exceptionDetails = $e->getMessage();
            $response['status'] = 'error';
            $response['message'] = trans('admin::messages.not-added', ['name' => trans('admin::controller/cpr.medical-assessment')]) . "<br /><b> Error Details</b> - " . $exceptionDetails;
            Log::error(trans('admin::messages.not-added', ['name' => trans('admin::controller/cpr.medical-assessment')]), ['Error Message' => $exceptionDetails, 'Current Action' => Route::getCurrentRoute()->getActionName()]);

            return $response;
        }
    }

    // Function to Send Notification to Area Technical Head when weight loss is less than 0 kg
    public function escalateMember($inputs, $weightLoss, $weigthGain)
    {
        $member_id = $inputs["member_id"];
        $session_id = $inputs["session_id"];

        $center_users = DB::select("SELECT members.id as member_id, members.crm_center_id, members.mobile_number, v_centers.id as        center_id, a_centers.user_id as center_user_id FROM members members
        LEFT OUTER JOIN vlcc_centers v_centers ON members.crm_center_id = v_centers.crm_center_id
        LEFT OUTER JOIN admin_centers a_centers ON v_centers.id = a_centers.center_id
        WHERE members.id = " . $member_id . "");

        $center_users = json_decode(json_encode($center_users), true);
        $users = [];
        foreach ($center_users as $key => $value) {
            if (!empty($center_users[$key]["center_user_id"])) {
                array_push($users, $center_users[$key]["center_user_id"]);
            }
        }

        if (!empty($users)) {
            $tags = "'" . implode("', '", $users) . "'";
            $ath_users = DB::select("SELECT id as ath_user_id FROM admins WHERE id IN(" . $tags . ") AND user_type_id='9';");
            $ath_users = json_decode(json_encode($ath_users), true);
            $insertArray = [];
            $insertEscalationMatrixData = [];

            foreach ($ath_users as $key => $value) {
                if (!empty($ath_users[$key]["ath_user_id"])) {
                    $notifications = [];
                    $escalation_matrix = [];
                    
                    // Commenting below line as function will be called from cron & Auth::guard object is not available 
                    
                    //$notifications = array("admin_id" => $ath_users[$key]["ath_user_id"], "notification_text" => "Customer has been escalated for unsuccessful weight loss.", "deep_linking" => "escalation-matrix", "notification_date" => date('Y-m-d H:i:s'), "notification_type" => 3, "read_status" => 0, "created_by" => Auth::guard('admin')->user()->id, "updated_by" => Auth::guard('admin')->user()->id, "created_at" => date('Y-m-d H:i:s'));
                    
                    $notifications = array("admin_id" => $ath_users[$key]["ath_user_id"], "notification_text" => "Customer has been escalated for unsuccessful weight loss.", "deep_linking" => "escalation-matrix", "notification_date" => date('Y-m-d H:i:s'), "notification_type" => 3, "read_status" => 0, "created_by" => $inputs["created_by"], "updated_by" => $inputs["created_by"], "created_at" => date('Y-m-d H:i:s'));

                    $escalation_matrix = array("admin_id" => $ath_users[$key]["ath_user_id"], "session_id" => $inputs["session_id"], "member_id" => $inputs["member_id"], "package_id" => $inputs["package_id"], "weight_loss" => $weightLoss, "weight_gain" => $weigthGain, "escalation_date" => date('Y-m-d'), "escalation_status" => "1", "created_at" => date('Y-m-d H:i:s'));

                    $insertArray[] = $notifications;
                    $insertEscalationMatrixData[] = $escalation_matrix;
                }
            }

            // Insert into admin_notifications database table
            if (!empty($insertArray)) {
                DB::table("admin_notifications")->insert($insertArray);
                DB::table("member_escalation_matrix")->insert($insertEscalationMatrixData);
            }
        }
    }

    public function storeSkinHairAnalysis($inputs)
    {

        try {

            $memberSkinHairAnalysis = new $this->memberSkinHairAnalysis;
            $whereClause = [
                'member_id' => $inputs['member_id']
            ];
            $skinHairAnalysisInsertOrUpdate = [
                'skin_type' => isset($inputs['skin_type']) ? implode(",", $inputs['skin_type']) : '',
                'skin_condition' => isset($inputs['skin_condition']) ? implode(",", $inputs['skin_condition']) : '',
                'hyperpigmentation_type' => isset($inputs['hyperpigmentation_type']) ? $inputs['hyperpigmentation_type'] : '',
                'hyperpigmentation_size' => isset($inputs['hyperpigmentation_size']) ? $inputs['hyperpigmentation_size'] : '',
                'hyperpigmentation_depth' => isset($inputs['hyperpigmentation_depth']) ? $inputs['hyperpigmentation_depth'] : '',
                'scars_depth' => isset($inputs['scars_depth']) ? $inputs['scars_depth'] : '',
                'scars_size' => isset($inputs['scars_size']) ? $inputs['scars_size'] : '',
                'scars_pigmented' => isset($inputs['scars_pigmented']) ? $inputs['scars_pigmented'] : '',
                'fine_lines_and_wrinkles' => isset($inputs['fine_lines_and_wrinkles']) ? $inputs['fine_lines_and_wrinkles'] : '',
                'skin_curvature' => isset($inputs['skin_curvature']) ? $inputs['skin_curvature'] : '',
                'other_marks' => isset($inputs['other_marks']) ? implode(",", $inputs['other_marks']) : '',
                'hair_type' => isset($inputs['hair_type']) ? implode(",", $inputs['hair_type']) : '',
                'condition_of_scalp' => isset($inputs['condition_of_scalp']) ? implode(",", $inputs['condition_of_scalp']) : '',
                'hair_density' => isset($inputs['hair_density']) ? $inputs['hair_density'] : '',
                'condition_of_hair_shaft' => isset($inputs['condition_of_hair_shaft']) ? implode(",", $inputs['condition_of_hair_shaft']) : '',
                'history_of_allergy' => isset($inputs['history_of_allergy']) ? $inputs['history_of_allergy'] : '',
                'conclusion' => isset($inputs['conclusion']) ? $inputs['conclusion'] : '',
                'skin_and_hair_specialist_name' => isset($inputs['skin_and_hair_specialist_name']) ? $inputs['skin_and_hair_specialist_name'] : '',
                'analysis_date' => isset($inputs['analysis_date']) ? $inputs['analysis_date'] : ''
            ];
            $save = $memberSkinHairAnalysis->updateOrCreate($whereClause, $skinHairAnalysisInsertOrUpdate);
            if (isset($save)) {
                if ($save) {
                    $response['status'] = 'success';
                    $response['message'] = trans('admin::messages.added', ['name' => trans('admin::controller/cpr.skin-hair-analysis')]);
                } else {
                    $response['status'] = 'error';
                    $response['message'] = trans('admin::messages.not-added', ['name' => trans('admin::controller/cpr.skin-hair-analysis')]);
                }
            } else {
                $response['status'] = 'error';
                $response['message'] = "Skin & Hair Analysis Form can not be empty";
            }

            return $response;
        } catch (Exception $e) {
            $exceptionDetails = $e->getMessage();
            $response['status'] = 'error';
            $response['message'] = trans('admin::messages.not-added', ['name' => trans('admin::controller/cpr.skin-hair-analysis')]) . "<br /><b> Error Details</b> - " . $exceptionDetails;
            Log::error(trans('admin::messages.not-added', ['name' => trans('admin::controller/cpr.skin-hair-analysis')]), ['Error Message' => $exceptionDetails, 'Current Action' => Route::getCurrentRoute()->getActionName()]);

            return $response;
        }
    }

    public function storeReviewFitnessActivity($inputs)
    {
        try {
            $memberActivityFitnessReview = new $this->memberActivityFitnessReview;

            $allColumns = $memberActivityFitnessReview->getTableColumns($memberActivityFitnessReview->getTable());

            foreach ($inputs as $key => $value) {
                if (in_array($key, $allColumns)) {
                    $memberActivityFitnessReview->$key = $value;
                }
            }
            $memberActivityFitnessReview->review_date = date('Y-m-d', strtotime($inputs['review_date']));
            $save = $memberActivityFitnessReview->save();

            if ($save) {
                $response['status'] = 'success';
                $response['message'] = trans('admin::messages.added', ['name' => trans('admin::controller/cpr.review')]);
            } else {
                $response['status'] = 'error';
                $response['message'] = trans('admin::messages.not-added', ['name' => trans('admin::controller/cpr.review')]);
            }

            return $response;
        } catch (Exception $e) {
            $exceptionDetails = $e->getMessage();
            $response['status'] = 'error';
            $response['message'] = trans('admin::messages.not-added', ['name' => trans('admin::controller/cpr.review')]) . "<br /><b> Error Details</b> - " . $exceptionDetails;
            Log::error(trans('admin::messages.not-added', ['name' => trans('admin::controller/cpr.review')]), ['Error Message' => $exceptionDetails, 'Current Action' => Route::getCurrentRoute()->getActionName()]);

            return $response;
        }
    }

    /**     * Function to get Latest completed session id of member ** */
    public function getLatestSessionId($memberId)
    {
        $sessionId = DB::table('member_session_bookings')
                ->where('member_id', $memberId)
                ->where('status', 5)
                ->orderBy('id', 'DESC')->first();

        if (isset($sessionId->id))
            return $sessionId->id;
        else
            return 0;
    }

    public function storeMemberMeasurementRecord($inputs)
    {
        try {

            $sessionDateSelected = $this->sessionBookingsRepository->getSessionData($inputs['date']);
            $sessionDateSelected1 = $sessionDateSelected['session_date'];

            foreach ($inputs['value'] as $typeKey => $typeValue) {

                $memberMeasurementRecordss = new $this->memberMeasurementRecords;

                for ($i = 1; $i <= count($typeValue); $i++) {
                    if ($typeValue[$i] != '' || $typeValue[$i] != 0) {

                        $sessionDateSelected = $this->sessionBookingsRepository->getSessionData($inputs['date']);
                        $sessionDateSelected1 = $sessionDateSelected['session_date'];
                        $memberMeasurementRecordss = new $this->memberMeasurementRecords;
                        $whereClause = [
                            'member_id' => $inputs['member_id'],
                            'session_id' => $inputs['date'],
                            'type' => $typeKey,
                            'sub_type' => $i];

                        $memberMeasurementRecordInsertOrUpdate = [
                            'member_id' => $inputs['member_id'],
                            'session_id' => $inputs['date'],
                            'date' => $sessionDateSelected1,
                            'type' => $typeKey,
                            'sub_type' => $i,
                            'value' => $typeValue[$i],
                            'created_by' => Auth::guard('admin')->user()->id];

                        $save = $memberMeasurementRecordss->updateOrCreate($whereClause, $memberMeasurementRecordInsertOrUpdate);
                    }
                }
            }//foreach

            if (isset($save)) {
                if ($save) {
                    $response['status'] = 'success';
                    $response['message'] = trans('admin::messages.added', ['name' => trans('admin::controller/cpr.measurements-records')]);
                } else {
                    $response['status'] = 'error';
                    $response['message'] = trans('admin::messages.not-added', ['name' => trans('admin::controller/cpr.measurements-records')]);
                }
            } else {
                $response['status'] = 'empty-error';
                $response['message'] = "Measurement Record Form can not be empty";
            }

            return $response;
        } catch (Exception $e) {
            $exceptionDetails = $e->getMessage();
            $response['status'] = 'error';
            $response['message'] = trans('admin::messages.not-added', ['name' => trans('admin::controller/cpr.measurements-records')]) . "<br /><b> Error Details</b> - " . $exceptionDetails;
            Log::error(trans('admin::messages.not-added', ['name' => trans('admin::controller/cpr.measurements-records')]), ['Error Message' => $exceptionDetails, 'Current Action' => Route::getCurrentRoute()->getActionName()]);

            return $response;
        }
    }

    public function storeMedicalReview($inputs)
    {
        try {
            $memberMedicalReview = new $this->memberMedicalReview;
            $allColumns = $memberMedicalReview->getTableColumns($memberMedicalReview->getTable());
            foreach ($inputs as $key => $value) {
                if (in_array($key, $allColumns)) {
                    $memberMedicalReview->$key = $value;
                }
            }
            $memberMedicalReview->date = date('Y-m-d');
            $save = $memberMedicalReview->save();

            $strlen = strlen($inputs['advice']);
            if ($strlen > 100) {
                $message = strpos($inputs['advice'], ' ', 100);
                $message_text = substr($inputs['advice'], 0, $message);
            } else {
                $message_text = $inputs['advice'];
            }


            $params = array("member_id" => $inputs['member_id'], "message_type" => 6, "message_text" => $message_text, "deep_link_screen" => $memberMedicalReview->id, "created_by" => 3);

            $recommendationRepository = new $this->recommendationRepository($this->recommendation);
            $response = $recommendationRepository->create($params);

            if ($save) {
                $response['status'] = 'success';
                $response['message'] = trans('admin::messages.added', ['name' => trans('admin::controller/cpr.medical-review')]);
            } else {
                $response['status'] = 'error';
                $response['message'] = trans('admin::messages.not-added', ['name' => trans('admin::controller/cpr.medical-review')]);
            }

            return $response;
        } catch (Exception $e) {
            $exceptionDetails = $e->getMessage();
            $response['status'] = 'error';
            $response['message'] = trans('admin::messages.not-added', ['name' => trans('admin::controller/cpr.medical-review')]) . "<br /><b> Error Details</b> - " . $exceptionDetails;
            Log::error(trans('admin::messages.not-added', ['name' => trans('admin::controller/cpr.medical-review')]), ['Error Message' => $exceptionDetails, 'Current Action' => Route::getCurrentRoute()->getActionName()]);

            return $response;
        }
    }

    public function getMemberCenter($memberId)
    {
        DB::setFetchMode(PDO::FETCH_ASSOC);
        $member_center = DB::select("SELECT center.id as center_id FROM members member LEFT OUTER JOIN vlcc_centers center ON member.crm_center_id=center.crm_center_id WHERE member.id=" . $memberId);
        DB::setFetchMode(PDO::FETCH_CLASS);
        $center_id = (isset($member_center[0]["center_id"])) ? $member_center[0]["center_id"] : 0;
        return $center_id;
    }

    public function getPackageId($memberId)
    {
        $result = Member::with(['memberPackageOne'])->where('id', $memberId)->first();
        $package_id = isset($result->toArray()['member_package_one']) ? $result->toArray()['member_package_one']['id'] : 0;
        return $package_id;
    }

    public function getMemberMobileNumber($memberId)
    {
        DB::setFetchMode(PDO::FETCH_ASSOC);
        $member_mobile = DB::select("SELECT mobile_number FROM members WHERE id=" . $memberId);
        DB::setFetchMode(PDO::FETCH_CLASS);
        $mobile_no = (isset($member_mobile[0]["mobile_number"])) ? $member_mobile[0]["mobile_number"] : 0;
        return $mobile_no;
    }

    // This function is used to generate OTP
    public function generateOtp()
    {
        $digit = 4;
        $otpNumber = rand(pow(10, $digit - 1), pow(10, $digit) - 1);
        return $otpNumber;
    }

    public function sendOtp($to, $params = [])
    {
        // Curl Call Code to Send OTP to registerd mobile number
        try {
            $messageText = $params["message_text"];
            $url = Config::get('admin.send_message_url');
            $time = date('YmdHi');
            $url = $url . "?feedid=" . Config::get('admin.feed_id') . "&username=" . Config::get('admin.user_name') . "&password=" . Config::get('admin.password') . "&To=" . $params["mobile_number"] . "&Text=" . $messageText . "&time=" . $time . "&senderid=" . Config::get('admin.sender_id');
            $client = new Client(); //GuzzleHttp\Client
            $response = $client->request('GET', $url);
            return true;
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    public function saveOtp($inputs)
    {
        $otp_id = 0;
        $member_otp = new $this->memberOtp;
        $allColumns = $member_otp->getTableColumns($member_otp->getTable());
        foreach ($inputs as $key => $value) {
            if (in_array($key, $allColumns)) {
                $member_otp->$key = $value;
            }
        }
        $save = $member_otp->save();
        if ($save) {
            $otp_id = $member_otp->id;
        }
        return $otp_id;
    }

    public function getOtpSendTime($params)
    {
        DB::connection()->getPdo()->setAttribute(PDO::ATTR_EMULATE_PREPARES, true);
        $response = DB::select("CALL getOtpSendTime(?, ?)", array($params['otp'], $params['otp_id']));
        DB::connection()->getPdo()->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
        $result = (isset($response[0]->created_at) && !empty($response[0]->created_at)) ? $response[0]->created_at : 0;
        return $result;
    }

    public function validateOtp($params)
    {
        DB::connection()->getPdo()->setAttribute(PDO::ATTR_EMULATE_PREPARES, true);
        $response = DB::select("CALL validateOtp(?, ?, ?)", array($params['mobile_number'], $params['otp'], $params['otp_id']));
        DB::connection()->getPdo()->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
        $response = json_decode(json_encode($response[0]), false);
        $result = $response->affected_rows;
        return $result;
    }

    public function updateSessionOtpFlag($params)
    {
        $member_session_record = new $this->memberSessionRecord;
        $memberData = ['otp_verified' => 1];
        $save = $member_session_record->where('id', $params['session_programme_id'])->update($memberData);
    }

    // Function to get Service area comma separated specification flag
    public function getServiceAreaSpecification($service_id)
    {
        $result = DB::table('member_package_services')->select('area_specification')->whereIn('id', explode(",", $service_id))->get();
        $result = json_decode(json_encode($result), true);
        $result = array_column($result, 'area_specification');
        $unique_area_specifications = [];
        foreach ($result as $value) {
            if (!empty($value)) {
                $array = explode(",", $value);
                $unique_area_specifications = array_merge($unique_area_specifications, $array);
            }
        }
        return array_unique($unique_area_specifications);
    }

    // Function to check if Body Measurement fileds are added for particular session or not
    public function checkWholeBodyFields($member_id, $session_id)
    {
        $result = DB::table('member_measurement_details')->select('id', 'neck', 'chest', 'arms', 'arm_right', 'tummy', 'waist', 'hips', 'thighs', 'thighs_right', 'total_cm_loss', 'therapist_name')->where('member_id', $member_id)->where('session_id', $session_id)->get();
        $response["count"] = count($result);
        $response["result"] = $result;
        return $response;
    }

    // Function to check if Spot Reduction Measurement fields are added or not
    public function checkSpotReductionFields($member_id, $session_id, $measurement_record_fields)
    {
        $specification_type = array_column($measurement_record_fields, 'type_id')[0];
        $specification_sub_type = array_keys($measurement_record_fields);
        $result = DB::table('member_measurement_records')->select('id', 'type', 'sub_type', 'value')->where('member_id', $member_id)->where('session_id', $session_id)->where('type', (int) $specification_type)->get();
        $flag = (count($specification_sub_type) == count($result)) ? true : false;
        $response["flag"] = $flag;
        $response["result"] = $result;
        return $response;
    }

    // Function to get sessionid & session date for booked & completed sessions & those record not inserted in member_measurement_details table(Body Measurements)
    public function getSessionInfo($memberId)
    {
        $logged_in_user_id = Auth::guard('admin')->user()->id;
//        $response = DB::select("SELECT id, session_date FROM `member_session_bookings` WHERE `member_session_bookings`.`member_id` = '$memberId' AND `member_session_bookings`.status IN(2,5)AND id NOT IN(Select session_id from member_measurement_details where member_id = '$memberId' AND session_id IS NOT NULL)");
        $response = DB::select("SELECT id, date_format(session_date,'%d-%b-%Y') as session_date FROM `member_session_bookings` WHERE `member_session_bookings`.`member_id` = '$memberId' AND `member_session_bookings`.status IN(5,7) AND id NOT IN(Select session_id from member_measurement_details where member_id = '$memberId' AND session_id IS NOT NULL) ORDER BY session_date DESC");
        $arrSessionData = collect($response)->toArray();
        $arrSessionData1 = array_column($arrSessionData, 'session_date', 'id');
       // dd($arrSessionData1);
        return $arrSessionData1;
    }

    // Function to get sessionid & session date for booked & completed sessions & those record not inserted in member_measurement_records table(Spot Reduction Measurements)
    public function getSessionInfoSpot($memberId)
    {
        $logged_in_user_id = Auth::guard('admin')->user()->id;
        // $response=DB::select("SELECT id, session_date FROM `member_session_bookings` WHERE `member_session_bookings`.`member_id` = '$memberId' AND `member_session_bookings`.status IN(2,5) AND id NOT IN(Select session_id from member_measurement_records where member_id = '$memberId' AND session_id IS NOT NULL)");
        $response = DB::select("SELECT id, date_format(session_date,'%d-%b-%Y') as session_date FROM `member_session_bookings` WHERE `member_session_bookings`.`member_id` = '$memberId' AND `member_session_bookings`.status IN(5,7) ORDER BY session_date DESC");
        $arrSessionData = collect($response)->toArray();
        $arrSessionData1 = array_column($arrSessionData, 'session_date', 'id');
        return $arrSessionData1;
    }

    // Function to get list of Therapists
    public function getTherapistList($memberId)
    {
        $result = DB::select("SELECT admins.id, CONCAT_WS(' ', admins.first_name, admins.last_name, '-', admins.username) AS username FROM members LEFT OUTER JOIN vlcc_centers ON members.crm_center_id = vlcc_centers.crm_center_id LEFT OUTER JOIN admin_centers ON vlcc_centers.id = admin_centers.center_id LEFT OUTER JOIN admins ON admin_centers.user_id = admins.id WHERE members.id=" . $memberId . " AND (admins.user_type_id=5 OR admins.user_type_id=9 OR admins.user_type_id=10) AND admins.status=1");
        $result = collect($result)->toArray();
        $result = array_column($result, 'username', 'id');
        return $result;
    }

    // Function to get Therapist username
    public function getTherapistName($user_id)
    {
        return User::select('username', 'first_name', 'last_name')->where('id', $user_id)->first();
    }

    // Function to get Service data
    public function getServiceData($service_id)
    {
        $result = DB::select("SELECT crm_service_guid, area_specification, service_category, service_code FROM member_package_services WHERE id=" . $service_id);
        $result = json_decode(json_encode($result), true);
        return $result[0];
    }

    // Function to create specification json string
    public function createSpecificationJson($specifications, $member_id, $session_id, $service_execution_params, $measurement_record_fields)
    {
        foreach ($specifications as $specification) {
            if ($specification == "Whole Body") {
                $result = $this->checkWholeBodyFields($member_id, $session_id)["result"];
                $result = json_decode(json_encode($result), true)[0];
                $whole_body["ArmLeft"] = $result["arms"];
                $whole_body["ArmsRight"] = $result["arm_right"];
                $whole_body["Chest"] = $result["chest"];
                $whole_body["Hip"] = $result["hips"];
                $whole_body["Neck"] = $result["neck"];
                $whole_body["ThighLeft"] = $result["thighs"];
                $whole_body["ThighRight"] = $result["thighs_right"];
                $whole_body["Tummy"] = $result["tummy"];
                $whole_body["Waist"] = $result["waist"];
                $whole_body["whr"] = $result["arms"];
                $service_execution_params["Whole Body"] = $whole_body;
            } else {
                $measurement_records = $measurement_record_fields[$specification];
                $check_spot_reduction_measurements = $this->checkSpotReductionFields($member_id, $session_id, $measurement_records)["result"];
                $check_spot_reduction_measurements = json_decode(json_encode($check_spot_reduction_measurements), true);
                if ($specification == "Arm") {
                    $arm["Arm5CmAboveMuac"] = $this->iterateArray($check_spot_reduction_measurements, 2); // sub_type 2
                    $arm["Arm5CmbelowMuac"] = $this->iterateArray($check_spot_reduction_measurements, 3); // sub_type 3
                    $arm["ArmMuacSite"] = $this->iterateArray($check_spot_reduction_measurements, 1);  // sub_type 1
                    $service_execution_params["Arm"] = $arm;
                } else if ($specification == "Back") {
                    $back["Back10CmBelowBothArmPit"] = $this->iterateArray($check_spot_reduction_measurements, 4); // sub_type 4
                    $back["BackRightArmpitToLeftArmPitbck"] = $this->iterateArray($check_spot_reduction_measurements, 3); // sub_type 3
                    $back["BackTipOfTheLeftAcromionToTheRightArmPit"] = $this->iterateArray($check_spot_reduction_measurements, 2);  // sub_type 2
                    $back["BackTipOfTheRightAcromionToTheLeftArmPit"] = $this->iterateArray($check_spot_reduction_measurements, 1);  // sub_type 1
                    $service_execution_params["Back"] = $back;
                } else if ($specification == "Chest") {
                    $chest["Chest10CmBelowArmPit"] = $this->iterateArray($check_spot_reduction_measurements, 2); // sub_type 2
                    $chest["ChestRightArmPitToLeftArmPit"] = $this->iterateArray($check_spot_reduction_measurements, 1); // sub_type 1
                    $service_execution_params["Chest"] = $chest;
                } else if ($specification == "Hip") {
                    $hip["HipHighestPointOfInnerSideOfThigh"] = $this->iterateArray($check_spot_reduction_measurements, 2); // sub_type 2
                    $hip["HipMaximumExtensionOfHipsinStandingPos"] = $this->iterateArray($check_spot_reduction_measurements, 1); // sub_type 1
                    $service_execution_params["Hip"] = $hip;
                } else if ($specification == "Sides") {
                    $sides["Sides5CmAboveIliaccrest"] = $this->iterateArray($check_spot_reduction_measurements, 2); // sub_type 2
                    $sides["Sides7CmAboveIliaccrest"] = $this->iterateArray($check_spot_reduction_measurements, 3); // sub_type 3
                    $sides["SidesHighestPointOfIiliaccrest"] = $this->iterateArray($check_spot_reduction_measurements, 1); // sub_type 1
                    $service_execution_params["Sides"] = $sides;
                } else if ($specification == "Thigh") {
                    $thigh["Thigh5CmAbove"] = $this->iterateArray($check_spot_reduction_measurements, 2); // sub_type 2
                    $thigh["Thigh5CmBelowMidthighCircumference"] = $this->iterateArray($check_spot_reduction_measurements, 3); // sub_type 3
                    $thigh["ThighMidthighCircumferenceOfOnlyRightThigh"] = $this->iterateArray($check_spot_reduction_measurements, 1); // sub_type 1
                    $service_execution_params["Thigh"] = $thigh;
                } else if ($specification == "Tummy") {
                    $tummy["Tummy5CmAboveNavelPoint"] = $this->iterateArray($check_spot_reduction_measurements, 3); // sub_type 3
                    $tummy["TummyHighestPointOfIliaccrest"] = $this->iterateArray($check_spot_reduction_measurements, 1); // sub_type 1
                    $tummy["TummynavelPoint"] = $this->iterateArray($check_spot_reduction_measurements, 2); // sub_type 2
                    $service_execution_params["Tummy"] = $tummy;
                }
            }
        }
        return $service_execution_params;
    }

    // Function to iterate array values
    public function iterateArray($check_spot_reduction_measurements, $search)
    {
        $key = array_filter($check_spot_reduction_measurements, function($v, $k) use ($search) {
            if ($v["sub_type"] == $search) {
                return true;
            } else {
                return false;
            }
        }, ARRAY_FILTER_USE_BOTH);
        //return array_column($key, 'value')[0];
        return current($key)['value'];
    }

    // Function to update member_session_record id in database
    public function updateServiceExecutedFlag($member_session_record_id, $flag = 0)
    {
        $save = [];
        $member_session_record = new $this->memberSessionRecord;
        if ($flag == 0) {
            $memberData = ['service_executed' => 1];
            $save = $member_session_record->where('id', $member_session_record_id)->where('service_executed', '=', 0)->update($memberData);
        } else if ($flag == 2) {
            $memberData = ['service_executed' => 1];
            $save = $member_session_record->where('id', $member_session_record_id)->update($memberData);
        } else {
            $session_record_data = $this->getMemberSessionRecordData($member_session_record_id);
            if (!empty($session_record_data->service_execution_status)) {
                $service_execution_status = json_decode($session_record_data->service_execution_status, true);
                $service_status_code = array_column($service_execution_status, "code");
                foreach ($service_status_code as $service_status_code_key) {
                    if ($service_status_code_key == 200) {
                        $status = 3;
                    } else {
                        $status = 2;
                        break;
                    }
                }
                $memberData = ['service_executed' => $status];
                $save = $member_session_record->where('id', $member_session_record_id)->update($memberData);
            }
        }
        if ($save) {
            return 1;
        } else {
            return 0;
        }
    }

    public function getMemberSessionRecordData($id)
    {
        return MemberSessionRecord::where('id', $id)->first();
    }

    public function getMemberSessionData($id)
    {
        return SessionBookings::where('id', $id)->first();
    }

    //Function to get CLM Service execution response message
    public function getServiceExecutionResponse($session_id, $session_programme_record_id, $member_id)
    {
        DB::setFetchMode(PDO::FETCH_ASSOC);
        $response = DB::select("SELECT S.service_id, A.username FROM member_session_bookings S INNER JOIN admins A ON S.dietician_id = A.id WHERE S.id=" . $session_id);
        DB::setFetchMode(PDO::FETCH_CLASS);
        $result = $response[0];
        $service_ids = explode(",", $result["service_id"]);
        $error_message = "";
        foreach ($service_ids as $value) {
            $file_name = $result["username"] . "-" . $member_id . "-" . $session_id . "-" . $value . "-" . $session_programme_record_id;
            $file_name = "/var/www/html/vlcc-admin/storage/logs/service_execution_response/" . $file_name . ".log";
            if (file_exists($file_name)) {
                $str = file_get_contents($file_name);
                $array = json_decode($str, true);
                $error_message .= isset($array["Header"]) ? " " . $array["Header"] : "Something went wrong.";
            }
        }
        return ltrim($error_message);
    }
}
