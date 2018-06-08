<?php

/**
 * The helper library class for getting information of a logged in user from storage
 *
 *
 * @author Tushar Dahiwale <tushard@iprogrammer.com>
 * @package Admin
 * @since 1.0
 */

namespace Modules\Admin\Services\Helper;

use Auth;
use Modules\Admin\Models\User;
use DB;
use PDO;

class UserInfoHelper {

    /**
     * fetch user details
     * @return String
     */
    public static function getAuthUserInfo() {
        return $userinfo = User::find(Auth::guard('admin')->user()->id);
    }

    /**
     * fetch user details
     * @return String
     */
    public static function getAuthUserWithType() {
        return $userinfo = User::whereId(Auth::guard('admin')->user()->id)->with('userType')->first();
    }

    /**
     * fetch user's center details
     * @return Array
     */
    public static function getAuthUserCenter($id) {
        DB::setFetchMode(PDO::FETCH_ASSOC);
        $response = DB::select("SELECT center_name FROM vlcc_centers as vc LEFT JOIN admin_centers as ac ON vc.id=ac.center_id LEFT JOIN admins as a ON ac.user_id = a.id WHERE a.id = " . $id . "");
        DB::setFetchMode(PDO::FETCH_CLASS);
        return $response;
    }
    
    // Function to get Logged in User (Dietician, Slimming head, Physiotherapist, Centre Head ) City & Center
    public function getLoggedInUserCenter($logged_in_user_id) {
        $result = DB::select("SELECT admin_centers.center_id, vlcc_centers.id, vlcc_centers.crm_center_id, vlcc_centers.city_id, cities.name FROM admins LEFT OUTER JOIN admin_centers ON admins.id = admin_centers.user_id LEFT OUTER JOIN vlcc_centers ON admin_centers.center_id =  vlcc_centers.id INNER JOIN cities ON vlcc_centers.city_id = cities.id WHERE admins.id=" . $logged_in_user_id . " ");
        $result = json_decode(json_encode($result), true);
        return $result;
    }
}
