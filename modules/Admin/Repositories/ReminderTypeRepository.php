<?php

/**
 * The repository class for managing reminder type specific actions.
 *
 *
 * @author Gauri Deshmukh <gaurid@iprogrammer.com>
 * @package Admin
 * @since 1.0
 */

namespace Modules\Admin\Repositories;

use Modules\Admin\Models\ReminderType;
use Exception;
use Route;
use Log;
use Cache;

class ReminderTypeRepository extends BaseRepository {

    /**
     * Create a new ReminderTypeRepository instance.
     *
     * @param  Modules\Admin\Models\ReminderType $model
     * @return void
     */
    public function __construct(ReminderType $reminderType) {
        $this->model = $reminderType;
    }

    /**
     * Get a listing of the resource.
     *
     * @return Response
     */
    public function data($params = []) {
        $cacheKey = str_replace(['\\'], [''], __METHOD__) . ':' . md5(json_encode($params));
        //Cache::tags not suppport with files and Database
        $response = Cache::tags(ReminderType::table())->remember($cacheKey, $this->ttlCache, function() {
            return ReminderType::select([
                        'id', 'type_name', 'status'
                    ])->orderBy('id')->get();
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
            $reminderType = new $this->model;

            $allColumns = $reminderType->getTableColumns($reminderType->getTable());

            foreach ($inputs as $key => $value) {
                if (in_array($key, $allColumns)) {
                    $reminderType->$key = $value;
                }
            }
            $reminderType->type_name = ucfirst($inputs['type_name']);
            $reminderType->status = isset($inputs['status']) ? $inputs['status'] : 0;

            $save = $reminderType->save();

            if ($save) {
                $response['status'] = 'success';
                $response['message'] = trans('admin::messages.added', ['name' => trans('admin::controller/reminder-type.reminder-type')]);
            } else {
                $response['status'] = 'error';
                $response['message'] = trans('admin::messages.not-added', ['name' => trans('admin::controller/reminder-type.reminder-type')]);
            }

            return $response;
        } catch (Exception $e) {
            $exceptionDetails = $e->getMessage();
            $response['status'] = 'error';
            $response['message'] = trans('admin::messages.not-added', ['name' => trans('admin::controller/reminder-type.reminder-type')]) . "<br /><b> Error Details</b> - " . $exceptionDetails;
            Log::error(trans('admin::messages.not-added', ['name' => trans('admin::controller/reminder-type.reminder-type')]), ['Error Message' => $exceptionDetails, 'Current Action' => Route::getCurrentRoute()->getActionName()]);

            return $response;
        }
    }

    /**
     * Update an reminder type.
     *
     * @param  Form data posted from ajax $inputs, Modules\Admin\Models\ReminderType $reminderType
     * @return $result array with status and message elements
     * @return void
     */
    public function update($inputs, $reminderType) {
        try {

            foreach ($inputs as $key => $value) {
                if (isset($reminderType->$key)) {
                    $reminderType->$key = $value;
                }
            }
            $reminderType->type_name = ucfirst($inputs['type_name']);
            $reminderType->status = isset($inputs['status']) ? $inputs['status'] : 0;

            $save = $reminderType->save();

            if ($save) {
                $response['status'] = 'success';
                $response['message'] = trans('admin::messages.updated', ['name' => trans('admin::controller/reminder-type.reminder-type')]);
            } else {
                $response['status'] = 'error';
                $response['message'] = trans('admin::messages.not-updated', ['name' => trans('admin::controller/reminder-type.reminder-type')]);
            }

            return $response;
        } catch (Exception $e) {
            $exceptionDetails = $e->getMessage();
            $response['status'] = 'error';
            $response['message'] = trans('admin::messages.not-updated', ['name' => trans('admin::controller/reminder-type.reminder-type')]) . "<br /><b> Error Details</b> - " . $exceptionDetails;
            Log::error(trans('admin::messages.not-updated', ['name' => trans('admin::controller/reminder-type.reminder-type')]), ['Error Message' => $exceptionDetails, 'Current Action' => Route::getCurrentRoute()->getActionName()]);

            return $response;
        }
    }

    /**
     * Delete actions on reminder types
     *
     * @param  int  $status
     * @return int
     */
    public function deleteAction($inputs) {
        try {

            $resultStatus = false;

            $id = $inputs['ids'];
            $reminderType = ReminderType::find($id);
            if (!empty($reminderType)) {
                $reminderType->delete();
                $resultStatus = true;
            }

            return $resultStatus;
        } catch (Exception $e) {
            $exceptionDetails = $e->getMessage();
            $response['status'] = 'error';
            $response['message'] = trans('admin::messages.not-deleted', ['name' => trans('admin::controller/reminder-type.reminder-type')]) . "<br /><b> Error Details</b> - " . $exceptionDetails;
            Log::error(trans('admin::messages.not-deleted', ['name' => trans('admin::controller/reminder-type.reminder-type')]), ['Error Message' => $exceptionDetails, 'Current Action' => Route::getCurrentRoute()->getActionName()]);

            return $response;
        }
    }

}
