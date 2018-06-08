<?php

/**
 * The repository class for managing member diet log.
 *
 *
 * @author Gauri Deshmukh <gaurid@iprogrammer.com>
 * @package Admin
 * @since 1.0
 */

namespace Modules\Admin\Repositories;

use Modules\Admin\Models\MemberDietLog;
use Modules\Admin\Models\MemberDietRecommendation;
use Exception;
use Route;
use Log;
use Cache;
use DB;
use File;
use PDO;
use Modules\Admin\Services\Helper\PushHelper;
use Modules\Admin\Models\MemberDeviceToken;
use Auth;

class MemberDietLogRepository extends BaseRepository {

    /**
     * Create a new MemberDietLogRepository instance.
     *
     * @param  Modules\Admin\Models\MemberDietLog $memberDietLog
     * @return void
     */
    public function __construct(MemberDietLog $memberDietLog, MemberDietRecommendation $memberDietRecommendation) {
        $this->model = $memberDietLog;
        $this->memberDietRecommendation = $memberDietRecommendation;
    }

    /**
     * Get a listing of the resource.
     *
     * @return Response
     */
    public function data($params = []) {
        DB::setFetchMode(PDO::FETCH_ASSOC);
        $request_details = DB::select('select S.schedule_name,M.id,M.food_name,M.servings_consumed,M.measure,M.calories,M.serving_size,M.serving_unit,D.calories_recommended,D.calories_consumed from diet_schedule_types S LEFT JOIN member_diet_logs M ON S.id=M.diet_schedule_type_id AND M.member_id=' . $params['member_id'] . ' AND M.diet_date = "' . $params['date'] . '" LEFT JOIN member_diet_deviations D ON S.id=D.diet_schedule_type_id AND D.member_id=' . $params['member_id'] . ' AND D.deviation_date="' . $params['date'] . '"');
        DB::setFetchMode(PDO::FETCH_CLASS);
        return $response = collect($request_details);
    }

    /**
     * Get a listing of the member diet recommendation.
     *
     * @return Response
     */
    public function getMemberDietRecommendations($params = []) {
//        $response = MemberDietRecommendation::with('Food', 'Schedule', 'Type')->whereMemberId($params['member_id'])->whereDietPlanId($params['member_diet_plan_id'])->whereRecommendationDate($params['date'])->orderBy('id')->get();
//        return $response;

        DB::setFetchMode(PDO::FETCH_ASSOC);
        $request_details = DB::select('select F.food_name, F.measure, F.calories, F.serving_size, F.serving_unit, R.servings_recommended, S.schedule_name, T.food_type_name from foods F INNER JOIN member_diet_recommendations R ON F.id=R.food_id INNER JOIN diet_schedule_types S ON S.id=R.diet_schedule_type_id INNER JOIN food_types T ON T.id=F.food_type_id where R.diet_plan_id=' . $params['member_diet_plan_id'] . ' AND R.recommendation_date="' . $params['date'] . '"');
        DB::setFetchMode(PDO::FETCH_CLASS);
//        dd(collect($request_details));
        return $response = collect($request_details);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  Form data posted from ajax $inputs
     * @return $result array with status and message elements
     */
    public function create($inputs) {
        try {
            $member_diet_recommendation = array();
            $creates_by = Auth::guard('admin')->user()->id;
            $data = date("Y-m-d");
            foreach ($inputs['food_id'] as $key => $value) {
                $insert_array = array("member_id" => $inputs["member_id"], "diet_plan_id" => $inputs["diet_plan_id"], "diet_schedule_type_id" => $inputs["schedule_type_id"], "food_id" => $inputs["food_id"][$key], "servings_recommended" => $inputs["servings_recommended"][$key], "recommendation_date" => $data, "status" => "1", "created_by" => $creates_by, "created_at" => date("Y-m-d H:i:s"));

                $getFoodName = DB::table('foods')->select('food_name')->where('id', $inputs['food_id'])->first();
                $getScheduleName = DB::table('diet_schedule_types')->select('schedule_name')->where('id', $inputs['schedule_type_id'])->first();
//                $member_diet_recommendation[] = $insert_array;
                $lastInsertedId = DB::table("member_diet_recommendations")->insertGetId($insert_array);

                //send notification to user
                $tokenData = MemberDeviceToken::whereMemberId($inputs['member_id'])->first();

                $title = "VLCC - Slimmer's App";
                $extra['body'] = "You have a diet recommendation for" . $getScheduleName->schedule_name . ", have 2 servings of " . $getFoodName->food_name . "";
                $extra['title'] = $title;
                $message_text = $extra['body'];

                if (isset($tokenData->device_token)) {
                    PushHelper::sendGeneralPushNotification($tokenData->device_token, $tag = '', $message_text, $extra, $title, $tokenData->device_type, $lastInsertedId);
                }
            }

            $response['status'] = 'success';
            $response['message'] = trans('admin::messages.added', ['name' => trans('admin::controller/member-diet-log.diet-log-recommendation')]);
            return $response;
        } catch (Exception $e) {
            $exceptionDetails = $e->getMessage();
            $response['status'] = 'error';
            $response['message'] = trans('admin::messages.not-added', ['name' => trans('admin::controller/member-diet-log.diet-log-recommendation')]) . "<br /><b> Error Details</b> - " . $exceptionDetails;
            Log::error(trans('admin::messages.not-added', ['name' => trans('admin::controller/member-diet-log.diet-log-recommendation')]), ['Error Message' => $exceptionDetails, 'Current Action' => Route::getCurrentRoute()->getActionName()]);

            return $response;
        }
    }
}
