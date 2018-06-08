<?php
/**
 * The repository class for managing member activity recommendation specific actions.
 *
 *
 * @author Gauri Deshmukh <gaurid@iprogrammer.com>
 * @package Admin
 * @since 1.0
 */

namespace Modules\Admin\Repositories;

use Modules\Admin\Models\MemberActivityRecommendation;
use Modules\Admin\Models\ActivityType;
use Modules\Admin\Models\Recommendation;
use Exception;
use Route;
use Log;
use Cache;
use Modules\Admin\Services\Helper\PushHelper;
//use Modules\Admin\Models\Member;
use Modules\Admin\Models\MemberDeviceToken;
use Auth;

class MemberActivityRecommendationRepository extends BaseRepository
{

    /**
     * Create a new MemberActivityRecommendationRepository instance.
     *
     * @param  Modules\Admin\Models\MemberActivityRecommendation $model
     * @return void
     */
    public function __construct(MemberActivityRecommendation $memberActivityRecommendation, Recommendation $recommendation)
    {
        $this->model = $memberActivityRecommendation;
        $this->recommendation_model = $recommendation;
    }

    /**
     * Get a listing of the resource.
     *
     * @return Response
     */
    public function data($params = [])
    {
        $response = MemberActivityRecommendation::with('ActivityType')->orderBy('recommendation_date', 'DESC')->orderBy('created_at', 'DESC')->where('member_id', $params['member_id'])->get();
        return $response;
    }

    public function getCalories($typeId)
    {
        $response = ActivityType::select('calories')->where('id', '=', $typeId)->first();
        return $response;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  Form data posted from ajax $inputs
     * @return $result array with status and message elements
     */
    public function create($inputs)
    {
        try {
            $memberActivityRecommendation = new $this->model;

            $allColumns = $memberActivityRecommendation->getTableColumns($memberActivityRecommendation->getTable());

            foreach ($inputs as $key => $value) {
                if (in_array($key, $allColumns)) {
                    $memberActivityRecommendation->$key = $value;
                }
            }

            $recommendation_date = \DateTime::createFromFormat('d-m-Y', $inputs['recommendation_date']);
            $recommendation_date = $recommendation_date->format('Y-m-d');
            $memberActivityRecommendation->recommendation_date = $recommendation_date;
            $save = $memberActivityRecommendation->save();

            $tokenData = MemberDeviceToken::whereMemberId($inputs['member_id'])->first();
            $extra = [];
            $title = "VLCC - Slimmer's App";
            $extra['body'] = "You have a new activity recommendation";
            $extra['title'] = $title;

            if (isset($tokenData->device_token)) {
                PushHelper::sendGeneralPushNotification($tokenData->device_token, $tag = '', $extra['body'], $extra, $title, $tokenData->device_type, $memberActivityRecommendation->id);
                // Insert Data into member_notifications table
                $recommendations = new $this->recommendation_model;
                $this->insertNotification($inputs, $recommendations, $extra['body']);
            }

            if ($save) {
                $response['status'] = 'success';
                $response['message'] = trans('admin::messages.added', ['name' => trans('admin::controller/member-activity-recommendation.member-activity-recommendation')]);
            } else {
                $response['status'] = 'error';
                $response['message'] = trans('admin::messages.not-added', ['name' => trans('admin::controller/member-activity-recommendation.member-activity-recommendation')]);
            }

            return $response;
        } catch (Exception $e) {
            $exceptionDetails = $e->getMessage();
            $response['status'] = 'error';
            $response['message'] = trans('admin::messages.not-added', ['name' => trans('admin::controller/member-activity-recommendation.member-activity-recommendation')]) . "<br /><b> Error Details</b> - " . $exceptionDetails;
            Log::error(trans('admin::messages.not-added', ['name' => trans('admin::controller/member-activity-recommendation.member-activity-recommendation')]), ['Error Message' => $exceptionDetails, 'Current Action' => Route::getCurrentRoute()->getActionName()]);

            return $response;
        }
    }

    // Function to insert data into member_notifications table
    public function insertNotification($inputs, $recommendations, $message_text)
    {
        $recommendations->member_id = $inputs["member_id"];
        $recommendations->message_type = 5;
        $recommendations->message_text = $message_text;
        $recommendations->status = 1;
        $recommendations->created_by = Auth::guard('admin')->user()->id;
        $saveRecommendation = $recommendations->save();
    }
}
