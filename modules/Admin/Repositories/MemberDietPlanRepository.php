<?php

/**
 * The repository class for managing member diet plan specific actions.
 *
 *
 * @author Gauri Deshmukh <gaurid@iprogrammer.com>
 * @package Admin
 * @since 1.0
 */

namespace Modules\Admin\Repositories;

use Modules\Admin\Models\MemberDietPlan;
use Modules\Admin\Models\DietPlan;
use Modules\Admin\Models\DietScheduleType;
use Modules\Admin\Models\Food;
use Modules\Admin\Models\DietPlanDetail;
use Exception;
use Route;
use Log;
use Cache;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7;
use GuzzleHttp\Psr7\Stream;
use Illuminate\Support\Facades\DB;
use Session;
use Auth;
use PDO;

class MemberDietPlanRepository extends BaseRepository {

    /**
     * Create a new MemberDietPlanRepository instance.
     *
     * @param  Modules\Admin\Models\MemberDietPlan $model
     * @return void
     */
    public function __construct(MemberDietPlan $memberDietPlan) {
        $this->model = $memberDietPlan;
    }

    /**
     * Get a listing of the resource.
     *
     * @return Response
     */
    public function data($params = []) {
        $member_id = $params["member_id"];
//        $member_diet_plan_details = DB::select("SELECT id, diet_plan_id, diet_schedule_type_id, schedule_name, food_id,  food_name, measure, calories, serving_size, serving_unit, servings_recommended, active FROM (
//(SELECT items.id, items.diet_plan_id, items.diet_schedule_type_id, diet_schedule_types.schedule_name, items.food_id, foods.food_name, foods.measure, foods.calories, foods.serving_size, foods.serving_unit, items.servings_recommended, 1 AS active FROM member_diet_plan_details items LEFT JOIN diet_schedule_types ON items.diet_schedule_type_id = diet_schedule_types.id
//LEFT JOIN foods ON items.food_id = foods.id WHERE items.member_id = " . $member_id . " AND items.diet_plan_id = " . $params["diet_plan_id"] . " AND items.status = 1)
//UNION
//(SELECT items.id, items.diet_plan_id, items.diet_schedule_type_id, diet_schedule_types.schedule_name, items.food_id, foods.food_name, foods.measure, foods.calories, foods.serving_size, foods.serving_unit, items.servings_recommended, 0 AS active FROM diet_plan_details items LEFT JOIN diet_schedule_types ON items.diet_schedule_type_id = diet_schedule_types.id LEFT JOIN foods ON items.food_id = foods.id WHERE items.diet_plan_id = " . $params["diet_plan_id"] . " AND items.status = 1)
//) AS items
//GROUP BY items.food_id, items.diet_schedule_type_id
//ORDER BY items.diet_schedule_type_id, items.active DESC");

        $member_diet_plan_details = DB::select("SELECT id, diet_plan_id, diet_schedule_type_id, schedule_name, food_type_id, food_type_name, food_id,  food_name, measure, calories, serving_size, serving_unit, servings_recommended, active, created_at FROM (
(SELECT items.id, items.diet_plan_id, items.diet_schedule_type_id, diet_schedule_types.schedule_name, items.food_id, food_types.id as food_type_id, food_types.food_type_name, foods.food_name, foods.measure, foods.calories, foods.serving_size, foods.serving_unit, items.servings_recommended, items.created_at, 1 AS active FROM member_diet_plan_details items LEFT JOIN diet_schedule_types ON items.diet_schedule_type_id = diet_schedule_types.id LEFT JOIN foods ON items.food_id = foods.id LEFT JOIN food_types ON foods.food_type_id = food_types.id WHERE items.member_id = " . $member_id . " AND items.diet_plan_id = " . $params["diet_plan_id"] . " AND items.status = 1)
UNION (
SELECT items.id, items.diet_plan_id, diet_schedule_types.id as diet_schedule_type_id, diet_schedule_types.schedule_name, items.food_id, food_types.id as food_type_id, food_types.food_type_name, foods.food_name, foods.measure, foods.calories, foods.serving_size, foods.serving_unit, items.servings_recommended, items.created_at, 0 AS active FROM diet_plan_details items RIGHT JOIN diet_schedule_types ON items.diet_schedule_type_id = diet_schedule_types.id AND items.diet_plan_id = " . $params["diet_plan_id"] . " AND items.status = 1 LEFT JOIN foods ON items.food_id = foods.id LEFT JOIN food_types ON foods.food_type_id = food_types.id
)
)
AS items
GROUP BY items.food_id, items.diet_schedule_type_id
ORDER BY items.diet_schedule_type_id, items.active DESC
");
        return $response = collect($member_diet_plan_details);

//        $cacheKey = str_replace(['\\'], [''], __METHOD__) . ':' . md5(json_encode($params));
//        //Cache::tags not suppport with files and Database
//        $response = Cache::tags(DietPlanDetail::table(), DietPlan::table(), DietScheduleType::table(), Food::table())->remember($cacheKey, $this->ttlCache, function() use($params) {
//            return DietPlanDetail::with('DietPlan', 'DietScheduleType', 'Food')->whereDietPlanId($params['diet_plan_id'])->orderBy('id')->get();
//        });
//
//        return $response;
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function listMemberDietPlanDetails() {
        $cacheKey = str_replace(['\\'], [''], __METHOD__);
        $response = Cache::tags(MemberDietPlan::table())->remember($cacheKey, $this->ttlCache, function() {
            return MemberDietPlan::select('first_name', 'last_name', 'id')->orderBY('id')->get();
        });


        return $response;
    }

    public function getMemberPlan($memberID) {
        $memberID = (int) $memberID;
        $cacheKey = str_replace(['\\'], [''], __METHOD__) . ':' . md5(json_encode($memberID));
        //Cache::tags not suppport with files and Database
        //$response = Cache::tags(MemberDietPlan::table())->remember($cacheKey, $this->ttlCache, function() use($memberID) {
        return MemberDietPlan::select('diet_plan_id')->has('DietPlan')->whereId($memberID)->where('diet_plan_id', '!=', 0)->orderBY('id')->first();
        //});

        return $response;
    }

    /**
     * Update an member diet plan.
     *
     * @param  Form data posted from ajax $inputs, Modules\Admin\Models\MemberDietPlan $memberDietPlan
     * @return $result array with status and message elements
     * @return void
     */
    public function update($inputs, $memberDietPlan) {
        try {
            // Delete Data from Database for that member_id & diet_plan_id
            $delete_success = DB::table('member_diet_plan_details')
                    ->where('member_id', '=', $inputs["id"])
                    ->delete();

            $final_memder_diet_plan_details = array();
            $final_selected_member_diet_plan = explode(",", $inputs["member_diet_plan"]);

            foreach ($final_selected_member_diet_plan as $key => $checked_food) {
                // Insert Only Checked Food Items
                if ($checked_food) {
                    if ((isset($inputs["diet_schedule_type_id"][$key]) && isset($inputs["food_id"][$key]) && isset($inputs["servings_recommended"][$key])) && (!empty($inputs["diet_schedule_type_id"][$key]) && !empty($inputs["food_id"][$key]) && !empty($inputs["servings_recommended"][$key]) && $inputs["servings_recommended"][$key] != 0)) {
                        // Insert Data into table member_diet_plan_details
                        $insert_array = array("member_id" => $inputs["id"], "diet_plan_id" => $inputs["diet_plan_id"], "diet_schedule_type_id" => $inputs["diet_schedule_type_id"][$key], "food_id" => $inputs["food_id"][$key], "servings_recommended" => $inputs["servings_recommended"][$key], "status" => "1", "created_by" => "1", "created_at" => date("Y-m-d H:i:s"));
                        $final_memder_diet_plan_details[] = $insert_array;
                    }
                }
            }

            DB::table("member_diet_plan_details")->insert($final_memder_diet_plan_details);

            // Update diet Plan id in database
            DB::table('members')->where('id', $inputs["id"])->update(['diet_plan_id' => $inputs["diet_plan_id"]]);

            $response['status'] = 'success';
            $response['message'] = trans('admin::messages.updated', ['name' => trans('admin::controller/member-diet-plan.member-diet-plan')]);
            return $response;
        } catch (Exception $ex) {
            $exceptionDetails = $ex->getMessage();
            $response['status'] = 'error';
            $response['message'] = trans('admin::messages.not-updated', ['name' => trans('admin::controller/member-diet-plan.member-diet-plan')]) . "<br /><b> Error Details</b> - " . $exceptionDetails;
            Log::error(trans('admin::messages.not-updated', ['name' => trans('admin::controller/member-diet-plan.member-diet-plan')]), ['Error Message' => $exceptionDetails, 'Current Action' => Route::getCurrentRoute()->getActionName()]);

            return $response;
        }
    }
public function updateDietPlan($inputs, $memberDietPlan){
    try {
        // Delete Data from Database for that member_id & diet_plan_id
        //$delete_success = DB::table('member_diet_plan_details')
        //    ->where('member_id', '=', $inputs["id"])
        //    ->delete()

//        $final_selected_member_diet_plan = explode(",", $inputs["member_diet_plan"]);
//        $final_member_diet_plan_details = array();

//        foreach ($final_member_diet_plan_details as $key => $checked_food) {
//            // Insert Only Checked Food Items
//            if ($checked_food) {
//                if ((isset($inputs["diet_schedule_type_id"][$key]) && isset($inputs["food_id"][$key]) && isset($inputs["servings_recommended"][$key])) && (!empty($inputs["diet_schedule_type_id"][$key]) && !empty($inputs["food_id"][$key]) && !empty($inputs["servings_recommended"][$key]) && $inputs["servings_recommended"][$key] != 0)) {
//                    // Insert Data into table member_diet_plan_details
//                    $update_array = array("member_id" => $inputs["id"], "diet_plan_id" => $inputs["diet_plan_id"], "diet_schedule_type_id" => $inputs["diet_schedule_type_id"][$key], "food_id" => $inputs["food_id"][$key], "servings_recommended" => $inputs["servings_recommended"][$key], "status" => "1", "created_by" => "1", "created_at" => date("Y-m-d H:i:s"));
//                    $final_member_diet_plan_details[] = $update_array;
//                }
//            }
//       }

//        foreach ($inputs as $key => $value){
//            if(isset($memberDietPlan->$key)){
//                $memberDietPlan->$key=$value;
//            }
//        }
        print_r($inputs);
        $final_member_diet_plan_details["member_id"] = $inputs["id"];
        $final_member_diet_plan_details["diet_plan_id"] = $inputs["diet_plan_id"];
        $final_member_diet_plan_details["diet_schedule_type_id"] = $inputs["diet_schedule_type_id"];
        foreach ($inputs["food_id"] as $key => $value){
            $final_member_diet_plan_details["food_id"] = $inputs["food_id"][$key];
        }
        $final_member_diet_plan_details["servings_recommended"] = $inputs["servings_recommended"];
        $final_member_diet_plan_details["status"] = "1";
        $final_member_diet_plan_details["updated_by"] = "1";
        $final_member_diet_plan_details["updated_at"] = date("Y-m-d H:i:s");
        $save = DB::table("member_diet_plan_details")->where('id',$inputs["diet_plan_row_id"])->update($final_member_diet_plan_details);

        // Update diet Plan id in database
       // DB::table('members')->where('id', $inputs["id"])->update(['diet_plan_id' => $inputs["diet_plan_id"]]);
        if($save) {
            $response['status'] = 'success';
            $response['message'] = trans('admin::messages.updated', ['name' => trans('admin::controller/member-diet-plan.member-diet-plan')]);
        }else {
            $response['status'] = 'error';
            $response['message'] = trans('admin::messages.not-updated', ['name' => trans('admin::controller/session-bookings.session-bookings')]);
        }
        return $response;
    } catch (Exception $ex) {
        $exceptionDetails = $ex->getMessage();
        $response['status'] = 'error';
        $response['message'] = trans('admin::messages.not-updated', ['name' => trans('admin::controller/member-diet-plan.member-diet-plan')]) . "<br /><b> Error Details</b> - " . $exceptionDetails;
        Log::error(trans('admin::messages.not-updated', ['name' => trans('admin::controller/member-diet-plan.member-diet-plan')]), ['Error Message' => $exceptionDetails, 'Current Action' => Route::getCurrentRoute()->getActionName()]);

        return $response;
    }
}
    public function getDieticianFoods($params) {
        $dietician_id = Auth::guard('admin')->user()->id;
        $food_type_id = $params["food_type_id"];

        /* $result =  Food::select('id', 'food_name', 'measure', 'calories', 'serving_size', 'serving_unit')
          ->where('food_type_id', '=', $food_type_id)
          ->where('status', '=', 1)
          ->where('created_by_user_type', '=', '1')
          ->orWhere('created_by', '=', 1)
          ->where('created_by_user_type', '=', '4')
          ->orWhere('created_by', '=', $dietician_id)
          ->lists('food_name', 'id', 'food_type_id'); */
        DB::setFetchMode(PDO::FETCH_KEY_PAIR);
        $result = DB::select("SELECT id, food_name FROM foods WHERE food_type_id = " . $food_type_id . " AND status = '1' AND ((created_by_user_type = '1' AND created_by = '1') OR (created_by_user_type = '4'))");
        DB::setFetchMode(PDO::FETCH_CLASS);
        return collect($result);
    }

    public function getDietPlanCalories($dietPlanId) {
        DB::setFetchMode(PDO::FETCH_ASSOC);
        $result = DB::select("SELECT calories FROM diet_plans WHERE id = " . $dietPlanId . "");
        DB::setFetchMode(PDO::FETCH_CLASS);
        return $result[0];
    }

    public function getDietPlanDate($memberID) {
        DB::setFetchMode(PDO::FETCH_ASSOC);
        $result = DB::select("SELECT created_at FROM member_diet_plan_details WHERE member_id = " . $memberID . "");
        DB::setFetchMode(PDO::FETCH_CLASS);
        return $result[0];
    }

    public function getDietPlan($RowId)
    {
        $result = DB::select("SELECT diet_plan_id, diet_schedule_type_id, food_id, servings_recommended, food_types.id as foodtypeid
                              FROM member_diet_plan_details
                              LEFT JOIN foods on foods.id = member_diet_plan_details.food_id
                              LEFT JOIN food_types ON food_types.id = foods.food_type_id 
                              WHERE member_diet_plan_details.id = ".$RowId." ");
         return $result[0];
    }
}
