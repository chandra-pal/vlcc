<?php

/**
 * The repository class for managing member model specific actions.
 *
 *
 * @author Tushar Dahiwale <tushard@iprogrammer.com>
 * @package Admin
 * @since 1.0
 */

namespace Modules\Admin\Repositories;

use Modules\Admin\Models\Member;
use Modules\Admin\Models\DietPlan;
use Modules\Admin\Models\MemberActivityLog;
use Modules\Admin\Models\MemberPackage;
use Modules\Admin\Models\MemberBcaDetails;
use Modules\Admin\Models\MemberPackageServices;
use Modules\Admin\Models\MemberSessionRecord;
use DB;
use Cache;
use Auth;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7;
use GuzzleHttp\Psr7\Stream;
use Config;
use Carbon\Carbon;
use Log;
use PDO;
use Modules\Admin\Services\Helper\UserInfoHelper;

//use Guzzle\Http\Exception\ClientErrorResponseException;

class MembersRepository extends BaseRepository {

    /**
     * Create a new repository instance.
     *
     * @param  Modules\Admin\Models\Country $country
     * @return void
     */
    public function __construct(Member $member) {
        $this->model = $member;
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function data($request, $params = []) {
        $cacheKey = str_replace(['\\'], [''], __METHOD__) . ':' . md5(json_encode($params));
        $response = '';
        if ($params['user_type_id'] == 4) { //Dietician 
            $userInfoHelper = new UserInfoHelper();
            $user_center = $userInfoHelper->getLoggedInUserCenter($params["user_id"]);
            $response = Member::orderBy('first_name')->where('dietician_username', $params['username'])->whereStatus("1")->get();
            //$response = Member::has('Packages')->with('Packages')->orderBy('first_name')->where('dietician_username', $params['username'])->get();
//
            $first = Member::orderBy('first_name')->where('dietician_username', $params['username'])->where('status', 1)->get();
            $second = Member::select('members.id', 'members.dietician_username', 'members.first_name', 'members.last_name', 'members.mobile_number', 'members.gender', 'members.status', 'members.crm_customer_id', 'members.crm_center_id')->with('Centers')->orderBy('first_name')->where('dietician_username', '')->where('crm_center_id', $user_center[0]['crm_center_id'])->where('status', 1)->get();

            $response = $first->merge($second); // Contains foo and bar.
//            print_r($merged->toArray());
//            die;
        } elseif ($params['user_type_id'] == 7 || $params['user_type_id'] == 8) { // Center Head & Slimming Head
            $response = Member::orderBy('first_name')
                    ->join('vlcc_centers', 'members.crm_center_id', '=', 'vlcc_centers.crm_center_id')
                    ->join('admin_centers', 'vlcc_centers.id', '=', 'admin_centers.center_id')
                    ->select('members.id', 'members.dietician_username', 'members.first_name', 'members.last_name', 'members.mobile_number', 'members.gender', 'members.status', 'members.crm_customer_id', 'members.crm_center_id')
                    ->where('admin_centers.user_id', '=', $params['user_id'])
                    ->where('members.status', '=', 1)
                    //->whereStatus("1")
                    ->get();
        } elseif ($params['user_type_id'] == 9 || $params['user_type_id'] == 5) { // ATH & Physiotherapist
            $response = Member::orderBy('first_name')
                    ->join('vlcc_centers', 'members.crm_center_id', '=', 'vlcc_centers.crm_center_id')
                    ->select('members.id', 'members.dietician_username', 'members.first_name', 'members.last_name', 'members.mobile_number', 'members.gender', 'members.status', 'members.crm_customer_id', 'members.crm_center_id')
                    ->where('vlcc_centers.id', '=', $params['centerId'])
                    ->where('members.status', '=', 1)
                    //->whereStatus("1")
                    ->get();
        } else {
            $response = collect();
        }
        return $response;
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function getMemberData($request, $params = []) {
        $cacheKey = str_replace(['\\'], [''], __METHOD__) . ':' . md5(json_encode($params));
        //Cache::tags not suppport with files and Database
        //$response = Cache::tags(Member::table())->remember($cacheKey, $this->ttlCache, function() use($params) {
        return Member::whereId($params['id'])->first();
        //});
        return $response;
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function listMembersDataByDietician($params = []) {
        if (isset($params['username'])) {
            $cacheKey = str_replace(['\\'], [''], __METHOD__) . ':' . md5(json_encode($params['username']));
        } else {
            $cacheKey = str_replace(['\\'], [''], __METHOD__);
        }

        //Cache::tags not suppport with files and Database
        if (isset($params['username'])) {
            //$response = Cache::tags(Member::table())->remember($cacheKey, $this->ttlCache, function() use($params) {
            return Member::select('id', DB::raw('CONCAT_WS(" ",mobile_number, "-", first_name, last_name) AS full_name'))->orderBy('first_name')->where('dietician_username', $params['username'])->whereStatus("1")->lists('full_name', 'id');
            //});
        } else {
            //$response = Cache::tags(Member::table())->remember($cacheKey, $this->ttlCache, function() use($params) {
            return Member::select('id', DB::raw('CONCAT_WS(" ",mobile_number, "-", first_name, last_name) AS full_name'))->orderBy('first_name')->whereStatus("1")->lists('full_name', 'id');
            //});
        }
        return $response;
    }

    public function listMemberData($params) {

        $client = new Client(); //GuzzleHttp\Client
        $apiBaseUrl = Config::get('admin.api_base_url');
        $response = $client->request('POST', $apiBaseUrl . 'listMember', [
            'form_params' => ['username' => $params['username']]
        ]);
        return json_decode($response->getBody(), true);
    }

    public function getMemberBcaData($memberId) {
        $response = MemberBcaDetails::orderBY('recorded_date', 'DESC')->whereMemberId($memberId)->first();
        return $response;
        /* $client = new Client(); //GuzzleHttp\Client
          $crmBaseUrl = Config::get('admin.crm_base_url');
          try {
          $crmResponse = $client->request('POST', $crmBaseUrl . 'getCustomerDetails?mobile=' . $params['mobile_number']);
          $response = json_decode($crmResponse->getBody(), true);
          //$response = MemberBcaDetails::orderBY('recorded_date', 'DESC')->whereMemberId($memberId)->first();
          //echo "response!";
          //dd($response);
          } catch (GuzzleException $exception) {
          $response = $exception->getResponse();
          $response = "error";
          }
          return $response; */
    }

    public function getRecommendedCalories($params) {
        $cacheKey = str_replace(['\\'], [''], __METHOD__) . ':' . md5(json_encode($params));
        $response = Cache::tags(DietPlan::table())->remember($cacheKey, $this->ttlCache, function() use($params) {
            return DietPlan::select('calories')->whereId($params['memberDietPlan'])->orderBY('id')->first();
        });
        return $response;
    }

    public function getLatestActivity($params) {
        $cacheKey = str_replace(['\\'], [''], __METHOD__) . ':' . md5(json_encode($params));
        $response = Cache::tags(MemberActivityLog::table())->remember($cacheKey, $this->ttlCache, function() use($params) {
            return MemberActivityLog::select('activity')->whereMemberId($params['id'])->orderBY('created_at', 'desc')->lists('activity')->first();
        });
        return $response;
    }

    public function listMembers($params) {
        return Member::whereDietitianUsername($params['username'])->get();
    }

    public function importMemberDataOriginal($params) {
        $client = new Client(); //GuzzleHttp\Client
        if (config('app.env') == 'production') {
            $crmBaseUrl = config('admin.crm_base_url_prod');
        } else {
            $crmBaseUrl = config('admin.crm_base_url_dev');
        }
        $guzzleDebugMode = false;
        if (config('app.timezone') == 'Asia/Dubai') {
            $guzzleDebugMode = true;
        }
        try {
            $msg = "There were no customers present for this user.";
            $result = "success";
            $customerCount = 0;
            $response = $client->request('POST', $crmBaseUrl . 'getDietitianCustomerDetails?username=' . $params['username'], array(
                'timeout' => 600 * 30, //30 minutes
                'connection_timeout' => 30, //30 minutes
                'debug' => $guzzleDebugMode
            ));
            $responseData = json_decode($response->getBody(), true);
            if (!empty($responseData['response']['Customer'])) {
                $numberOfCustomers = sizeof($responseData['response']['Customer']);
                if ($numberOfCustomers < 100) {
                    $created_by = (isset(Auth::guard('admin')->user()->userType->id)) ? Auth::guard('admin')->user()->userType->id : "1";
                    foreach ($responseData['response']['Customer'] as $k => $customerDetails) {
                        if ($customerDetails['profile_data']['mobile_number'] != "") {
                            DB::table('members')->where('mobile_number', $customerDetails['profile_data']['mobile_number'])->where("crm_customer_id", "!=", $customerDetails['profile_data']['clientid'])->update([
                                'status' => "0"
                            ]);

                            $check = Member::select('id', 'mobile_number')->where('mobile_number', $customerDetails['profile_data']['mobile_number'])->where("crm_customer_id", "=", $customerDetails['profile_data']['clientid'])->whereStatus(1)->first();

                            if (strtolower($customerDetails['profile_data']['gender'] == 'male')) {
                                $gender = 1;
                            } else {
                                $gender = 2;
                            }
                            $created_by = isset(Auth::guard('admin')->user()->userType->id) ? Auth::guard('admin')->user()->userType->id : "1";
                            if (!isset($check)) {
                                $memberId = DB::table('members')->insertGetId([
                                    'crm_customer_id' => $customerDetails['profile_data']['clientid'],
                                    'crm_center_id' => $responseData['response']['DieticianDetails']['center_id'],
                                    'dietician_username' => $params['username'],
                                    'first_name' => $customerDetails['profile_data']['first_name'],
                                    'last_name' => (isset($customerDetails['profile_data']['last_name'])) ? $customerDetails['profile_data']['last_name'] : '',
                                    'date_of_birth' => (isset($customerDetails['profile_data']['date_of_birth'])) ? $customerDetails['profile_data']['date_of_birth'] : '',
                                    'mobile_number' => $customerDetails['profile_data']['mobile_number'],
                                    'diet_plan_id' => 0,
                                    'gender' => $gender,
                                    'created_by' => $created_by,
                                    'created_at' => Carbon::now()
                                ]);

                                if (!empty($customerDetails['package_data'])) {
                                    foreach ($customerDetails['package_data'] as $i => $packageData) {
                                        $package_name = array();
                                        if (!empty($packageData['services'])) {
                                            foreach ($packageData['services'] as $services) {
                                                $services['service_name'] = isset($services['service_name']) ? $services['service_name'] : "";
                                                array_push($package_name, $services['service_name']);
                                            }

                                            if (count($package_name) > 1) {
                                                $package_name_string = implode(' + ', $package_name);
                                            } else {
                                                $package_name_string = implode('', $package_name);
                                            }
                                            $package_name = $package_name_string;

                                            $start_date = "";
                                            if (null != $customerDetails['profile_data']['start_date']) {
                                                $s_date = Carbon::createFromFormat('d/m/Y', $customerDetails['profile_data']['start_date']);
                                                if ($start_date != "false") {
                                                    $start_date = $s_date->format('Y-m-d');
                                                }
                                            }

                                            $end_date = "";
                                            if (null != $customerDetails['profile_data']['end_date']) {
                                                $e_date = Carbon::createFromFormat('d/m/Y', $customerDetails['profile_data']['end_date']);
                                                if ($end_date != "false") {
                                                    $end_date = $e_date->format('Y-m-d');
                                                }
                                            }

                                            $amount_paid = "0";
                                            $total_amount = (null != $packageData['total_amount']) ? $packageData['total_amount'] : 0;
                                            $amount_remaining = (null != $packageData['total_amount_remaining']) ? $packageData['total_amount_remaining'] : 0;
                                            if ($total_amount > 0 && $amount_remaining >= 0 && $total_amount >= $amount_remaining) {
                                                $amount_paid = $total_amount - $amount_remaining;
                                            }

                                            $package_id = DB::table('member_packages')->insertGetId([
                                                'member_id' => $memberId,
                                                'crm_package_id' => $i,
                                                'package_title' => $package_name,
                                                'start_date' => $start_date,
                                                'end_date' => $end_date,
                                                'total_payment' => $total_amount,
                                                'payment_made' => $amount_paid,
                                                'created_by' => $created_by,
                                                'created_at' => Carbon::now(),
                                                'updated_at' => Carbon::now()
                                            ]);
                                            foreach ($packageData['services'] as $k => $services) {
                                                $services['service_validity'] = ($services['service_validity'] == null) ? "" : $services['service_validity'];

                                                $crm_service_id = $i . '_' . $k;
                                                if (null != $services['service_start_date']) {
                                                    $serviceStartDate = Carbon::createFromFormat('d/m/Y', $services['service_start_date']);
                                                    $serviceEndDate = clone $serviceStartDate;
                                                    $serviceEndDate->addDays($services['service_validity']);
                                                    $serviceEndDate = $serviceEndDate->format('Y-m-d');
                                                    $serviceStartDate = $serviceStartDate->format('Y-m-d');
                                                } else {
                                                    $serviceStartDate = "";
                                                    $serviceEndDate = "";
                                                }

                                                DB::table('member_package_services')->insert([
                                                    'package_id' => $package_id,
                                                    'member_id' => $memberId,
                                                    'crm_service_id' => $crm_service_id,
                                                    'service_name' => $services['service_name'],
                                                    'service_validity' => $services['service_validity'],
                                                    'services_booked' => $services['total_services'],
                                                    'services_consumed' => $services['services_consumed'],
                                                    'start_date' => $serviceStartDate,
                                                    'end_date' => $serviceEndDate,
                                                    'created_by' => $created_by,
                                                    'created_at' => Carbon::now(),
                                                    'updated_at' => Carbon::now()
                                                ]);
                                            }
                                        }
                                    }
                                }
                            } else {

                                DB::table('members')->where('mobile_number', $customerDetails['profile_data']['mobile_number'])->whereStatus(1)->update([
                                    'crm_customer_id' => $customerDetails['profile_data']['clientid'],
                                    'crm_center_id' => $responseData['response']['DieticianDetails']['center_id'],
                                    'dietician_username' => $params['username'],
                                    'first_name' => $customerDetails['profile_data']['first_name'],
                                    'last_name' => (isset($customerDetails['profile_data']['last_name'])) ? $customerDetails['profile_data']['last_name'] : '',
                                    'date_of_birth' => (isset($customerDetails['profile_data']['date_of_birth'])) ? $customerDetails['profile_data']['date_of_birth'] : '',
                                    'mobile_number' => $customerDetails['profile_data']['mobile_number'],
                                    'gender' => $gender,
                                    'updated_by' => $created_by,
                                    'updated_at' => Carbon::now()
                                ]);

                                $memberData = $check->toArray();
                                if (!empty($customerDetails['package_data'])) {
                                    foreach ($customerDetails['package_data'] as $i => $packageData) {
                                        $checkMember = MemberPackage::select('id')->where('member_id', $memberData['id'])->where('crm_package_id', $i)->first();
                                        $package_name = array();
                                        if (empty($checkMember)) {
                                            if (!empty($packageData['services'])) {
                                                foreach ($packageData['services'] as $services) {
                                                    $services['service_name'] = isset($services['service_name']) ? $services['service_name'] : "";
                                                    array_push($package_name, $services['service_name']);
                                                }

                                                if (count($package_name) > 1) {
                                                    $package_name_string = implode(' + ', $package_name);
                                                } else {
                                                    $package_name_string = implode('', $package_name);
                                                }
                                                $package_name = $package_name_string;

                                                $start_date = "";
                                                if (null != $customerDetails['profile_data']['start_date']) {
                                                    $s_date = Carbon::createFromFormat('d/m/Y', $customerDetails['profile_data']['start_date']);
                                                    if ($start_date != "false") {
                                                        $start_date = $s_date->format('Y-m-d');
                                                    }
                                                }

                                                $end_date = "";
                                                if (null != $customerDetails['profile_data']['end_date']) {
                                                    $e_date = Carbon::createFromFormat('d/m/Y', $customerDetails['profile_data']['end_date']);
                                                    if ($end_date != "false") {
                                                        $end_date = $e_date->format('Y-m-d');
                                                    }
                                                }

                                                $amount_paid = "0";
                                                $total_amount = (null != $packageData['total_amount']) ? $packageData['total_amount'] : 0;
                                                $amount_remaining = (null != $packageData['total_amount_remaining']) ? $packageData['total_amount_remaining'] : 0;
                                                if ($total_amount > 0 && $amount_remaining >= 0 && $total_amount >= $amount_remaining) {
                                                    $amount_paid = $total_amount - $amount_remaining;
                                                }
                                                $package_id = DB::table('member_packages')->insertGetId([
                                                    'member_id' => $memberData['id'],
                                                    'crm_package_id' => $i,
                                                    'package_title' => $package_name,
                                                    'start_date' => $start_date,
                                                    'end_date' => $end_date,
                                                    'total_payment' => $total_amount,
                                                    'payment_made' => $amount_paid,
                                                    'created_by' => $created_by,
                                                    'created_at' => Carbon::now(),
                                                    'updated_at' => Carbon::now()
                                                ]);

                                                foreach ($packageData['services'] as $k => $services) {
                                                    $services['service_validity'] = ($services['service_validity'] == null) ? "" : $services['service_validity'];
                                                    $crm_service_id = $i . '_' . $k;
                                                    if (null !== $services['service_start_date']) {
                                                        $serviceStartDate = Carbon::createFromFormat('d/m/Y', $services['service_start_date']);
                                                        $serviceEndDate = clone $serviceStartDate;
                                                        $serviceEndDate->addDays($services['service_validity']);
                                                        $serviceEndDate = $serviceEndDate->format('Y-m-d');
                                                        $serviceStartDate = $serviceStartDate->format('Y-m-d');
                                                    } else {
                                                        $serviceStartDate = "";
                                                        $serviceEndDate = "";
                                                    }
                                                    DB::table('member_package_services')->insert([
                                                        'package_id' => $package_id,
                                                        'member_id' => $memberData['id'],
                                                        'crm_service_id' => $crm_service_id,
                                                        'service_name' => $services['service_name'],
                                                        'service_validity' => $services['service_validity'],
                                                        'services_booked' => $services['total_services'],
                                                        'services_consumed' => $services['services_consumed'],
                                                        'start_date' => $serviceStartDate,
                                                        'end_date' => $serviceEndDate,
                                                        'created_by' => $created_by,
                                                        'created_at' => Carbon::now(),
                                                        'updated_at' => Carbon::now()
                                                    ]);
                                                }
                                            }
                                        } else {
                                            if (!empty($packageData['services'])) {
                                                foreach ($packageData['services'] as $services) {
                                                    array_push($package_name, $services['service_name']);
                                                }

                                                if (count($package_name) > 1) {
                                                    $package_name_string = implode(' + ', $package_name);
                                                } else {
                                                    $package_name_string = implode('', $package_name);
                                                }
                                                $package_name = $package_name_string;

                                                $start_date = "";
                                                if (null != $customerDetails['profile_data']['start_date']) {
                                                    $s_date = Carbon::createFromFormat('d/m/Y', $customerDetails['profile_data']['start_date']);
                                                    if ($start_date != "false") {
                                                        $start_date = $s_date->format('Y-m-d');
                                                    }
                                                }

                                                $end_date = "";
                                                if (null != $customerDetails['profile_data']['end_date']) {
                                                    $e_date = Carbon::createFromFormat('d/m/Y', $customerDetails['profile_data']['end_date']);
                                                    if ($end_date != "false") {
                                                        $end_date = $e_date->format('Y-m-d');
                                                    }
                                                }

                                                $amount_paid = "0";
                                                $total_amount = (null != $packageData['total_amount']) ? $packageData['total_amount'] : 0;
                                                $amount_remaining = (null != $packageData['total_amount_remaining']) ? $packageData['total_amount_remaining'] : 0;
                                                if ($total_amount > 0 && $amount_remaining >= 0 && $total_amount >= $amount_remaining) {
                                                    $amount_paid = $total_amount - $amount_remaining;
                                                }

                                                DB::table('member_packages')->where('crm_package_id', $i)->update([
                                                    'package_title' => $package_name,
                                                    'start_date' => $start_date,
                                                    'end_date' => $end_date,
                                                    'total_payment' => $amount_paid,
                                                    'payment_made' => $amount_paid,
                                                    'updated_by' => $created_by,
                                                    'updated_at' => Carbon::now()
                                                ]);
                                                foreach ($packageData['services'] as $k => $services) {
                                                    $services['service_validity'] = ($services['service_validity'] == null) ? "" : $services['service_validity'];
                                                    $crm_service_id = $i . '_' . $k;
                                                    $checkService = MemberPackageServices::select('id')->where('member_id', $memberData['id'])->where('crm_service_id', $crm_service_id)->first();
                                                    if (null != $services['service_start_date']) {
                                                        $serviceStartDate = Carbon::createFromFormat('d/m/Y', $services['service_start_date']);
                                                        $serviceEndDate = clone $serviceStartDate;
                                                        $serviceEndDate->addDays($services['service_validity']);
                                                        $serviceEndDate = $serviceEndDate->format('Y-m-d');
                                                        $serviceStartDate = $serviceStartDate->format('Y-m-d');
                                                    } else {
                                                        $serviceStartDate = "";
                                                        $serviceEndDate = "";
                                                    }
                                                    if (empty($checkService)) {

                                                        DB::table('member_package_services')->insert([
                                                            'package_id' => $checkMember['id'],
                                                            'member_id' => $memberData['id'],
                                                            'crm_service_id' => $crm_service_id,
                                                            'service_name' => $services['service_name'],
                                                            'service_validity' => $services['service_validity'],
                                                            'services_booked' => $services['total_services'],
                                                            'services_consumed' => $services['services_consumed'],
                                                            'start_date' => $serviceStartDate,
                                                            'end_date' => $serviceEndDate,
                                                            'created_by' => $created_by,
                                                            'created_at' => Carbon::now(),
                                                            'updated_at' => Carbon::now()
                                                        ]);
                                                    } else {
                                                        DB::table('member_package_services')->where('crm_service_id', $crm_service_id)->update([
                                                            'service_name' => $services['service_name'],
                                                            'service_validity' => $services['service_validity'],
                                                            'services_booked' => $services['total_services'],
                                                            'services_consumed' => $services['services_consumed'],
                                                            'start_date' => $serviceStartDate,
                                                            'end_date' => $serviceEndDate,
                                                            'updated_by' => $created_by,
                                                            'updated_at' => Carbon::now()
                                                        ]);
                                                    }
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                            $customerCount++;
                        }
                    }
                    $result = "success";
                    $msg = $customerCount . " customer(s) imported successfully";
                } else {
                    $result = "error";
                    $msg = "Customer import skipped (too many results).";
                }
            }
            $crmCenterId = ($responseData['response']['DieticianDetails']['center_id']) ? $responseData['response']['DieticianDetails']['center_id'] : "";
            $repoResponse = ["status" => $result, "message" => $msg, "crm_center_id" => $crmCenterId];
        } catch (ClientException $e) {
            //$exceptionMessage= Psr7\str($e->getRequest());
            $exceptionMessage = Psr7\str($e->getResponse());
            $repoResponse = ["status" => "error", "message" => $exceptionMessage];
        } catch (Exception $exception) {
            $exceptionMessage = json_decode($exception->getResponse()->getBody()->getContents(), true);
            $repoResponse = ["status" => "error", "message" => $exceptionMessage];
        }
        return $repoResponse;
    }

    public function importMemberData($params) {
        $client = new Client(); //GuzzleHttp\Client
        if (config('app.env') == 'production') {
            $crmBaseUrl = config('admin.crm_base_url_prod');
        } else {
            $crmBaseUrl = config('admin.crm_base_url_dev');
        }        
        $guzzleDebugMode = false;
        if (config('app.timezone') == 'Asia/Dubai') {
            $guzzleDebugMode = true;
        }
        try {
            $msg = "Customer data import skipped.";
            $result = "success";
            $customerCount = 0;
            $response = $client->request('POST', $crmBaseUrl . 'getDietitianCustomerDetails?username=' . $params['username'], array(
                //'timeout' => 600 * 30, //10 minutes
                'connection_timeout' => 30, //10 minutes
                'debug' => false
            ));
            $responseData = json_decode($response->getBody(), true);
            if (!empty($responseData['response']['Customer'])) {
                $file = fopen("/var/www/html/vlcc-admin/public/dataimported/" . $responseData['response']['DieticianDetails']['center_id'] . "-" . $params['username'] . ".txt", "w");
                fwrite($file, json_encode($responseData['response']['Customer']));
                fclose($file);
                $msg = "File saved successfully.";
            } else {
                $result = "error";
                $msg = "No customers found for this user.";
            }
            $crmCenterId = ($responseData['response']['DieticianDetails']['center_id']) ? $responseData['response']['DieticianDetails']['center_id'] : "";
            $repoResponse = ["status" => $result, "message" => $msg, "crm_center_id" => $crmCenterId];
        } catch (GuzzleException $exception) {
            if (null !== ($exception->getResponse())) {
                $exceptionMessage = json_decode($exception->getResponse()->getBody()->getContents(), true);
                $repoResponse = ["status" => "error", "message" => $exceptionMessage["Message"]];
            } else {
                $repoResponse = ["status" => "error", "message" => "Something went wrong."];
            }
        } catch (ClientException $e) {
            //$exceptionMessage= Psr7\str($e->getRequest());
            $exceptionMessage = Psr7\str($e->getResponse());
            $repoResponse = ["status" => "error", "message" => $exceptionMessage];
        } catch (Exception $exception) {
            $exceptionMessage = json_decode($exception->getResponse()->getBody()->getContents(), true);
            $repoResponse = ["status" => "error", "message" => $exceptionMessage];
        }
        return $repoResponse;
    }

    public function getMemberpackages($params) {
        $response = MemberPackage::orderBY('id')->whereMemberId($params['member_id'])->lists('package_title', 'id');
        return $response;
    }

    // Function to get Members Latest Package
    public function getMemberLatestPackage($member_id) {
        $response = MemberPackage::orderBY('end_date', 'desc')->whereMemberId($member_id)->get();
        return $response;
    }

    // Function to get Members Latest weight
    public function getMemberLatestWeight($memberId) {
        $response = MemberSessionRecord::orderBY('recorded_date', 'DESC')->whereMemberId($memberId)->first();
        $response = (!empty($response) && isset($response["after_weight"])) ? $response["after_weight"] : 0;
        return $response;
    }

    /**
     * get the count for dashboard
     * @param type $params
     * @return type
     */
    public function dataCount($params = []) {
        $response = '';
        if ($params['user_type_id'] == 4) { //Dietician & Slimming Head
            //$response = Member::where('dietician_username', $params['user_name'])->where('status', 1)->count();
            // Get Count of those members for whom dietician_username is empty
            $userInfoHelper = new UserInfoHelper();
            $user_center = $userInfoHelper->getLoggedInUserCenter($params["user_id"]);
            $first = Member::orderBy('first_name')->where('dietician_username', $params['user_name'])->where('status', 1)->get();
            $second = Member::select('members.*')->with('Centers')->orderBy('first_name')->where('dietician_username', '')->where('crm_center_id', $user_center[0]['crm_center_id'])->where('status', 1)->get();
            $response = (count($first->merge($second))) ? count($first->merge($second)) : ''; // Contains foo and bar.          
        } elseif ($params['user_type_id'] == 7 || $params['user_type_id'] == 9 || $params['user_type_id'] == 5 || $params['user_type_id'] == 8) { // ATH, Center Head, Physiotherapist
            $response = DB::table('members')
                    ->join('vlcc_centers', 'members.crm_center_id', '=', 'vlcc_centers.crm_center_id')
                    ->join('admin_centers', 'vlcc_centers.id', '=', 'admin_centers.center_id')
                    ->where('admin_centers.user_id', '=', $params['user_id'])
                    ->where('members.status', '=', 1)
                    ->count();
        } else {
            $response = collect();
        }
        return $response;
    }

    // Function To get Member Packages with Services
    public function getMemberpackagesWithServices($memberId) {
        $response = MemberPackage::has('Services')->with('Services')->orderBy('id', 'ASC')->where('member_id', $memberId)->get();
        return $response;
    }

    // Function To get Count of Successful Customers
    public function getSuccessfulCustomerCount($params) {
        $week = $this->getLastWeekStartEndDate();
        $userInfoHelper = new UserInfoHelper();
        if ($params["user_type_id"] == 4 || $params["user_type_id"] == 8 || $params["user_type_id"] == 5) {
            // If logged_in user is Dietician or Physiotherapist or Slimming Head
            $condition = "AND member.dietician_username='" . $params["user_name"] . "'";
        } elseif ($params["user_type_id"] == 7) {
            // If logged_in user is Centre Head
            $user_center = $userInfoHelper->getLoggedInUserCenter($params["user_id"]);
            $crm_center_id = isset($user_center[0]["crm_center_id"]) ? $user_center[0]["crm_center_id"] : 0;
            $condition = "AND member.crm_center_id='" . $crm_center_id . "'";
        } elseif ($params["user_type_id"] == 9) {
            // If logged_in user is ATH
            $user_center = $userInfoHelper->getLoggedInUserCenter($params["user_id"]);
            $prefix = $centerIds = '';
            foreach ($user_center as $key) {
                $centerIds .= $prefix . '"' . $key["crm_center_id"] . '"';
                $prefix = ', ';
            }
            $condition = "AND member.crm_center_id IN ('" . $centerIds . "')";
        } else {
            $condition = "AND 1=1";
        }
        $result = DB::select("SELECT count(summary.id) AS cnt, summary.member_id FROM member_session_record_summary summary INNER JOIN members member ON summary.member_id = member.id WHERE (summary.recorded_date BETWEEN '" . $week["start_day"] . "' AND '" . $week["end_day"] . "') AND summary.net_weight_loss >= 1 AND member.status=1 " . $condition . " GROUP BY summary.member_id");
        $result = json_decode(json_encode($result), true);
        $result = array_column($result, 'member_id');
        return $result;
    }

    // Function To get Count of Regular Customers
    public function getRegularCustomerCount($params) {
        $week = $this->getLastWeekStartEndDate();
        $userInfoHelper = new UserInfoHelper();
        if ($params["user_type_id"] == 4 || $params["user_type_id"] == 8 || $params["user_type_id"] == 5) {
            // If logged_in user is Dietician or Physiotherapist or Slimming Head
            $condition = "AND member.dietician_username='" . $params["user_name"] . "'";
        } elseif ($params["user_type_id"] == 7) {
            // If logged_in user is Centre Head
            $user_center = $userInfoHelper->getLoggedInUserCenter($params["user_id"]);
            $crm_center_id = isset($user_center[0]["crm_center_id"]) ? $user_center[0]["crm_center_id"] : 0;
            $condition = "AND member.crm_center_id='" . $crm_center_id . "'";
        } elseif ($params["user_type_id"] == 9) {
            // If logged_in user is ATH
            $user_center = $userInfoHelper->getLoggedInUserCenter($params["user_id"]);
            $prefix = $centerIds = '';
            foreach ($user_center as $key) {
                $centerIds .= $prefix . '"' . $key["crm_center_id"] . '"';
                $prefix = ', ';
            }
            $condition = "AND member.crm_center_id IN ('" . $centerIds . "')";
        } else {
            $condition = "AND 1=1";
        }
        $result = DB::select("SELECT count(session.id) AS session_count, session.member_id FROM member_session_bookings session INNER JOIN members member ON session.member_id = member.id WHERE (session.session_date BETWEEN '" . $week["start_day"] . "' AND '" . $week["end_day"] . "') AND session.status = 5  " . $condition . " GROUP BY session.member_id HAVING session_count >= 3");
        $result = json_decode(json_encode($result), true);
        $result = array_column($result, 'member_id');
        return $result;
    }

    // Function To get Count of Un Successful Customers
    public function getUnsuccessfulCustomerCount($params) {
        $week = $this->getLastWeekStartEndDate();
        $userInfoHelper = new UserInfoHelper();
        if ($params["user_type_id"] == 4 || $params["user_type_id"] == 8 || $params["user_type_id"] == 5) {
            // If logged_in user is Dietician or Physiotherapist or Slimming Head
            $condition = "AND member.dietician_username='" . $params["user_name"] . "'";
        } elseif ($params["user_type_id"] == 7) {
            // If logged_in user is Centre Head
            $user_center = $userInfoHelper->getLoggedInUserCenter($params["user_id"]);
            $crm_center_id = isset($user_center[0]["crm_center_id"]) ? $user_center[0]["crm_center_id"] : 0;
            $condition = "AND member.crm_center_id='" . $crm_center_id . "'";
        } elseif ($params["user_type_id"] == 9) {
            // If logged_in user is ATH
            $user_center = $userInfoHelper->getLoggedInUserCenter($params["user_id"]);
            $prefix = $centerIds = '';
            foreach ($user_center as $key) {
                $centerIds .= $prefix . '"' . $key["crm_center_id"] . '"';
                $prefix = ', ';
            }
            $condition = "AND member.crm_center_id IN ('" . $centerIds . "')";
        } else {
            $condition = "AND 1=1";
        }
        $result = DB::select("SELECT count(summary.id) AS cnt, summary.member_id FROM member_session_record_summary summary INNER JOIN members member ON summary.member_id = member.id WHERE (summary.recorded_date BETWEEN '" . $week["start_day"] . "' AND '" . $week["end_day"] . "') AND summary.net_weight_loss < 1 AND member.status=1 " . $condition . " GROUP BY summary.member_id");
        $result = json_decode(json_encode($result), true);
        $result = array_column($result, 'member_id');
        return $result;
    }

    // Function To get Count of Irregular Customers
    public function getIrregularCustomerCount($params) {
        $week = $this->getLastWeekStartEndDate();
        $userInfoHelper = new UserInfoHelper();
        if ($params["user_type_id"] == 4 || $params["user_type_id"] == 8 || $params["user_type_id"] == 5) {
            // If logged_in user is Dietician or Physiotherapist or Slimming Head
            $condition = "AND member.dietician_username='" . $params["user_name"] . "'";
        } elseif ($params["user_type_id"] == 7) {
            // If logged_in user is Centre Head
            $user_center = $userInfoHelper->getLoggedInUserCenter($params["user_id"]);
            $crm_center_id = isset($user_center[0]["crm_center_id"]) ? $user_center[0]["crm_center_id"] : 0;
            $condition = "AND member.crm_center_id='" . $crm_center_id . "'";
        } elseif ($params["user_type_id"] == 9) {
            // If logged_in user is ATH
            $user_center = $userInfoHelper->getLoggedInUserCenter($params["user_id"]);
            $prefix = $centerIds = '';
            foreach ($user_center as $key) {
                $centerIds .= $prefix . '"' . $key["crm_center_id"] . '"';
                $prefix = ', ';
            }
            $condition = "AND member.crm_center_id IN ('" . $centerIds . "')";
        } else {
            $condition = "AND 1=1";
        }
        $result = DB::select("SELECT count(session.id) AS session_count, session.member_id FROM member_session_bookings session INNER JOIN members member ON session.member_id = member.id WHERE (session.session_date BETWEEN '" . $week["start_day"] . "' AND '" . $week["end_day"] . "') AND session.status = 5  " . $condition . " GROUP BY session.member_id HAVING session_count < 3");
        $result = json_decode(json_encode($result), true);
        $result = array_column($result, 'member_id');
        return $result;
    }

    // Function to get Start date & end date of last week
    public function getLastWeekStartEndDate() {
        $previous_week = strtotime("-1 week +1 day");

        $start_week = strtotime("last monday midnight", $previous_week);
        $end_week = strtotime("next sunday", $start_week);

        $start_week = date("Y-m-d", $start_week);
        $end_week = date("Y-m-d", $end_week);

        $last_week["start_day"] = $start_week;
        $last_week["end_day"] = $end_week;

        return $last_week;
    }

    // Function to check if Member is Regular or not
    public function checkRegularMember($memberId) {
        $isRegular = 0;
        $week = $this->getLastWeekStartEndDate();
        $result = DB::select("SELECT IFNULL(COUNT(id),0) as session_count FROM member_session_bookings WHERE member_id = " . $memberId . "  AND session_date BETWEEN '" . $week["start_day"] . "' AND '" . $week["end_day"] . "'");
        $result = json_decode(json_encode($result), true);
        $result = array_column($result, 'session_count');
        if (isset($result[0]) && $result[0] >= 3) {
            $isRegular = 1;
        }
        return $isRegular;
    }

    // Function to check if Member is Successful or not
    public function checkSuccessfulMember($memberId) {
        $isSuccessful = 0;
        $week = $this->getLastWeekStartEndDate();
        $result = DB::select("SELECT IFNULL(COUNT(id),0) as summary_count FROM member_session_record_summary WHERE member_id = " . $memberId . "  AND recorded_date BETWEEN '" . $week["start_day"] . "' AND '" . $week["end_day"] . "' AND net_weight_loss >=1");
        $result = json_decode(json_encode($result), true);
        $result = array_column($result, 'summary_count');
        if (isset($result[0]) && $result[0] >= 1) {
            $isSuccessful = 1;
        }
        return $isSuccessful;
    }
    
    //Function to display dieticians dropdown    
    public function dataMemberDetails($request, $params = []) {
        $memberId = $params['id'];
        $response = '';       
       //$result = DB::select("SELECT CONCAT(admins.first_name, ' ',admins.last_name) as AdminFullName, admins.id, admins.user_type_id, admins.username, CONCAT(members.first_name, ' ', members.last_name) as memFullName, members.mobile_number,members.dietician_username, vlcc_centers.center_name from admins, members,vlcc_centers ,admin_centers WHERE admins.user_type_id = 4 AND members.crm_center_id = vlcc_centers.crm_center_id AND vlcc_centers.id = admin_centers.center_id AND admin_centers.user_id = admins.id AND members.id = ".$memberId);        
       $result = DB::select("SELECT CONCAT(admins.first_name, ' ',admins.last_name) as AdminFullName, admins.id, admins.user_type_id, admins.username, CONCAT(members.first_name, ' ', members.last_name) as memFullName, members.mobile_number,members.dietician_username, vlcc_centers.center_name FROM members LEFT OUTER JOIN vlcc_centers ON members.crm_center_id = vlcc_centers.crm_center_id LEFT OUTER JOIN admin_centers ON vlcc_centers.id = admin_centers.center_id LEFT OUTER JOIN admins ON admin_centers.user_id = admins.id WHERE (admins.user_type_id=4 or admins.user_type_id=8) AND admins.status=1 AND members.id=".$memberId);
         return collect($result);
    }

    //Function to get existing dietician to display as selected in the dropdown
    
    public function getExistingDietician($memberId)  {
         $memberId = (int) $memberId;
         $result = DB::select("SELECT CONCAT(admins.first_name, ' ',admins.last_name) as AdminFullName, admins.id, admins.username, members.dietician_username from admins, members where members.dietician_username = admins.username AND admins.user_type_id = 4 AND members.id = ".$memberId);
         return collect($result);
    }

    //Function to edit dietician in database
    
    public function editMemberDetails($inputs, $params = []){
        try{
            $memberId = $params['member_id'];
            $dieticianId=$params['dietician_id'];
            
//            if(isset($params['dietician_id']) && !empty($params['dietician_id'])){
//               //dd($dieticianId=$params['dietician_id']);
//            }
            
            $result = DB::select("SELECT admins.username from admins where admins.id =".$dieticianId);
            $responsee=collect($result)->toArray();
            $response1=($responsee[0]->username);

            DB::table('members')->where('id', $memberId)->update(['dietician_username' => $response1,'updated_at' => Carbon::now()]);
            $response['status'] = 'success';
            $response['message'] = trans('admin::messages.updated', ['name' => trans('admin::controller/member.member')]);

            return $response;
        
        }
        catch (Exception $ex){
            $exceptionDetails = $e->getMessage();
            $response['status'] = 'error';
            $response['message'] = trans('admin::messages.not-updated', ['name' => trans('admin::controller/member.member')]) . "<br /><b> Error Details</b> - " . $exceptionDetails;
            Log::error(trans('admin::messages.not-updated', ['name' => trans('admin::controller/member.member')]), ['Error Message' => $exceptionDetails, 'Current Action' => Route::getCurrentRoute()->getActionName()]);

            return $response;
        }
    }
}
