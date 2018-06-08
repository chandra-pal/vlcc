<?php

/**
 * The repository class for managing offers specific actions.
 *
 *
 * @author Priyanka Deshpande <priyankad@iprogrammer.com>
 * @package Admin
 * @since 1.0
 */

namespace Modules\Admin\Repositories;

use Modules\Admin\Models\EscalationMatrix;
use Exception;
use Route;
use Auth;
use Log;
use Cache;
use URL;
use File;
use DB;
use PDO;

class EscalationMatrixRepository extends BaseRepository {

    /**
     * Create a new OffersRepository instance.
     *
     * @param  Modules\Admin\Models\Offer $model
     * @return void
     */
    public function __construct(EscalationMatrix $escalation_matrix) {
        $this->model = $escalation_matrix;
    }

    /**
     * Get a listing of the resource.
     *
     * @return Response
     */
    public function data($params = []) {
        $response = '';
        if ($params['user_type_id'] == 9) {  //ATH
            $response = EscalationMatrix::with('Package', 'Member')
                    ->where('admin_id', Auth::guard('admin')->user()->id)
                    ->orderBy('id', 'DESC')
                    ->get();
        } elseif ($params['user_type_id'] == 4 || $params['user_type_id'] == 8) { //Dietician & Slimming Head
            $memberIds = DB::select("SELECT group_concat(id) as id FROM members WHERE dietician_username = '" . $params['user_name'] . "'");
            if (isset($memberIds) && isset($memberIds[0]->id) && $memberIds[0]->id != '') {
                $response = EscalationMatrix::with('Package', 'Member')
                        ->whereRaw("FIND_IN_SET(member_id,'" . $memberIds[0]->id . "')")
                        ->get();
            }
        } elseif ($params['user_type_id'] == 7) { //Center Head
            $centerIds = DB::select("SELECT group_concat(center_id) as center_id FROM admin_centers WHERE user_id = " . Auth::guard('admin')->user()->id . "");
            if (isset($centerIds[0]) && isset($centerIds[0]->center_id) && $centerIds[0]->center_id != '') {
                $response = EscalationMatrix::with('Package', 'Member')
                        ->whereRaw("FIND_IN_SET(member_id,(SELECT GROUP_CONCAT(id) as id FROM members WHERE dietician_username IN (SELECT username FROM admins as a LEFT JOIN admin_centers as ac ON a.id=ac.user_id WHERE ac.center_id IN('" . $centerIds[0]->center_id . "'))))")
                        ->get();
            } else {
                $response = collect();
            }
        } else {
            $response = collect();
        }
        return $response;
    }

    // Function to add ath comment when weight loss is less than 0 kg
    public function addAthComment($params) {
        $result = EscalationMatrix::where('admin_id', $params["user_id"])
                ->where('session_id', $params["session_id"])
                ->where('member_id', $params["member_id"])
                ->update(['escalation_status' => 2, "ath_comment" => $params["ath_comment"]]);
        DB::setFetchMode(PDO::FETCH_ASSOC);
        $get_dietician_id = DB::select("SELECT S.dietician_id FROM member_session_bookings S
            INNER JOIN member_escalation_matrix E ON
            S.id=E.session_id where E.session_id=" . $params["session_id"] . " AND E.member_id=" . $params["member_id"]);
        DB::setFetchMode(PDO::FETCH_CLASS);

        $notifications = array("admin_id" => $get_dietician_id[0]['dietician_id'], "notification_text" => "ATH has commented on escalation.", "deep_linking" => "escalation-matrix", "notification_date" => date('Y-m-d H:i:s'), "notification_type" => 3, "read_status" => 0, "created_by" => Auth::guard('admin')->user()->id, "updated_by" => Auth::guard('admin')->user()->id, "created_at" => date('Y-m-d H:i:s'));
        $insertArray[] = $notifications;
        DB::table("admin_notifications")->insert($insertArray);
        return $result;
    }

    // Function to get ath comment when weight loss is less than 0 kg
    public function getAthComment($params) {
        DB::setFetchMode(PDO::FETCH_ASSOC);
        //if (isset($params["user_id"])) {
            //$result = DB::select("SELECT ath_comment FROM member_escalation_matrix WHERE admin_id = " . $params["user_id"] . " AND package_id=" . $params["package_id"] . " AND session_id=" . $params["session_id"] . " AND member_id=" . $params["member_id"] . "");
        //} else {
            $result = DB::select("SELECT ath_comment FROM member_escalation_matrix WHERE package_id=" . $params["package_id"] . " AND session_id=" . $params["session_id"] . " AND member_id=" . $params["member_id"] . "");
        //}
        DB::setFetchMode(PDO::FETCH_CLASS);
        if (isset($result[0]))
            return $result[0];
        else
            return 0;
    }

    /**
     * get the count for dashboard
     * @param type $params
     * @return type
     */
    public function dataCount($params = []) {
        $response = '';
        if ($params['user_type_id'] == 4 || $params['user_type_id'] == 8) { //Dietician & Slimming Head
            $memberIds = DB::select("SELECT group_concat(id) as id FROM members WHERE dietician_username = '" . $params['user_name'] . "'");
            if (isset($memberIds[0]) && isset($memberIds[0]->id) && $memberIds[0]->id != '') {
                $response = EscalationMatrix::with('Package', 'Member')
                        ->whereRaw("FIND_IN_SET(member_id,'" . $memberIds[0]->id . "')")
                        ->count();
            }
        } elseif ($params['user_type_id'] == 9) { // ATH
            $response = EscalationMatrix::with('Package', 'Member')
                    ->where('admin_id', $params['user_id'])
                    ->count();
        } elseif ($params['user_type_id'] == 7) { //Center Head
            $centerIds = DB::select("SELECT group_concat(center_id) as center_id FROM admin_centers WHERE user_id = " . $params['user_id'] . "");
            if (isset($centerIds[0]) && isset($centerIds[0]->center_id) && $centerIds[0]->center_id != '') {
                $response = EscalationMatrix::with('Package', 'Member')
                        ->whereRaw("FIND_IN_SET(member_id,(SELECT GROUP_CONCAT(id) as id FROM members WHERE dietician_username IN (SELECT username FROM admins as a LEFT JOIN admin_centers as ac ON a.id=ac.user_id WHERE ac.center_id IN('" . $centerIds[0]->center_id . "'))))")
                        ->count();
            } else {
                $response = collect();
            }
        } else {
            $response = collect();
        }
        return $response;
    }
    
    
    // Function to get member center name 
    public function getMemberCenter($params){
        DB::setFetchMode(PDO::FETCH_ASSOC);
        
         $result = DB::select("SELECT vlcc_centers.center_name AS center FROM vlcc_centers,members,member_escalation_matrix WHERE member_escalation_matrix.member_id = members.id AND members.crm_center_id = vlcc_centers.crm_center_id
AND member_escalation_matrix.member_id=". $params);
          DB::setFetchMode(PDO::FETCH_CLASS);
         
        $response = collect($result)->toArray();
        $center_name = (isset($response[0]['center'])) ? $response[0]['center'] : 'N/A';
        return $center_name;       
        
    }

}
