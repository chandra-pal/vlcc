<?php

/**
 * The repository class for managing recommendation specific actions.
 *
 *
 * @author Gauri Deshmukh <gaurid@iprogrammer.com>
 * @package Admin
 * @since 1.0
 */

namespace Modules\Admin\Repositories;

use Modules\Admin\Models\Recommendation;
use Exception;
use Route;
use Log;
use Cache;
use Modules\Admin\Services\Helper\PushHelper;
//use Modules\Admin\Models\Member;
use Modules\Admin\Models\MemberDeviceToken;

class RecommendationRepository extends BaseRepository {

    /**
     * Create a new RecommendationRepository instance.
     *
     * @param  Modules\Admin\Models\Recommendation $model
     * @return void
     */
    public function __construct(Recommendation $recommendation) {
        $this->model = $recommendation;
    }

    /**
     * Get a listing of the resource.
     *
     * @return Response
     */
    public function data($params = []) {
        $cacheKey = str_replace(['\\'], [''], __METHOD__) . ':' . md5(json_encode($params));
        //Cache::tags not suppport with files and Database
        $response = Cache::tags(Recommendation::table())->remember($cacheKey, $this->ttlCache, function() use($params) {
            return Recommendation::select([
                        'id', 'message_type', 'message_text', 'status'
                    ])->whereMemberId($params['member_id'])->orderBy('id')->get();
        });

        return $response;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  Form data posted from ajax $inputs
     * @return $result array with status and message elements
     */
    public function create($inputs, $user_id = null) {
        try {
            $recommendation = new $this->model;

            $allColumns = $recommendation->getTableColumns($recommendation->getTable());

            foreach ($inputs as $key => $value) {
                if (in_array($key, $allColumns)) {
                    $recommendation->$key = $value;
                }
            }
            $trim = trim($inputs['message_text']);
            $recommendation->message_text = ucfirst($trim);

            $save = $recommendation->save();

            $tokenData = MemberDeviceToken::whereMemberId($inputs['member_id'])->first();
            $extra = [];
            switch ($recommendation->message_type) {
                case 1:
                    $tag = 'normal_push_notification';
                    break;
                case 2:
                    $tag = 'activity_recommendation';
                    break;
                case 3:
                    $tag = 'diet_recommendation';
                    break;
                case 6:
                    $tag = 'doctor_comment';
                    break;
                default:
                    $tag = 'invalid';
            };
            $title = "VLCC - Slimmer's App";
            $extra['body'] = $recommendation->message_text;
            $extra['title'] = $title;
            if (isset($inputs['deep_link_screen'])) {
                $extra['deep_link_screen'] = $recommendation->deep_link_screen;
            }

            if (isset($tokenData->device_token)) {
                PushHelper::sendGeneralPushNotification($tokenData->device_token, $tag, $inputs['message_text'], $extra, $title, $tokenData->device_type, $recommendation->id);
            }

            if ($save) {
                $response['status'] = 'success';
                $response['message'] = trans('admin::messages.added', ['name' => trans('admin::controller/recommendation.recommendation')]);
            } else {
                $response['status'] = 'error';
                $response['message'] = trans('admin::messages.not-added', ['name' => trans('admin::controller/recommendation.recommendation')]);
            }

            return $response;
        } catch (Exception $e) {
            $exceptionDetails = $e->getMessage();
            $response['status'] = 'error';
            $response['message'] = trans('admin::messages.not-added', ['name' => trans('admin::controller/recommendation.recommendation')]) . "<br /><b> Error Details</b> - " . $exceptionDetails;
            Log::error(trans('admin::messages.not-added', ['name' => trans('admin::controller/recommendation.recommendation')]), ['Error Message' => $exceptionDetails, 'Current Action' => Route::getCurrentRoute()->getActionName()]);

            return $response;
        }
    }

    /**
     * Update an recommendation category.
     *
     * @param  Form data posted from ajax $inputs, Modules\Admin\Models\Recommendation $recommendation
     * @return $result array with status and message elements
     * @return void
     */
    public function update($inputs, $recommendation) {
        try {

            foreach ($inputs as $key => $value) {
                if (isset($recommendation->$key)) {
                    $recommendation->$key = $value;
                }
            }
            $recommendation->message_text = ucfirst($inputs['message_text']);
            $save = $recommendation->save();

            if ($save) {
                $response['status'] = 'success';
                $response['message'] = trans('admin::messages.updated', ['name' => trans('admin::controller/recommendation.recommendation')]);
            } else {
                $response['status'] = 'error';
                $response['message'] = trans('admin::messages.not-updated', ['name' => trans('admin::controller/recommendation.recommendation')]);
            }

            return $response;
        } catch (Exception $e) {
            $exceptionDetails = $e->getMessage();
            $response['status'] = 'error';
            $response['message'] = trans('admin::messages.not-updated', ['name' => trans('admin::controller/recommendation.recommendation')]) . "<br /><b> Error Details</b> - " . $exceptionDetails;
            Log::error(trans('admin::messages.not-updated', ['name' => trans('admin::controller/recommendation.recommendation')]), ['Error Message' => $exceptionDetails, 'Current Action' => Route::getCurrentRoute()->getActionName()]);

            return $response;
        }
    }

    /**
     * Delete actions on recommendation categories
     *
     * @param  int  $status
     * @return int
     */
    public function deleteAction($inputs) {
        try {

            $resultStatus = false;

            $id = $inputs['ids'];

            $recommendation = Recommendation::find($id);
            if (!empty($recommendation)) {
                $recommendation->delete();
                $resultStatus = true;
            }

            return $resultStatus;
        } catch (Exception $e) {
            $exceptionDetails = $e->getMessage();
            $response['status'] = 'error';
            $response['message'] = trans('admin::messages.not-deleted', ['name' => trans('admin::controller/recommendation.recommendation')]) . "<br /><b> Error Details</b> - " . $exceptionDetails;
            Log::error(trans('admin::messages.not-deleted', ['name' => trans('admin::controller/recommendation.recommendation')]), ['Error Message' => $exceptionDetails, 'Current Action' => Route::getCurrentRoute()->getActionName()]);

            return $response;
        }
    }

}
