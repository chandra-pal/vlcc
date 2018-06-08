<?php

/**
 * The repository class for managing food specific actions.
 *
 *
 * @author Gauri Deshmukh <gaurid@iprogrammer.com>
 * @package Admin
 * @since 1.0
 */

namespace Modules\Admin\Repositories;

use Modules\Admin\Models\Food;
use Modules\Admin\Models\Center;
use Modules\Admin\Models\User;
use Exception;
use Route;
use Log;
use Cache;
use DB;
use PDO;
use Auth;

class ReportsRepository extends BaseRepository {

    /**
     * Create a new FoodRepository instance.
     *
     * @param  Modules\Admin\Models\Food $model
     * @return void
     */
    public function __construct() {

        //$this->model = $food;
    }

    /**
     * Get a listing of the resource.
     *
     * @return Response
     */
    public function data($params) {
//        return $detailedSales = DB::table('admins')
//            ->Join('admin_centers', 'admins.id', '=', 'admin_centers.user_id')
//            ->Join('admin_centers', 'admins.id', '=', 'admin_centers.user_id')
//            ->Join('vlcc_centers', 'vlcc_centers.id', '=', 'admin_centers.center_id')
//            ->Join('cities', 'cities.id', '=', 'vlcc_centers.city_id');
        if ($params["center_id"] != 0) {
            return DB::table('admins')
                            ->select(DB::raw('CONCAT(admins.first_name, " ", admins.last_name) AS fullname'), 'admins.contact', 'admins.username', 'vlcc_centers.center_name', 'cities.name', 'user_types.name AS designation')
                            ->Join('admin_centers', 'admins.id', '=', 'admin_centers.user_id')
                            ->Join('user_types', 'admins.user_type_id', '=', 'user_types.id')
                            ->Join('vlcc_centers', 'vlcc_centers.id', '=', 'admin_centers.center_id')
                            ->Join('cities', 'cities.id', '=', 'vlcc_centers.city_id')
                            ->where('vlcc_centers.id', '=', $params['center_id'])
                            ->where('admins.status', '=', 1)
                            ->orderBy('cities.name')
                            ->orderBy('vlcc_centers.area')
                            ->orderBy(DB::raw('CONCAT(admins.first_name, " ", admins.last_name)'));
        } else {
            return DB::table('admins')
                            ->select(DB::raw('CONCAT(admins.first_name, " ", admins.last_name) AS fullname'), 'admins.contact', 'admins.username', 'vlcc_centers.center_name', 'cities.name', 'user_types.name AS designation')
                            ->Join('admin_centers', 'admins.id', '=', 'admin_centers.user_id')
                            ->Join('user_types', 'admins.user_type_id', '=', 'user_types.id')
                            ->Join('vlcc_centers', 'vlcc_centers.id', '=', 'admin_centers.center_id')
                            ->Join('cities', 'cities.id', '=', 'vlcc_centers.city_id')
                            ->where('vlcc_centers.city_id', '=', $params['city_id'])
                            ->where('admins.status', '=', 1)
                            ->orderBy('cities.name')
                            ->orderBy('vlcc_centers.area')
                            ->orderBy(DB::raw('CONCAT(admins.first_name, " ", admins.last_name)'));
        }
    }

    public function getCenterwiseLogin($params) {
        if ($params["city_id"] == 0) {
            return DB::table('admins')
                            ->select(DB::raw('CONCAT(admins.first_name, " ", admins.last_name) AS fullname'), DB::raw('COUNT(login_logs.id) AS login_count'), 'admins.contact', 'admins.username', 'login_logs.ip_address', 'login_logs.last_access_time AS last_login', DB::raw('MAX(login_logs.in_time) AS last_login_datetime'), 'vlcc_centers.center_name', 'cities.name', 'user_types.name AS designation')
                            ->Join('admin_centers', 'admins.id', '=', 'admin_centers.user_id')
                            ->Join('user_types', 'admins.user_type_id', '=', 'user_types.id')
                            ->Join('vlcc_centers', 'vlcc_centers.id', '=', 'admin_centers.center_id')
                            ->Join('cities', 'cities.id', '=', 'vlcc_centers.city_id')
                            ->Join('login_logs', 'admin_centers.user_id', '=', 'login_logs.user_id')
                            //->where('vlcc_centers.city_id', '=', $params['city_id'])
                            ->where('admins.status', '=', 1)
                            ->groupBy('vlcc_centers.center_name', 'login_logs.user_id')
                            ->orderBy('cities.name')
                            ->orderBy('vlcc_centers.area')
                            ->orderBy(DB::raw('CONCAT(admins.first_name, " ", admins.last_name)'));
        } else {
            if ($params["center_id"] != 0) {
                return DB::table('admins')
                                ->select(DB::raw('CONCAT(admins.first_name, " ", admins.last_name) AS fullname'), DB::raw('COUNT(login_logs.id) AS login_count'), 'admins.contact', 'admins.username', 'login_logs.ip_address', 'login_logs.last_access_time AS last_login', DB::raw('MAX(login_logs.in_time) AS last_login_datetime'), 'vlcc_centers.center_name', 'cities.name', 'user_types.name AS designation')
                                ->Join('admin_centers', 'admins.id', '=', 'admin_centers.user_id')
                                ->Join('user_types', 'admins.user_type_id', '=', 'user_types.id')
                                ->Join('vlcc_centers', 'vlcc_centers.id', '=', 'admin_centers.center_id')
                                ->Join('cities', 'cities.id', '=', 'vlcc_centers.city_id')
                                ->Join('login_logs', 'admin_centers.user_id', '=', 'login_logs.user_id')
                                ->where('vlcc_centers.id', '=', $params['center_id'])
                                ->where('admins.status', '=', 1)
                                ->groupBy('vlcc_centers.center_name', 'login_logs.user_id')
                                ->orderBy('cities.name')
                                ->orderBy('vlcc_centers.area')
                                ->orderBy(DB::raw('CONCAT(admins.first_name, " ", admins.last_name)'));
            } else {
                return DB::table('admins')

                        ->select(DB::raw('CONCAT(admins.first_name, " ", admins.last_name) AS fullname'), DB::raw('COUNT(login_logs.id) AS login_count'), 'admins.contact', 'admins.username', 'login_logs.ip_address', 'login_logs.last_access_time AS last_login', DB::raw('MAX(login_logs.in_time) AS last_login_datetime'), 'vlcc_centers.center_name', 'cities.name', 'user_types.name AS designation')
                        ->Join('admin_centers', 'admins.id', '=', 'admin_centers.user_id')
                        ->Join('user_types', 'admins.user_type_id', '=', 'user_types.id')
                        ->Join('vlcc_centers', 'vlcc_centers.id', '=', 'admin_centers.center_id')
                        ->Join('cities', 'cities.id', '=', 'vlcc_centers.city_id')
                        ->Join('login_logs', 'admin_centers.user_id', '=', 'login_logs.user_id')
                        ->where('vlcc_centers.city_id', '=', $params['city_id'])
                        ->where('admins.status', '=', 1)
                        ->groupBy('vlcc_centers.center_name', 'login_logs.user_id')
                        ->orderBy('cities.name')
                        ->orderBy('vlcc_centers.area')
                        ->orderBy(DB::raw('CONCAT(admins.first_name, " ", admins.last_name)'));
        }
    }
    }
    
    public function getCenterList() {
        return Center::lists('center_name', 'id');
    }

    public function getCityList() {
        DB::setFetchMode(PDO::FETCH_KEY_PAIR);
        $result = DB::select("SELECT vlcc_centers.city_id, cities.name FROM vlcc_centers INNER JOIN cities ON vlcc_centers.city_id= cities.id ORDER BY cities.name");
        DB::setFetchMode(PDO::FETCH_CLASS);
        return collect($result);
    }

    public function getCenterwiseCustomers($params) {
        $params['logged_in_user_type'] = Auth::guard('admin')->user()->userType->id;
        $params['logged_in_user_id'] = Auth::guard('admin')->user()->id;
        if ($params["center_id"] != 0) {
            if ($params["logged_in_user_type"] == 4 || $params["logged_in_user_type"] == 8 || $params["logged_in_user_type"] == 5) {
                $result = DB::table('admins')
                        ->select(DB::raw('CONCAT(admins.first_name, " ", admins.last_name) AS dietician_name'), DB::raw('CONCAT(members.first_name, " ", members.last_name) AS customer_name'), 'user_types.name AS designation', 'members.id as member_id', 'members.mobile_number', 'admins.username as dietician_username', 'members.crm_center_id', 'admins.id AS dietician_id', 'vlcc_centers.center_name', 'cities.name')
                        ->Join('admin_centers', 'admins.id', '=', 'admin_centers.user_id')
                        ->Join('user_types', 'admins.user_type_id', '=', 'user_types.id')
                        ->Join('members', 'admins.username', '=', 'members.dietician_username')
                        ->Join('vlcc_centers', 'admin_centers.center_id', '=', 'vlcc_centers.id')
                        ->Join('cities', 'cities.id', '=', 'vlcc_centers.city_id')
                        ->where('admin_centers.center_id', '=', $params['center_id'])
                        ->where('members.status', '=', 1)
                        ->where('admins.status', '=', 1)
                        ->where('admins.id', '=', $params['logged_in_user_id'])
                        ->orderBy('cities.name')
                        ->orderBy('vlcc_centers.area')
                        ->orderBy(DB::raw('CONCAT(admins.first_name, " ", admins.last_name)'))
                        ->orderBy(DB::raw('CONCAT(members.first_name, " ", members.last_name)'));
            } else {
                $result = DB::table('admins')
                    ->select(DB::raw('CONCAT(admins.first_name, " ", admins.last_name) AS dietician_name'), DB::raw('CONCAT(members.first_name, " ", members.last_name) AS customer_name'), 'user_types.name AS designation', 'members.id as member_id', 'members.mobile_number', 'admins.username as dietician_username', 'members.crm_center_id', 'vlcc_centers.center_name', 'cities.name')
                    ->Join('admin_centers', 'admins.id', '=', 'admin_centers.user_id')
                    ->Join('user_types', 'admins.user_type_id', '=', 'user_types.id')
                    ->Join('members', 'admins.username', '=', 'members.dietician_username')
                    ->Join('vlcc_centers', 'admin_centers.center_id', '=', 'vlcc_centers.id')
                    ->Join('cities', 'cities.id', '=', 'vlcc_centers.city_id')
                    ->where('admin_centers.center_id', '=', $params['center_id'])
                    ->where('members.status', '=', 1)
                    ->where('admins.status', '=', 1)
                    ->orderBy('cities.name')
                    ->orderBy('vlcc_centers.area')
                    ->orderBy(DB::raw('CONCAT(admins.first_name, " ", admins.last_name)'))
                    ->orderBy(DB::raw('CONCAT(members.first_name, " ", members.last_name)'));

            }
            return $result;
        } else {
            return DB::table('vlcc_centers')

                    ->select(DB::raw('CONCAT(admins.first_name, " ", admins.last_name) AS dietician_name'), DB::raw('CONCAT(members.first_name, " ", members.last_name) AS customer_name'), 'user_types.name AS designation', 'members.id as member_id', 'members.mobile_number', 'admins.username as dietician_username', 'members.crm_center_id', 'vlcc_centers.center_name', 'cities.name')
                    ->Join('admin_centers', 'vlcc_centers.id', '=', 'admin_centers.center_id')
                    ->Join('admins', 'admin_centers.user_id', '=', 'admins.id')
                    ->Join('user_types', 'admins.user_type_id', '=', 'user_types.id')
                    ->Join('members', 'admins.username', '=', 'members.dietician_username')
                    ->Join('cities', 'cities.id', '=', 'vlcc_centers.city_id')
                    ->where('vlcc_centers.city_id', '=', $params['city_id'])
                    ->where('members.status', '=', 1)
                    ->where('admins.status', '=', 1)
                    ->orderBy('cities.name')
                    ->orderBy('vlcc_centers.area')
                    ->orderBy(DB::raw('CONCAT(admins.first_name, " ", admins.last_name)'))
                    ->orderBy(DB::raw('CONCAT(members.first_name, " ", members.last_name)'));
        }
    }

    public function getCityWiseCenters($cityId) {
        DB::setFetchMode(PDO::FETCH_KEY_PAIR);
        $result = DB::select("SELECT id, center_name FROM vlcc_centers WHERE city_id=" . $cityId . " ORDER BY center_name");
        DB::setFetchMode(PDO::FETCH_CLASS);
        return collect($result);
    }

    public function getNewUsers($params) {
        return DB::table('member_otp')
                        ->select('member_otp.mobile_number', 'member_otp.created_at', 'members.first_name', 'members.last_name')
                        ->Join('members', 'members.mobile_number', '=', 'member_otp.mobile_number')
                        ->where('member_otp.created_at', '>=', $params['date'] . ' 00:00:00')
                        ->where('member_otp.created_at', '<=', $params['date'] . ' 23:59:59')
                        ->whereNotIn('member_otp.mobile_number', function($query) use($params) {
                            $query->select('mobile_number')->from('member_otp')->where('created_at', '<', $params['date'] . ' 00:00:00');
                        })
                        ->where('member_otp.otp_used', '=', 1)
                        ->groupBy('member_otp.mobile_number');
    }

    public function getUserwiseCPRCount($params) {
        if ($params["city_id"] < 0) {
            $condition = " AND vlcc_centers.city_id = 0";
        } else if ($params["city_id"] == 0) {
            $condition = " AND 1=1";
        } else {
            if ($params["center_id"] != 0) {
                //Center is selected (match center_id)
                $condition = " AND admin_centers.center_id = " . $params["center_id"] . "";
            } else {
                //city is selected (match city_id)
                $condition = " AND vlcc_centers.city_id = " . $params["city_id"] . "";
            }
        }
        $result =  DB::table('admins')
                ->select('admins.id', 'admins.username', 'user_types.name AS designation', DB::raw('CONCAT(admins.first_name," ",admins.last_name) as full_name'), DB::raw('IFNULL(COUNT(members.id), 0) AS customer_count'), DB::raw('(SELECT IFNULL(COUNT(DISTINCT(member_session_record.member_id)),0) AS cpr_usage_count FROM member_session_record WHERE member_session_record.created_by=admins.id) as cpr_usage_count'), 'admin_centers.center_id', 'vlcc_centers.center_name', 'vlcc_centers.city_id', 'cities.name AS city_name')
                ->Join('user_types', 'admins.user_type_id', '=', 'user_types.id')
                ->leftJoin('members', 'admins.username', '=', 'members.dietician_username')
                ->leftJoin('admin_centers', 'admins.id', '=', 'admin_centers.user_id')
                ->leftJoin('vlcc_centers', 'admin_centers.center_id', '=', 'vlcc_centers.id')
                ->leftJoin('cities', 'vlcc_centers.city_id', '=', 'cities.id')
                ->whereRaw('(admins.user_type_id=4 OR admins.user_type_id=8) ' . $condition . ' AND admins.status=1')
                ->whereRaw('members.status=1')
                ->groupBy('admins.username')
                ->orderBy(DB::raw('CONCAT(admins.first_name, " ", admins.last_name)'));
        return $result;

        /* $result = DB::select("SELECT admins.id, admins.username, user_types.name AS designation, CONCAT(admins.first_name,' ',admins.last_name) as full_name, IFNULL(COUNT(members.id), 0) AS customer_count, (SELECT IFNULL(COUNT(DISTINCT(member_session_record.member_id)),0) AS cpr_usage_count FROM member_session_record WHERE member_session_record.created_by=admins.id) as cpr_usage_count, admin_centers.center_id, vlcc_centers.center_name, vlcc_centers.city_id, cities.name AS city_name FROM admins INNER JOIN user_types ON admins.user_type_id = user_types.id LEFT OUTER JOIN members ON admins.username = members.dietician_username LEFT OUTER JOIN admin_centers ON admins.id = admin_centers.user_id LEFT OUTER JOIN vlcc_centers ON admin_centers.center_id = vlcc_centers.id LEFT OUTER JOIN cities ON vlcc_centers.city_id = cities.id WHERE (admins.user_type_id=4 OR admins.user_type_id=8) " . $condition . " GROUP BY admins.username ORDER BY full_name ASC"); */
        //return $response = collect($result);
    }

    public function getCenterwiseEscalation($params) {
        if ($params["center_id"] != 0) {
            return DB::table('member_escalation_matrix')
                            ->select('member_escalation_matrix.admin_id AS ATHID', DB::raw('CONCAT(admins.first_name, " ", admins.last_name) AS ATHfullname'), 'member_escalation_matrix.escalation_date as EDate', 'members.first_name AS Memberfullname', 'members.mobile_number', 'vlcc_centers.center_name', 'cities.name', 'member_session_bookings.dietician_id AS DietId', DB::raw('CONCAT(a1.first_name, " ", a1.last_name) AS Dieticianfullname'))
                            ->Join('member_session_bookings', 'member_escalation_matrix.session_id', '=', 'member_session_bookings.id')
                            ->leftJoin('admins', 'member_escalation_matrix.admin_id', '=', 'admins.id')
                            ->leftJoin('admins AS a1', 'member_session_bookings.dietician_id', '=', 'a1.id')
                            ->Join('members', 'member_escalation_matrix.member_id', '=', 'members.id')
                            ->Join('admin_centers', 'admins.id', '=', 'admin_centers.user_id')
                            ->Join('vlcc_centers', 'admin_centers.center_id', '=', 'vlcc_centers.id')
                            ->Join('cities', 'cities.id', '=', 'vlcc_centers.city_id')
                            ->where('vlcc_centers.id', '=', $params['center_id'])
                            ->where('admins.status', '=', 1)
                            ->orderBy('cities.name')
                            ->orderBy('members.first_name');
        } else {
            return DB::table('member_escalation_matrix')
                            ->select('member_escalation_matrix.admin_id AS ATHID', DB::raw('CONCAT(admins.first_name, " ", admins.last_name) AS ATHfullname'), 'member_escalation_matrix.escalation_date as EDate', 'members.first_name AS Memberfullname', 'members.mobile_number', 'vlcc_centers.center_name', 'cities.name', 'member_session_bookings.dietician_id AS DietId', DB::raw('CONCAT(a1.first_name, " ", a1.last_name) AS Dieticianfullname'))
                            ->Join('member_session_bookings', 'member_escalation_matrix.session_id', '=', 'member_session_bookings.id')
                            ->leftJoin('admins', 'member_escalation_matrix.admin_id', '=', 'admins.id')
                            ->leftJoin('admins AS a1', 'member_session_bookings.dietician_id', '=', 'a1.id')
                            ->Join('members', 'member_escalation_matrix.member_id', '=', 'members.id')
                            ->Join('admin_centers', 'admins.id', '=', 'admin_centers.user_id')
                            ->Join('vlcc_centers', 'admin_centers.center_id', '=', 'vlcc_centers.id')
                            ->Join('cities', 'cities.id', '=', 'vlcc_centers.city_id')
                            ->where('vlcc_centers.city_id', '=', $params['city_id'])
                            ->where('admins.status', '=', 1)
                            ->orderBy('cities.name')
                            ->orderBy('members.first_name');
        }
    }
    
     /**
     * Get a listing of the resource.(download centerwise escalation data according to input search parameters)
     *
     * @return Response
     */
    public function searchDataCenterwiseEscalation($params=[]) {
        $ATHfullname = $params['ATHfullname'];
        $Dieticianfullname = $params['Dieticianfullname'];
        $Memberfullname = $params['Memberfullname'];
        $mobile_number = $params['mobile_number'];
        
        if ($params["center_id"] != 0) {
            $condition = "vlcc_centers.id = " . $params["center_id"] . "";
        } else {
            $condition = "vlcc_centers.city_id = " . $params["city_id"] . "";
        }

        if (isset($ATHfullname) && !empty($ATHfullname)) {
            $ATHfullname = " AND (CONCAT(admins.first_name, ' ' ,admins.last_name)) LIKE'%$ATHfullname%'";
        } else {
            $ATHfullname = "";
        }

        if (isset($Dieticianfullname) && !empty($Dieticianfullname)) {
           $Dieticianfullname = " AND (CONCAT(a1.first_name, ' ' ,a1.last_name)) LIKE '%$Dieticianfullname%'";
        } else {
            $Dieticianfullname = "";
        }

        if (isset($Memberfullname) && !empty($Memberfullname)) {
            $Memberfullname = " AND members.first_name LIKE '%$Memberfullname%'";
        } else {
            $Memberfullname = "";
        }

        if (isset($mobile_number) && !empty($mobile_number)) {
            $mobile_number = " AND members.mobile_number = '$mobile_number'";
        } else {
            $mobile_number = "";
        }
        
        return DB::table('member_escalation_matrix')
                            ->select('member_escalation_matrix.admin_id AS ATHID', DB::raw('CONCAT(admins.first_name, " ", admins.last_name) AS ATHfullname'), 'member_escalation_matrix.escalation_date as EDate', 'members.first_name AS Memberfullname', 'members.mobile_number', 'vlcc_centers.center_name', 'cities.name', 'member_session_bookings.dietician_id AS DietId', DB::raw('CONCAT(a1.first_name, " ", a1.last_name) AS Dieticianfullname'))
                            ->Join('member_session_bookings', 'member_escalation_matrix.session_id', '=', 'member_session_bookings.id')
                            ->leftJoin('admins', 'member_escalation_matrix.admin_id', '=', 'admins.id')
                            ->leftJoin('admins AS a1', 'member_session_bookings.dietician_id', '=', 'a1.id')
                            ->Join('members', 'member_escalation_matrix.member_id', '=', 'members.id')
                            ->Join('admin_centers', 'admins.id', '=', 'admin_centers.user_id')
                            ->Join('vlcc_centers', 'admin_centers.center_id', '=', 'vlcc_centers.id')
                            ->Join('cities', 'cities.id', '=', 'vlcc_centers.city_id')
                           ->whereRaw($condition . '' . $ATHfullname . '' . $Dieticianfullname . '' . $Memberfullname . '' . $mobile_number)
                            ->orderBy('cities.name')
                            ->orderBy('members.first_name');
    }
    
    public function getCenterwiseNotification($params) {
        if ($params["center_id"] != 0) {
            return DB::table('member_notifications')
                            ->select('member_notifications.member_id AS MemberID', 'member_notifications.message_type AS NotiType', DB::raw('COUNT(member_notifications.message_type) AS notification_count'), 'members.first_name AS Memberfullname', 'members.mobile_number', 'vlcc_centers.center_name', 'cities.name', 'members.id AS MemberId', DB::raw('CONCAT(admins.first_name, " ", admins.last_name) AS Dieticianfullname'))
                            ->Join('members', 'member_notifications.member_id', '=', 'members.id')
                            ->leftJoin('admins', 'members.dietician_username', '=', 'admins.username')
                            ->Join('admin_centers', 'admins.id', '=', 'admin_centers.user_id')
                            ->Join('vlcc_centers', 'admin_centers.center_id', '=', 'vlcc_centers.id')
                            ->Join('cities', 'cities.id', '=', 'vlcc_centers.city_id')
                            ->where('vlcc_centers.id', '=', $params['center_id'])
                            ->where('admins.status', '=', 1)
                            ->groupBy('member_notifications.member_id', 'member_notifications.message_type')
                            ->orderBy('cities.name')
                            ->orderBy('members.first_name');
        } else {
            return DB::table('member_notifications')
                            ->select('member_notifications.member_id AS MemberID', 'member_notifications.message_type AS NotiType', DB::raw('COUNT(member_notifications.message_type) AS notification_count'), 'members.first_name AS Memberfullname', 'members.mobile_number', 'vlcc_centers.center_name', 'cities.name', 'members.id AS MemberId', DB::raw('CONCAT(admins.first_name, " ", admins.last_name) AS Dieticianfullname'))
                            ->Join('members', 'member_notifications.member_id', '=', 'members.id')
                            ->leftJoin('admins', 'members.dietician_username', '=', 'admins.username')
                            ->Join('admin_centers', 'admins.id', '=', 'admin_centers.user_id')
                            ->Join('vlcc_centers', 'admin_centers.center_id', '=', 'vlcc_centers.id')
                            ->Join('cities', 'cities.id', '=', 'vlcc_centers.city_id')
                            ->where('vlcc_centers.city_id', '=', $params['city_id'])
                            ->where('admins.status', '=', 1)
                            ->groupBy('member_notifications.member_id', 'member_notifications.message_type')
                            ->orderBy('cities.name')
                            ->orderBy('members.first_name');
        }
    }
    
    /**
     * Get a listing of the resource.(download centerwise notification data according to input search parameters)
     *
     * @return Response
     */
    public function searchDataCenterwiseNotification($params = []) {
        $Dieticianfullname = $params['Dieticianfullname'];
        $Memberfullname = $params['Memberfullname'];
        $mobile_number = $params['mobile_number'];
        $NotiType = $params['NotiType'];

        if ($params["center_id"] != 0) {
            $condition = "vlcc_centers.id = " . $params["center_id"] . "";
        } else {
            $condition = "vlcc_centers.city_id = " . $params["city_id"] . "";
        }

        if (isset($Dieticianfullname) && !empty($Dieticianfullname)) {
            $Dieticianfullname = " AND (CONCAT(a1.first_name, ' ' ,a1.last_name)) LIKE '%$Dieticianfullname%'";
        } else {
            $Dieticianfullname = "";
        }

        if (isset($Memberfullname) && !empty($Memberfullname)) {
            $Memberfullname = " AND members.first_name LIKE '%$Memberfullname%'";
        } else {
            $Memberfullname = "";
        }

        if (isset($mobile_number) && !empty($mobile_number)) {
            $mobile_number = " AND members.mobile_number = '$mobile_number'";
        } else {
            $mobile_number = "";
        }

        if (isset($NotiType) && !empty($NotiType)) {
            $NotiType = " AND member_notifications.message_type = '$NotiType'";
        } else {
            $NotiType = "";
        }

        return DB::table('member_notifications')
                        ->select('member_notifications.member_id AS MemberID', 'member_notifications.message_type AS NotiType', DB::raw('COUNT(member_notifications.message_type) AS notification_count'), 'members.first_name AS Memberfullname', 'members.mobile_number', 'vlcc_centers.center_name', 'cities.name', 'members.id AS MemberId', DB::raw('CONCAT(admins.first_name, " ", admins.last_name) AS Dieticianfullname'))
                        ->Join('members', 'member_notifications.member_id', '=', 'members.id')
                        ->leftJoin('admins', 'members.dietician_username', '=', 'admins.username')
                        ->Join('admin_centers', 'admins.id', '=', 'admin_centers.user_id')
                        ->Join('vlcc_centers', 'admin_centers.center_id', '=', 'vlcc_centers.id')
                        ->Join('cities', 'cities.id', '=', 'vlcc_centers.city_id')
                        ->whereRaw($condition . '' . $Dieticianfullname . '' . $Memberfullname . '' . $mobile_number . '' . $NotiType . ' AND admins.status=1')
                        ->groupBy('member_notifications.member_id', 'member_notifications.message_type')
                        ->orderBy('cities.name')
                        ->orderBy('members.first_name');
    }
    
}
