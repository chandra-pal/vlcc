<?php

/**
 * The repository class for managing activity type specific actions.
 *
 *
 * @author Gauri Deshmukh <gaurid@iprogrammer.com>
 * @package Admin
 * @since 1.0
 */

namespace Modules\Admin\Repositories;

use Modules\Admin\Models\ActivityType;
use Exception;
use Route;
use Log;
use Cache;

class ActivityTypeRepository extends BaseRepository {

    /**
     * Create a new ActivityTypeRepository instance.
     *
     * @param  Modules\Admin\Models\ActivityType $model
     * @return void
     */
    public function __construct(ActivityType $activityType) {
        $this->model = $activityType;
    }

    /**
     * Get a listing of the resource.
     *
     * @return Response
     */
    public function data($params = []) {
        $cacheKey = str_replace(['\\'], [''], __METHOD__) . ':' . md5(json_encode($params));
        //Cache::tags not suppport with files and Database
        $response = Cache::tags(ActivityType::table())->remember($cacheKey, $this->ttlCache, function() {
            return ActivityType::select([
                        'id', 'activity_type', 'calories', 'status'
                    ])->orderBy('id')->get();
        });

        return $response;
    }

    public function getActivityList() {
        $cacheKey = str_replace(['\\'], [''], __METHOD__);
        //Cache::tags not suppport with files and Database
        $response = Cache::tags(ActivityType::table())->remember($cacheKey, $this->ttlCache, function() {
            return ActivityType::orderBY('id')->whereStatus(1)->lists('activity_type', 'id');
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
            $activityType = new $this->model;

            $allColumns = $activityType->getTableColumns($activityType->getTable());

            foreach ($inputs as $key => $value) {
                if (in_array($key, $allColumns)) {
                    $activityType->$key = $value;
                }
            }
            $activityType->status = isset($inputs['status']) ? $inputs['status'] : 0;

            $save = $activityType->save();

            if ($save) {
                $response['status'] = 'success';
                $response['message'] = trans('admin::messages.added', ['name' => trans('admin::controller/activity-type.activity-type')]);
            } else {
                $response['status'] = 'error';
                $response['message'] = trans('admin::messages.not-added', ['name' => trans('admin::controller/activity-type.activity-type')]);
            }

            return $response;
        } catch (Exception $e) {
            $exceptionDetails = $e->getMessage();
            $response['status'] = 'error';
            $response['message'] = trans('admin::messages.not-added', ['name' => trans('admin::controller/activity-type.activity-type')]) . "<br /><b> Error Details</b> - " . $exceptionDetails;
            Log::error(trans('admin::messages.not-added', ['name' => trans('admin::controller/activity-type.activity-type')]), ['Error Message' => $exceptionDetails, 'Current Action' => Route::getCurrentRoute()->getActionName()]);

            return $response;
        }
    }

    /**
     * Update an activity type.
     *
     * @param  Form data posted from ajax $inputs, Modules\Admin\Models\ActivityType $activityType
     * @return $result array with status and message elements
     * @return void
     */
    public function update($inputs, $activityType) {
        try {

            foreach ($inputs as $key => $value) {
                if (isset($activityType->$key)) {
                    $activityType->$key = $value;
                }
            }
            $activityType->status = isset($inputs['status']) ? $inputs['status'] : 0;

            $save = $activityType->save();

            if ($save) {
                $response['status'] = 'success';
                $response['message'] = trans('admin::messages.updated', ['name' => trans('admin::controller/activity-type.activity-type')]);
            } else {
                $response['status'] = 'error';
                $response['message'] = trans('admin::messages.not-updated', ['name' => trans('admin::controller/activity-type.activity-type')]);
            }

            return $response;
        } catch (Exception $e) {
            $exceptionDetails = $e->getMessage();
            $response['status'] = 'error';
            $response['message'] = trans('admin::messages.not-updated', ['name' => trans('admin::controller/activity-type.activity-type')]) . "<br /><b> Error Details</b> - " . $exceptionDetails;
            Log::error(trans('admin::messages.not-updated', ['name' => trans('admin::controller/activity-type.activity-type')]), ['Error Message' => $exceptionDetails, 'Current Action' => Route::getCurrentRoute()->getActionName()]);

            return $response;
        }
    }

    /**
     * Delete actions on activity types
     *
     * @param  int  $status
     * @return int
     */
    public function deleteAction($inputs) {
        try {

            $resultStatus = false;

            $id = $inputs['ids'];
            $activityType = ActivityType::find($id);
            if (!empty($activityType)) {
                $activityType->delete();
                $resultStatus = true;
            }

            return $resultStatus;
        } catch (Exception $e) {
            $exceptionDetails = $e->getMessage();
            $response['status'] = 'error';
            $response['message'] = trans('admin::messages.not-deleted', ['name' => trans('admin::controller/activity-type.activity-type')]) . "<br /><b> Error Details</b> - " . $exceptionDetails;
            Log::error(trans('admin::messages.not-deleted', ['name' => trans('admin::controller/activity-type.activity-type')]), ['Error Message' => $exceptionDetails, 'Current Action' => Route::getCurrentRoute()->getActionName()]);

            return $response;
        }
    }

}
