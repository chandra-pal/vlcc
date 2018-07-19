<?php
/**
 * The repository class for managing session bookings specific actions.
 *
 *
 * @author Sachin Gend <saching@iprogrammer.com>
 * @package Admin
 * @since 1.0
 */

namespace Modules\Admin\Repositories;

use GuzzleHttp\Client;
use Modules\Admin\Models\Member;
use Modules\Admin\Models\SessionBookings;
use Modules\Admin\Models\MemberPackage;
use Modules\Admin\Models\Recommendation;
use Modules\Admin\Models\MemberDeviceToken;
use Modules\Admin\Models\MemberPackageServices;
use Modules\Admin\Models\BeautyServices;
use Modules\Admin\Services\Helper\PushHelper;
use Exception;
use Route;
use Log;
use Cache;
use Auth;
use Illuminate\Support\Facades\DB;
use PDO;
use Modules\Admin\Models\AdminCenter;
use Modules\Admin\Models\User;
use Modules\Admin\Models\StaffAvailability;
use Carbon\Carbon;
use Modules\Admin\Models\MachineAvailability;
use Modules\Admin\Models\MemberSessionBookingResources;
use Modules\Admin\Models\RoomAvailability;
use Modules\Admin\Models\Machine;
use Modules\Admin\Models\Room;
use Config;

class SessionBookingsRepository extends BaseRepository
{

    /**
     * Create a new SessionBookingsRepository instance.
     *
     * @param  Modules\Admin\Models\SessionBookings $model
     * @return void
     */
    public function __construct(SessionBookings $sessionBookings, Recommendation $recommendation, MemberSessionBookingResources $memberSessionBookingResources)
    {
        $this->model = $sessionBookings;
        $this->recommendation_model = $recommendation;
        $this->memberSessionBookingResources = $memberSessionBookingResources;
    }

    /**
     * Get a listing of the resource.
     *
     * @return Response
     */
//    public function data($params = [])
//    {
//        $response = '';
//        if (isset($params['session_date'])) {
//            if ($params['user_type_id'] == 4 || $params['user_type_id'] == 8) { //Dietician, Slimming Head
//                $response = SessionBookings::with('MemberPackage', 'Member')
//                    ->where('dietician_id', $params['user_id'])
//                    ->where('session_date', $params['session_date'])
//                    ->orderBy('session_date', 'DESC')
//                    ->orderBy('start_time', 'ASC')
//                    ->get();
//            } elseif ($params['user_type_id'] == 6 || $params['user_type_id'] == 9 || $params['user_type_id'] == 5 || $params['user_type_id'] == 7 || $params['user_type_id'] == 11) { // Physiotherapist, Center Head, Doctors, ATH and Center Admin
//                $centerIds = DB::select("SELECT group_concat(center_id) as center_id FROM admin_centers WHERE user_id = " . $params['user_id'] . "");
//                if (isset($centerIds[0]) && isset($centerIds[0]->center_id) && $centerIds[0]->center_id != '') {
//                    $response = SessionBookings::with('MemberPackage', 'Member')
//                        ->where('session_date', $params['session_date'])
//                        ->whereRaw("FIND_IN_SET(dietician_id,(select group_concat(user_id) FROM admin_centers WHERE center_id IN( " . $centerIds[0]->center_id . ")))")
//                        ->get();
//                } else {
//                    $response = collect();
//                }
//            }
//        } else {
//            if (isset($params["logged_in_user_type_id"]) && $params["logged_in_user_type_id"] == 9) {
//                $response = SessionBookings::with('Member')->where('member_id', $params['customer_id'])->get();
//            } else {
//                if (isset($params["customer_id"]) && !empty($params["customer_id"])) {
//                    $response = SessionBookings::with('Member')
//                        ->where('dietician_id', $params['dietician_id'])
//                        ->where('member_id', $params['customer_id'])
//                        ->get();
//                } else {
//                    $response = SessionBookings::with('Member')->where('dietician_id', $params['dietician_id'])
//                        ->get();
//                }
//            }
//        }
//        return $response;
//    }


    public function data($params = []) {

                if(isset($params['center_id']) && !empty($params['center_id'])){
                    $centerQuery = " AND VC.id = '" . $params['center_id'] . "' ";
                }else{
                    $centerQuery = "";
                }

                
//                if(isset($params['customer_gender']) && !empty($params['customer_gender'])){
//                    $genderQuery = " AND M.gender = '" . $params['customer_gender'] . "' ";                                   
//                }else{
//                    $genderQuery = "";
//                }
                
                if(isset($params['customer_gender']) && !empty($params['customer_gender'])){                    
                    if( $params['customer_gender'] == '2'){ 					
                    	$genderQuery = " AND M.gender in ( 0 , '" . $params['customer_gender'] . "' ) ";
                    } else {					
			            $genderQuery = " AND M.gender = '" . $params['customer_gender'] . "' ";
                    }  
                } else {

                    $genderQuery = "";
                }

                if(isset($params['customer_service_cat']) && !empty($params['customer_service_cat'])){
                    if( $params['customer_service_cat'] == '100000001'){
                    	$serviceQuery = " AND MSB.package_id NOT IN (0) AND MPS.service_category = '" . $params['customer_service_cat'] . "' ";
                    } else {
			$serviceQuery = " AND ( MSB.package_id IN (0) OR MPS.service_category = '" . $params['customer_service_cat'] . "' )";
                    }

                } else {
                    $serviceQuery = "";
                }

                if(isset($params['customer_id']) && !empty($params['customer_id'])){
                    $custQuery = " AND MSB.member_id = '" . $params['customer_id'] . "' ";
                }else{
                    $custQuery = "";
                }

        $response = '';
        if (isset($params['session_date'])) {
            if ($params['center_id'] == 0 && $params['customer_gender'] == 0 && $params['customer_service_cat'] == 0 && $params['customer_id'] == 0) {
                //final SQL
             //$result = DB::select("SELECT MSB.id, MSB.member_id, MSB.dietician_id, MSB.package_id, MSB.service_id, MSB.session_date, MSB.start_time, MSB.end_time, MSB.status, MSB.created_by,  M.first_name, M.last_name, M.mobile_number, M.gender, VC.center_name FROM member_session_bookings MSB LEFT JOIN members M ON MSB.member_id = M.id LEFT JOIN vlcc_centers VC ON M.crm_center_id = VC.crm_center_id LEFT JOIN admin_centers AC ON VC.id = AC.center_id WHERE AC.user_id = ". $params['user_id'] . " AND MSB.dietician_id = ". $params['user_id'] . " AND MSB.session_date = '" . $params['session_date'] . "'");

                $result = DB::select("SELECT MSB.id, MSB.member_id, MSB.dietician_id, MSB.package_id, MSB.service_id, MSB.session_date, MSB.start_time, MSB.end_time, MSB.status, MSB.created_by, MSB.session_comment, M.first_name, M.last_name, M.mobile_number, M.gender, VC.center_name FROM member_session_bookings MSB LEFT JOIN members M ON MSB.member_id = M.id LEFT JOIN vlcc_centers VC ON M.crm_center_id = VC.crm_center_id LEFT JOIN admin_centers AC ON VC.id = AC.center_id WHERE AC.user_id = ". $params['user_id'] . " AND MSB.session_date BETWEEN '".Date('Y-m-d', strtotime("-2 days"))."' AND '" . $params['session_date'] . "'");

            }else{
                
                //$result = DB::select("SELECT MSB.id, MSB.member_id, MSB.dietician_id, MSB.package_id, MSB.service_id, MSB.session_date, MSB.start_time, MSB.end_time, MSB.status, MSB.created_by, MSB.session_comment, M.first_name, M.last_name, M.mobile_number, M.gender, VC.center_name FROM member_session_bookings MSB LEFT JOIN members M ON MSB.member_id = M.id LEFT JOIN vlcc_centers VC ON M.crm_center_id = VC.crm_center_id LEFT JOIN admin_centers AC ON VC.id = AC.center_id WHERE AC.user_id = ". $params['user_id'] . " AND MSB.session_date BETWEEN '".Date('Y-m-d', strtotime("-2 days"))."' AND '" . $params['session_date'] . "'" . $centerQuery . $genderQuery . $serviceQuery . $custQuery);
              
                $result = DB::select("SELECT MSB.id, MSB.member_id, MSB.dietician_id, MSB.package_id, MSB.service_id, MSB.session_date, MSB.start_time, MSB.end_time, MSB.status, MSB.created_by, MSB.session_comment, M.first_name, M.last_name, M.mobile_number, M.gender, VC.center_name FROM member_session_bookings MSB LEFT JOIN member_package_services MPS ON MPS.id = MSB.service_id LEFT JOIN members M ON MSB.member_id = M.id LEFT JOIN vlcc_centers VC ON M.crm_center_id = VC.crm_center_id LEFT JOIN admin_centers AC ON VC.id = AC.center_id WHERE AC.user_id = ". $params['user_id'] . " AND MSB.session_date BETWEEN '".Date('Y-m-d', strtotime("-2 days"))."' AND '" . $params['session_date'] . "'" . $centerQuery . $genderQuery . $serviceQuery . $custQuery); 
            }
        } else {

              if ($params['center_id'] == 0 && $params['customer_gender'] == 0 && $params['customer_service_cat'] == 0 && $params['customer_id'] == 0) {
                  //Finalworking SQL
//                 $result = DB::select("SELECT MSB.id, MSB.member_id, MSB.dietician_id, MSB.package_id, MSB.service_id, MSB.session_date, MSB.start_time, MSB.end_time, MSB.status, MSB.created_by, M.first_name, M.last_name, M.mobile_number, M.gender, VC.center_name FROM member_session_bookings MSB LEFT JOIN members M ON MSB.member_id = M.id LEFT JOIN vlcc_centers VC ON M.crm_center_id = VC.crm_center_id LEFT JOIN admin_centers AC ON VC.id = AC.center_id WHERE AC.user_id = ". $params['dietician_id'] . " AND MSB.dietician_id = ". $params['dietician_id']);

                 $result = DB::select("SELECT MSB.id, MSB.member_id, MSB.dietician_id, MSB.package_id, MSB.service_id, MSB.session_date, MSB.start_time, MSB.end_time, MSB.status, MSB.created_by, M.first_name, M.last_name, M.mobile_number, M.gender, VC.center_name FROM member_session_bookings MSB LEFT JOIN members M ON MSB.member_id = M.id LEFT JOIN vlcc_centers VC ON M.crm_center_id = VC.crm_center_id LEFT JOIN admin_centers AC ON VC.id = AC.center_id WHERE AC.user_id = ". $params['dietician_id']);

            } else {

                if (isset($params["customer_id"]) && !empty($params["customer_id"])) {
                    $response = SessionBookings::with('Member')
                        ->where('dietician_id', $params['dietician_id'])
                        ->where('member_id', $params['customer_id'])
                        ->get();
                } else {
                    $response = SessionBookings::with('Member')->where('dietician_id', $params['dietician_id'])
                        ->get();
                }

                //Final working sql
//                $result = DB::select("SELECT MSB.id, MSB.member_id, MSB.dietician_id, MSB.package_id, MSB.service_id, MSB.session_date, MSB.start_time, MSB.end_time, MSB.status, M.first_name, M.last_name, M.mobile_number, M.gender, VC.center_name FROM member_session_bookings MSB  LEFT JOIN members M ON MSB.member_id = M.id LEFT JOIN vlcc_centers VC ON M.crm_center_id = VC.crm_center_id LEFT JOIN admin_centers AC ON VC.id = AC.center_id WHERE AC.user_id = ". $params['dietician_id'] . " AND MSB.dietician_id = ". $params['dietician_id'] . $centerQuery . $genderQuery . $custQuery);


               $result = DB::select("SELECT MSB.id, MSB.member_id, MSB.dietician_id, MSB.service_id, MSB.session_date, MSB.start_time, MSB.end_time, MSB.status, MSB.created_by, MSB.session_comment, M.first_name, M.last_name, M.mobile_number, M.gender, VC.center_name FROM member_session_bookings MSB LEFT JOIN member_package_services MPS ON MPS.id = MSB.service_id  LEFT JOIN members M ON MSB.member_id = M.id  LEFT JOIN vlcc_centers VC ON M.crm_center_id = VC.crm_center_id LEFT JOIN admin_centers AC ON VC.id = AC.center_id WHERE AC.user_id = ". $params['dietician_id']  .  $centerQuery . $genderQuery . $serviceQuery . $custQuery);

            }
        }
        return $result;
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
            $inputs["service_id"] = implode(",", $inputs["service_id"]);
            $sessionBookings = new $this->model;
            $allColumns = $sessionBookings->getTableColumns($sessionBookings->getTable());
            foreach ($inputs as $key => $value) {
                if (in_array($key, $allColumns)) {
                    $sessionBookings->$key = $value;
                }
            }
            $sessionBookings->status = isset($inputs['status']) ? $inputs['status'] : 0;
            $save = $sessionBookings->save();

            foreach ($inputs['staff_id'] as $staff_key => $staff_value) {
                $memberSessionBookingResources = new $this->memberSessionBookingResources;
                $memberSessionBookingResources->session_id = $sessionBookings['id'];
                $memberSessionBookingResources->member_id = $inputs['member_id'];
                $memberSessionBookingResources->resource_id = $staff_value;
                $memberSessionBookingResources->resource_type = 1;
                $memberSessionBookingResources->resource_start_time = isset($inputs['staff_start_time']) ? date('H:i', strtotime($inputs['staff_start_time'][$staff_key])) : '00:00:00';
                $memberSessionBookingResources->resource_end_time = isset($inputs['staff_end_time']) ? date('H:i', strtotime($inputs['staff_end_time'][$staff_key])) : '00:00:00';
                $memberSessionBookingResources->save();
            }

            if (isset($inputs['machine_id']) && !empty($inputs['machine_id'])) {
                foreach ($inputs['machine_id'] as $machine_key => $machine_value) {
                    $memberSessionBookingResources = new $this->memberSessionBookingResources;
                    $memberSessionBookingResources->session_id = $sessionBookings['id'];
                    $memberSessionBookingResources->member_id = $inputs['member_id'];
                    $memberSessionBookingResources->resource_id = $machine_value;
                    $memberSessionBookingResources->resource_type = 2;
                    $memberSessionBookingResources->resource_start_time = isset($inputs['machine_start_time']) ? date('H:i', strtotime($inputs['machine_start_time'][$machine_key])): '00:00:00';
                    $memberSessionBookingResources->resource_end_time = isset($inputs['machine_end_time']) ? date('H:i', strtotime($inputs['machine_end_time'][$machine_key])): '00:00:00';
                    $memberSessionBookingResources->save();
                }
            }

            if (isset($inputs['room_id']) && !empty($inputs['room_id'])) {
                foreach ($inputs['room_id'] as $room_key => $room_value) {
                    $memberSessionBookingResources = new $this->memberSessionBookingResources;
                    $memberSessionBookingResources->session_id = $sessionBookings['id'];
                    $memberSessionBookingResources->member_id = $inputs['member_id'];
                    $memberSessionBookingResources->resource_id = $room_value;
                    $memberSessionBookingResources->resource_type = 3;
                    $memberSessionBookingResources->resource_start_time = isset($inputs['room_start_time']) ? date('H:i', strtotime($inputs['room_start_time'][$room_key])): '00:00:00';
                    $memberSessionBookingResources->resource_end_time = isset($inputs['room_end_time']) ? date('H:i', strtotime($inputs['room_end_time'][$room_key])): '00:00:00';
                    $memberSessionBookingResources->save();
                }
            }

            if ($save) {
                $response['status'] = 'success';
                $response['message'] = trans('admin::messages.added', ['name' => trans('admin::controller/session-bookings.session-bookings')]);

// Code For Sending Push Notification to Customer When Dietician Books Session
                $tokenData = MemberDeviceToken::whereMemberId($inputs['member_id'])->first();
                $title = "VLCC - Slimmer's App";
                $tag = 'session_booking';
                $message_text = "You have new Session Booked on " . date('d M Y', strtotime($inputs["session_date"]));
                $extra['body'] = $message_text;
                $extra['title'] = $title;
                $extra['session_status'] = $sessionBookings->status;

                if (isset($tokenData->device_token)) {
                    PushHelper::sendGeneralPushNotification($tokenData->device_token, $tag, $message_text, $extra, $title, $tokenData->device_type, $sessionBookings->id);
// Insert Data into member_notifications table
                    $recommendations = new $this->recommendation_model;
                    $this->insertNotification($inputs, $recommendations, $message_text);
                }
                if (isset($inputs['sms_send'])) {
                    $mobileNumber = $this->getMemberMobileNumber($inputs['member_id']);
                    $sessionStatus = array("2" => "Booked", "4" => "Cancel", "9" => "Waiting list", "10" => "Confirmed", "11" => "No Response");
                    $validateData['mobile_number'] = $mobileNumber;
                    $messageText = "You have new Session Booked on " . date('d M Y', strtotime($inputs["session_date"]));
                    $validateData['message_text'] = $messageText;
                    $sendMessage = $this->sendSms($mobileNumber, $validateData);
                }
            } else {
                $response['status'] = 'error';
                $response['message'] = trans('admin::messages.not-added', ['name' => trans('admin::controller/session-bookings.session-bookings')]);
            }

            return $response;
        } catch (Exception $e) {
            $exceptionDetails = $e->getMessage();
            $response['status'] = 'error';
            $response['message'] = trans('admin::messages.not-added', ['name' => trans('admin::controller/session-bookings.session-bookings')]) . "<br /><b> Error Details</b> - " . $exceptionDetails;
            Log::error(trans('admin::messages.not-added', ['name' => trans('admin::controller/session-bookings.session-bookings')]), ['Error Message' => $exceptionDetails, 'Current Action' => Route::getCurrentRoute()->getActionName()]);

            return $response;
        }
    }

    /**
     * Update an session bookings.
     *
     * @param  Form data posted from ajax $inputs, Modules\Admin\Models\SessionBookings $sessionBookings
     * @return $result array with status and message elements
     * @return void
     */
    public function update($inputs, $sessionBookings)
    {
        try {
            $inputs["service_id"] = implode(",", $inputs["service_id"]);
            foreach ($inputs as $key => $value) {
                if (isset($sessionBookings->$key)) {
                    $sessionBookings->$key = $value;
                }
            }
            $sessionBookings->status = isset($inputs['status']) ? $inputs['status'] : 0;
            $sessionBookings->session_comment = $inputs["session_comment"];
            $machine_id = "";
            $staff_id = implode(",", $inputs['staff_id']);
            if (isset($inputs['machine_id']) && !empty($inputs['machine_id'])) {

                $machine_id = implode(",", $inputs['machine_id']);
            }

            $room_id = implode(",", $inputs['room_id']);

            DB::table('member_session_booking_resources')
                ->whereNotIn('resource_id', [$staff_id])
                ->whereResourceType(1)
                ->whereSessionId($sessionBookings['id'])
                ->whereMemberId($inputs['member_id'])
                ->delete();

            DB::table('member_session_booking_resources')
                ->whereNotIn('resource_id', [$machine_id])
                ->whereResourceType(2)
                ->whereSessionId($sessionBookings['id'])
                ->whereMemberId($inputs['member_id'])
                ->delete();

            DB::table('member_session_booking_resources')
                ->whereNotIn('resource_id', [$room_id])
                ->whereResourceType(3)
                ->whereSessionId($sessionBookings['id'])
                ->whereMemberId($inputs['member_id'])
                ->delete();

            foreach ($inputs['staff_id'] as $staff_key => $staff_value) {
                $memberSessionBookingResources = new $this->memberSessionBookingResources;

                $whereClause = [
                    'session_id' => $sessionBookings['id'],
                    'member_id' => $inputs['member_id'],
                    'resource_id' => $staff_value,
                    'resource_type' => 1
                ];
                $availabilityInsertOrUpdate = [
                    'resource_id' => $staff_value,
                    'resource_type' => 1,
                    'resource_start_time' => isset($inputs['staff_start_time']) ? date('H:i', strtotime($inputs['staff_start_time'][$staff_key])): '00:00:00',
                    'resource_end_time' => isset($inputs['staff_end_time']) ? date('H:i', strtotime($inputs['staff_end_time'][$staff_key])): '00:00:00',
                ];

                $memberSessionBookingResources->updateOrCreate($whereClause, $availabilityInsertOrUpdate);
            }

            if (isset($inputs['machine_id']) && !empty($inputs['machine_id'])) {

                foreach ($inputs['machine_id'] as $machine_key => $machine_value) {
                    $memberSessionBookingResources = new $this->memberSessionBookingResources;

                    $whereClause = [
                        'session_id' => $sessionBookings['id'],
                        'member_id' => $inputs['member_id'],
                        'resource_id' => $machine_value,
                        'resource_type' => 2
                    ];
                    $availabilityInsertOrUpdate = [
                        'resource_id' => $machine_value,
                        'resource_type' => 2,
                        'resource_start_time' => isset($inputs['machine_start_time']) ? date('H:i', strtotime($inputs['machine_start_time'][$machine_key])): '00:00:00',
                        'resource_end_time' => isset($inputs['machine_end_time']) ? date('H:i', strtotime($inputs['machine_end_time'][$machine_key])): '00:00:00',
                    ];

                    $memberSessionBookingResources->updateOrCreate($whereClause, $availabilityInsertOrUpdate);
                }
            }

            foreach ($inputs['room_id'] as $room_key => $room_value) {
                $memberSessionBookingResources = new $this->memberSessionBookingResources;

                $whereClause = [
                    'session_id' => $sessionBookings['id'],
                    'member_id' => $inputs['member_id'],
                    'resource_id' => $room_value,
                    'resource_type' => 3
                ];
                $availabilityInsertOrUpdate = [
                    'resource_id' => $room_value,
                    'resource_type' => 3,
                    'resource_start_time' => isset($inputs['room_start_time']) ? date('H:i', strtotime($inputs['room_start_time'][$room_key])): '00:00:00',
                    'resource_end_time' => isset($inputs['room_end_time']) ? date('H:i', strtotime($inputs['room_end_time'][$room_key])): '00:00:00',
                ];

                $memberSessionBookingResources->updateOrCreate($whereClause, $availabilityInsertOrUpdate);
            }

            $save = $sessionBookings->save();
            if ($save) {
                $response['status'] = 'success';
                $response['message'] = trans('admin::messages.updated', ['name' => trans('admin::controller/session-bookings.session-bookings')]);

// Code For Sending Push Notification to Customer When Dietician Cancels or Rejects or Reschedules session
                $flag = 0;

                if ($inputs['previous_status'] == 1 && $inputs['status'] == 2) {
// Status is rejected
                    $message_text = "Your session booking on " . date('d M Y', strtotime($inputs["session_date"])) . " has been confirmed.";
                    $flag = 1;
                } else if ($inputs['status'] == 3) {
// Status is rejected
                    $message_text = "Your session booking on " . date('d M Y', strtotime($inputs["session_date"])) . " has been rejected.";
                    $flag = 1;
                } else if ($inputs['status'] == 4) {
// Status is Cancelled
                    $message_text = "Your session booking on " . date('d M Y', strtotime($inputs["session_date"])) . " has been cancelled.";
                    $flag = 1;
                } else if ($inputs["previous_session_date"] != $inputs["session_date"] || $inputs["previous_start_time"] != $inputs["start_time"] || $inputs["previous_end_time"] != $inputs["end_time"]) {
                    $message_text = "Your session booking of " . date('d M Y', strtotime($inputs["previous_session_date"])) . " has been rescheduled.";
                    $flag = 1;
                } else {
// No Push Notification
                }

                if ($flag) {
                    $tokenData = MemberDeviceToken::whereMemberId($inputs['member_id'])->first();
                    $title = "VLCC - Slimmer's App";
                    $tag = 'session_booking';
                    $extra['body'] = $message_text;
                    $extra['title'] = $title;
                    $extra['session_status'] = $sessionBookings->status;

                    if (isset($tokenData->device_token)) {
                        PushHelper::sendGeneralPushNotification($tokenData->device_token, $tag, $message_text, $extra, $title, $tokenData->device_type, $sessionBookings->id);
// Insert Data into member_notifications table
// Insert Data into member_notifications table
                        $recommendations = new $this->recommendation_model;
                        $this->insertNotification($inputs, $recommendations, $message_text);
                    }
                }

                if (isset($inputs['sms_send'])) {
                    $mobileNumber = $this->getMemberMobileNumber($inputs['member_id']);
                    $sessionStatus = array("2" => "Booked", "4" => "Cancelled", "6" => "Waiting list", "7" => "Confirmed", "8" => "No Response");
                    $validateData['mobile_number'] = $mobileNumber;
                    $messageText = "Your session booking on " . date('d M Y', strtotime($inputs["session_date"])) . " has been " . $sessionStatus[$inputs['status']];
                    $validateData['message_text'] = $messageText;
                    $sendMessage = $this->sendSms($mobileNumber, $validateData);
                }
                /*                 * * Code For Sending Push Notification ends here ** */
            } else {
                $response['status'] = 'error';
                $response['message'] = trans('admin::messages.not-updated', ['name' => trans('admin::controller/session-bookings.session-bookings')]);
            }

            return $response;
        } catch (Exception $e) {
            $exceptionDetails = $e->getMessage();
            $response['status'] = 'error';
            $response['message'] = trans('admin::messages.not-updated', ['name' => trans('admin::controller/session-bookings.session-bookings')]) . "<br /><b> Error Details</b> - " . $exceptionDetails;
            Log::error(trans('admin::messages.not-updated', ['name' => trans('admin::controller/session-bookings.session-bookings')]), ['Error Message' => $exceptionDetails, 'Current Action' => Route::getCurrentRoute()->getActionName()]);

            return $response;
        }
    }

    /**
     * Delete actions on session bookingss
     *
     * @param  int  $status
     * @return int
     */
    public function deleteAction($inputs)
    {
        try {

            $resultStatus = false;

            $id = $inputs['ids'];
            $sessionBookings = SessionBookings::find($id);
            if (!empty($sessionBookings)) {
                $sessionBookings->delete();
                $resultStatus = true;
            }

            return $resultStatus;
        } catch (Exception $e) {
            $exceptionDetails = $e->getMessage();
            $response['status'] = 'error';
            $response['message'] = trans('admin::messages.not-deleted', ['name' => trans('admin::controller/session-bookings.session-bookings')]) . "<br /><b> Error Details</b> - " . $exceptionDetails;
            Log::error(trans('admin::messages.not-deleted', ['name' => trans('admin::controller/session-bookings.session-bookings')]), ['Error Message' => $exceptionDetails, 'Current Action' => Route::getCurrentRoute()->getActionName()]);

            return $response;
        }
    }

    // Function to Check Customer Session Booking
    public function checkSessionAvailability($params)
    {
        $params['session_date'] = date('Y-m-d', strtotime($params['session_date']));
        $packageId = filter_var($params['package_id'], FILTER_VALIDATE_INT);
        $serviceId = filter_var($params['service_id'][0], FILTER_VALIDATE_INT);
        $customerId = filter_var($params['customer_id'], FILTER_VALIDATE_INT);
        $start_time = date('H:i:s', strtotime($params['start_time']));
        $end_time = date('H:i:s', strtotime($params['end_time']));
        $session_id = $params["session_id"];
        $condition = "AND 1=1";
        if ($session_id != 0) {
            $condition = " AND id!=" . $session_id;
        }

        $start_date_time = $params['session_date'] . " " . $start_time;
        //$start_date_time = date('Y-m-d H:i:s', strtotime("-1 minutes",strtotime($start_date_time)));
        $end_date_time = $params['session_date'] . " " . $end_time;
        //$end_date_time = date('Y-m-d H:i:s', strtotime("-1 minutes",strtotime($end_date_time)));
        DB::setFetchMode(PDO::FETCH_ASSOC);
        $result = DB::select("SELECT id, service_id FROM member_session_bookings WHERE member_id = '" . $customerId . "' AND session_date = '" . $params['session_date'] . "' AND package_id = '" . $packageId . "'  $condition  AND((CONCAT(session_date,' ',start_time) >= '" . $start_date_time . "' AND CONCAT(session_date,' ',start_time) < '" . $end_date_time . "') OR(CONCAT(session_date,' ',end_time) > '" . $start_date_time . "' AND CONCAT(session_date,' ',end_time) <= '" . $end_date_time . "'))");

        DB::setFetchMode(PDO::FETCH_CLASS);
        if (!empty($result)) {
            //$service_id_array = array_column($result, 'service_id');
            return count($result);
        } else {
            return 0;
        }
        //$result = isset($result[0]) ? count($result[0]) : 0;
        //return $result;
//        return SessionBookings::select(['id'])
//                ->where('member_id', $params['customer_id'])
//                ->where('session_date', $params['session_date'])
//                ->where(function($query) {
//                    return $query
//                        ->orWhere('status', '=', '1')
//                        ->orWhere('status', '=', '2');
//                })
//                ->get();
    }

// Function to Get Member Packages List
    public function getMemberPackages($params)
    {
        return MemberPackage::select(['id', 'package_title'])
                ->where('member_id', $params['member_id'])
//->where('end_date', '>=', date('Y-m-d'))
                ->orderBY('package_title', 'ASC')->lists('package_title', 'id');
//return MemberPackage::whereMemberId($params['member_id'])->orderBY('package_title')->lists('package_title', 'id');
    }

// Function to Get Member Package Services List
    public function getMemberPackageServices($params)
    {
        $used_services = MemberPackageServices::select(['id', 'service_name'])
                ->where('member_id', $params['member_id'])
                ->where('package_id', $params['package_id'])
                ->whereRaw('services_booked-services_consumed = 0')
                ->orderBY('service_name', 'ASC')->lists('service_name', 'id');


        if ($params['package_id'] != 0){
            $remaining_services = MemberPackageServices::select(['id', 'service_name'])
                ->where('member_id', $params['member_id'])
                ->where('package_id', $params['package_id'])
                ->whereRaw('services_booked-services_consumed  >  0')
                //->whereRaw('services_paid  >  0')
                ->orderBY('service_name', 'ASC')->lists('service_name', 'id');
        } else {
            $remaining_services = BeautyServices::select(['id', 'service_name'])
                ->where('status', 1)
                ->orderBY('service_name', 'ASC')->lists('service_name', 'id');
        }


        /*$unpaid_services = MemberPackageServices::select(['id', 'service_name'])
                ->where('member_id', $params['member_id'])
                ->where('package_id', $params['package_id'])
                ->whereRaw('services_paid-services_consumed = 0')
                ->whereRaw('services_booked > services_paid')
                ->orderBY('service_name', 'ASC')->lists('service_name', 'id');*/

        $result["used_services"] = $used_services;
        $result["remaining_services"] = $remaining_services;
        //$result["unpaid_services"] = $unpaid_services;
        return $result;
    }

// Function to Get Beauty Services List
    public function getBeautyServices($params)
    {
        $result = BeautyServices::select(['id', 'service_name'])
                ->orderBY('service_name', 'ASC')->lists('service_name', 'id');
        return $result;
    }

// Function to insert data into member_notifications table
    public function insertNotification($inputs, $recommendations, $message_text)
    {
        $recommendations->member_id = $inputs["member_id"];
        $recommendations->message_type = 4;
        $recommendations->message_text = $message_text;
        $recommendations->status = 1;
        $recommendations->created_by = Auth::guard('admin')->user()->id;
        $saveRecommendation = $recommendations->save();
    }

// Function to check if Selected package is valid or not while booking session
    public function checkPackageActive($package_id)
    {
        $flag = 1;
        DB::setFetchMode(PDO::FETCH_ASSOC);
        $result = DB::select("SELECT end_date FROM member_packages WHERE id = " . $package_id . "");
        DB::setFetchMode(PDO::FETCH_CLASS);
        if (isset($result[0]) && $result[0] < date('Y-m-d')) {
            $flag = 0;
        }
        return $flag;
    }

    public function dataCount($params = [])
    {
        $response = '';
        if ($params['user_type_id'] == 4 || $params['user_type_id'] == 8) { //Dietician & Slimming Head
            $response = SessionBookings::with('MemberPackage', 'Member')
                ->where('dietician_id', $params['user_id'])
                ->where('session_date', $params['session_date'])
                ->count();
        } elseif ($params['user_type_id'] == 1) { // Webmaster
            $response = SessionBookings::with('MemberPackage', 'Member')
                ->where('session_date', $params['session_date'])
                ->count();
        } elseif ($params['user_type_id'] == 6 || $params['user_type_id'] == 9 || $params['user_type_id'] == 5 || $params['user_type_id'] == 7) { // Physiotherapist, Center Head, Doctors and ATH
            $centerIds = DB::select("SELECT group_concat(center_id) as center_id FROM admin_centers WHERE user_id = " . $params['user_id'] . "");
            if (isset($centerIds[0]) && isset($centerIds[0]->center_id) && $centerIds[0]->center_id != '') {
                $response = SessionBookings::with('MemberPackage', 'Member')
                    ->where('session_date', $params['session_date'])
                    ->whereRaw("FIND_IN_SET(dietician_id,(select group_concat(user_id) FROM admin_centers WHERE center_id IN( " . $centerIds[0]->center_id . ")))")
                    ->count();
            }
        } else {
            $response = collect();
        }

        return $response;
    }

    public function getAvailabilityList($params)
    {
        DB::setFetchMode(PDO::FETCH_ASSOC);
        $memberCenterId = DB::select("select C.id as center_id from vlcc_centers C INNER JOIN members M ON C.crm_center_id=M.crm_center_id where M.id= " . $params['member_id'] . "");

        DB::setFetchMode(PDO::FETCH_CLASS);

        if (isset($memberCenterId) && !empty($memberCenterId)) {
            $centerId = $memberCenterId[0]['center_id'];
            $response['center_id'] = $centerId;

            DB::setFetchMode(PDO::FETCH_ASSOC);
            $staff_result = DB::select("SELECT A.id,A.first_name,A.last_name from admins A INNER JOIN admin_centers C ON A.id=C.user_id where C.center_id = " . $centerId . " AND A.user_type_id NOT IN ('1','2','3','9','11') AND A.status=1");
            DB::setFetchMode(PDO::FETCH_CLASS);
            $response['staff_list'] = collect($staff_result);

            DB::setFetchMode(PDO::FETCH_ASSOC);
            $machine_result = DB::select("select M.id,M.name from machines M INNER JOIN machine_centers C ON M.id=C.machine_id where C.center_id = " . $centerId . " AND M.status=1");
            $newValueMachine = array('id' => 9999999, 'name' => 'Not Required');
            array_push($machine_result, $newValueMachine);
            DB::setFetchMode(PDO::FETCH_CLASS);
            $response['machine_list'] = collect($machine_result);

            DB::setFetchMode(PDO::FETCH_ASSOC);
            $room_result = DB::select("select id,name from rooms where center_id = " . $centerId . " AND status=1");
            $newValueRoom = array('id' => 9999999, 'name' => 'Not Required');
            array_push($room_result, $newValueRoom);
            DB::setFetchMode(PDO::FETCH_CLASS);
            $response['room_list'] = collect($room_result);

            return $response;
        }
    }

    public function fetchResources($params)
    {
        if ($params['flag'] == 1) {
            //return $result = User::whereIn('id', $params['id'])->whereIn('user_type_id', [4, 5, 10])->get();
            return $result = User::whereIn('id', $params['id'])->whereNotIn('user_type_id', [1, 2, 3, 9, 11])->get();
        } else if ($params['flag'] == 2) {
            return Machine::whereIn('id', $params['id'])->get();
        } else if ($params['flag'] == 3) {
            return $result = Room::whereIn('id', $params['id'])->get();
        }
    }

    public function fetchAvailability($params)
    {
        if ($params['flag'] == 1) {
            return $result = StaffAvailability::whereCenterId($params['center_id'])->whereIn('staff_id', $params['id'])->get();
        } else if ($params['flag'] == 2) {
            return $result = MachineAvailability::whereCenterId($params['center_id'])->whereIn('machine_id', $params['id'])->get();
        } else if ($params['flag'] == 3) {
            return $result = RoomAvailability::whereCenterId($params['center_id'])->whereIn('room_id', $params['id'])->get();
        }
    }

    public function fetchSessionList($params, $flag)
    {
        $resources = implode(",", $params['id']);
        DB::setFetchMode(PDO::FETCH_ASSOC);

        $machine_result = DB::select("SELECT S.id,R.resource_start_time AS start_time, R.resource_end_time AS end_time, M.first_name, R.resource_id from member_session_bookings S INNER JOIN member_session_booking_resources R ON S.id=R.session_id INNER JOIN members M ON S.member_id=M.id WHERE S.session_date='" . $params['availability_date'] . "' AND R.resource_type=" . $flag . " AND R.resource_id IN (" . $resources . ") AND R.resource_start_time BETWEEN '" . $params['start_time'] . "' AND '" . $params['end_time'] . "'");

        DB::setFetchMode(PDO::FETCH_CLASS);
        return collect($machine_result);
    }

    public function getSessionResourceDetails($session_id)
    {
        return MemberSessionBookingResources::whereSessionId($session_id)->get();
    }

    // Function to get session data from session id
    public function getSessionData($sessionId)
    {
        //$result = SessionBookings::select(['id', 'member_id', 'dietician_id', 'package_id', 'service_id', 'session_date'])->where('id', $sessionId)->first();
        $result = DB::select("SELECT member_session_bookings.id, member_session_bookings.member_id, member_session_bookings.dietician_id, member_session_bookings.package_id, member_session_bookings.service_id, member_session_bookings.session_date, members.crm_customer_id, members.crm_center_id, member_packages.crm_package_guid, admins.username as dietician_username FROM member_session_bookings LEFT OUTER JOIN members ON member_session_bookings.member_id = members.id LEFT OUTER JOIN member_packages ON member_session_bookings.package_id = member_packages.id LEFT OUTER JOIN admins ON member_session_bookings.dietician_id = admins.id WHERE member_session_bookings.id=" . $sessionId);
        $result = json_decode(json_encode($result), true);
        return $result[0];
    }

    //Function to get member Center name for view today session
    public function getMemberCenterName($memberId)
    {
        $response = DB::table('members')
            ->select('members.crm_center_id', 'vlcc_centers.center_name')
            ->join('vlcc_centers', 'members.crm_center_id', '=', 'vlcc_centers.crm_center_id')
            ->where('members.id', '=', $memberId)
            ->get();
        return $response[0]->center_name;
    }

    // Function to get beauty services name
    public function beautyServiceName($serviceIds)
    {
        $result = DB::table('beauty_services')->select('service_name')->whereIn('id', explode(",", $serviceIds))->get();
        $result = json_decode(json_encode($result), true);
        $result = array_column($result, 'service_name');
        $string = "";
        foreach ($result as $value) {
            $string .= $value . ",";
        }
        return rtrim($string, ",");
    }

    // Function to update session status
    public function updateSessionStatus($session_id)
    {
        $flag = SessionBookings::where('id', $session_id)
            ->update(['status' => 5]);
        return 1;
    }
    
    //Function to get Member Package Services for view todas session data table

    public function getTodaysMemberPackageServices($servicesIds) {

         $response = DB::table('member_package_services')->select('service_name')->whereIn('id', explode(",", $servicesIds))->get();
       // $response = DB::select("select service_name from member_package_services where id IN($servicesData)");
        return $response;
    }

    public function sendSms($to, $params = [])
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

    public function getMemberMobileNumber($memberId)
    {
        DB::setFetchMode(PDO::FETCH_ASSOC);
        $member_mobile = DB::select("SELECT mobile_number FROM members WHERE id=" . $memberId);
        DB::setFetchMode(PDO::FETCH_CLASS);
        $mobile_no = (isset($member_mobile[0]["mobile_number"])) ? $member_mobile[0]["mobile_number"] : 0;
        return $mobile_no;
    }

    public function bookingHistoryStatus($params, $flag)
	{
        if($params['member_id'] == 0 || $params['member_id'] == "") {
            $var = DB::table('member_session_bookings')
                ->select('members.dietician_username','members.first_name','members.mobile_number','member_session_bookings.package_id', 'member_packages.package_title','member_session_bookings.session_date','member_session_bookings.start_time',
                    'member_session_bookings.end_time','member_session_bookings.status','adm.first_name as Created_BY', 'adm1.first_name as Updated_BY' , 'member_session_bookings.id AS session_id',
                    DB::raw("(SELECT GROUP_CONCAT(mps.service_name) FROM member_session_bookings AS mbs_new INNER JOIN member_package_services AS mps ON FIND_IN_SET(mps.id, mbs_new.service_id) > 0
     WHERE mbs_new.id = session_id) as service_name"),'beauty_services.service_name as service_name1')
                ->leftJoin( 'members', 'members.id','=','member_session_bookings.member_id')
                ->leftJoin('member_package_services','member_package_services.id','=','member_session_bookings.service_id')
                ->leftJoin('member_packages','member_packages.id','=','member_session_bookings.package_id')
                ->leftJoin('admins as adm','adm.id','=','member_session_bookings.created_by')
                ->leftJoin('admins as adm1', 'adm1.id','=', 'member_session_bookings.updated_by')
                ->leftJoin('beauty_services','beauty_services.id','=','member_session_bookings.service_id')
                ->leftJoin('vlcc_centers','members.crm_center_id','=','vlcc_centers.crm_center_id')
                ->leftJoin('admin_centers','vlcc_centers.id','=','admin_centers.center_id')
                ->where('admin_centers.user_id','=',$params['dietician_id'])
                ->where('member_session_bookings.session_date','>=',$params['from'])
                ->where('member_session_bookings.session_date','<=',$params['to'])
                ->orderBy('member_session_bookings.session_date','DESC');
        } else {
            $var = DB::table('member_session_bookings')
                ->select('members.dietician_username', 'members.first_name', 'members.mobile_number', 'member_session_bookings.package_id', 'member_packages.package_title', 'member_session_bookings.session_date', 'member_session_bookings.start_time',
                    'member_session_bookings.end_time', 'member_session_bookings.status', 'adm.first_name as Created_BY', 'adm1.first_name as Updated_BY', 'member_session_bookings.id AS session_id',
                    DB::raw("(SELECT GROUP_CONCAT(mps.service_name) FROM member_session_bookings AS mbs_new INNER JOIN member_package_services AS mps ON FIND_IN_SET(mps.id, mbs_new.service_id) > 0
     WHERE mbs_new.id = session_id) as service_name"), 'beauty_services.service_name as service_name1')
                ->leftJoin('members', 'members.id', '=', 'member_session_bookings.member_id')
                ->leftJoin('member_package_services', 'member_package_services.id', '=', 'member_session_bookings.service_id')
                ->leftJoin('member_packages', 'member_packages.id', '=', 'member_session_bookings.package_id')
                ->leftJoin('admins as adm', 'adm.id', '=', 'member_session_bookings.created_by')
                ->leftJoin('admins as adm1', 'adm1.id', '=', 'member_session_bookings.updated_by')
                ->leftJoin('beauty_services', 'beauty_services.id', '=', 'member_session_bookings.service_id')
                ->where('member_session_bookings.member_id', '=', $params['member_id'])
                ->where('member_session_bookings.session_date', '>=', $params['from'])
                ->where('member_session_bookings.session_date', '<=', $params['to'])
                ->orderBy('member_session_bookings.session_date', 'DESC');
        }
	
	if($flag == 1 )
		return $var->get();
	else
		return $var;
	
	}
}
