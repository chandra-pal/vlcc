<?php

/**
 * The repository class for managing diet plan specific actions.
 *
 *
 * @author Gauri Deshmukh <gaurid@iprogrammer.com>
 * @package Admin
 * @since 1.0
 */

namespace Modules\Admin\Repositories;

use Modules\Admin\Models\DietPlan;
use Exception;
use Route;
use Log;
use Cache;

class DietPlanRepository extends BaseRepository {

    /**
     * Create a new DietPlanRepository instance.
     *
     * @param  Modules\Admin\Models\DietPlan $model
     * @return void
     */
    public function __construct(DietPlan $DietPlan) {
        $this->model = $DietPlan;
    }

    /**
     * Get a listing of the resource.
     *
     * @return Response
     */
    public function data($params = []) {
        $cacheKey = str_replace(['\\'], [''], __METHOD__) . ':' . md5(json_encode($params));
        //Cache::tags not suppport with files and Database
        $response = Cache::tags(DietPlan::table())->remember($cacheKey, $this->ttlCache, function() {
            return DietPlan::select([
                        'id', 'plan_name', 'plan_type', 'calories', 'status'
                    ])->orderBy('id')->get();
        });

        return $response;
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function listAllCategoriesData() {
        $cacheKey = str_replace(['\\'], [''], __METHOD__);
        //Cache::tags not suppport with files and Database
        $response = Cache::tags(DietPlan::table())->remember($cacheKey, $this->ttlCache, function() {
            return DietPlan::orderBY('id')->lists('plan_name', 'plan_type', 'calories', 'id');
        });

        return $response;
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function listPlanType() {
        $cacheKey = str_replace(['\\'], ['list'], __METHOD__);
        //Cache::tags not suppport with files and Database
        $response = Cache::tags(DietPlan::table())->remember($cacheKey, $this->ttlCache, function() {
            return DietPlan::select('plan_name', 'plan_type', 'calories', 'id')->orderBY('id')->get();
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
            $dietPlan = new $this->model;

            $allColumns = $dietPlan->getTableColumns($dietPlan->getTable());

            foreach ($inputs as $key => $value) {
                if (in_array($key, $allColumns)) {
                    $dietPlan->$key = $value;
                }
            }
            $dietPlan->plan_name = ucfirst($inputs['plan_name']);
            $dietPlan->status = isset($inputs['status']) ? $inputs['status'] : 0;

            $save = $dietPlan->save();

            if ($save) {
                $response['status'] = 'success';
                $response['message'] = trans('admin::messages.added', ['name' => trans('admin::controller/diet-plan.diet-plan')]);
            } else {
                $response['status'] = 'error';
                $response['message'] = trans('admin::messages.not-added', ['name' => trans('admin::controller/diet-plan.diet-plan')]);
            }

            return $response;
        } catch (Exception $e) {
            $exceptionDetails = $e->getMessage();
            $response['status'] = 'error';
            $response['message'] = trans('admin::messages.not-added', ['name' => trans('admin::controller/diet-plan.diet-plan')]) . "<br /><b> Error Details</b> - " . $exceptionDetails;
            Log::error(trans('admin::messages.not-added', ['name' => trans('admin::controller/diet-plan.diet-plan')]), ['Error Message' => $exceptionDetails, 'Current Action' => Route::getCurrentRoute()->getActionName()]);

            return $response;
        }
    }

    /**
     * Update an diet pan.
     *
     * @param  Form data posted from ajax $inputs, Modules\Admin\Models\DietPlan $dietPlan
     * @return $result array with status and message elements
     * @return void
     */
    public function update($inputs, $dietPlan) {
        try {

            foreach ($inputs as $key => $value) {
                if (isset($dietPlan->$key)) {
                    $dietPlan->$key = $value;
                }
            }
            $dietPlan->plan_name = ucfirst($inputs['plan_name']);
            $dietPlan->status = isset($inputs['status']) ? $inputs['status'] : 0;

            $save = $dietPlan->save();

            if ($save) {
                $response['status'] = 'success';
                $response['message'] = trans('admin::messages.updated', ['name' => trans('admin::controller/diet-plan.diet-plan')]);
            } else {
                $response['status'] = 'error';
                $response['message'] = trans('admin::messages.not-updated', ['name' => trans('admin::controller/diet-plan.diet-plan')]);
            }

            return $response;
        } catch (Exception $e) {
            $exceptionDetails = $e->getMessage();
            $response['status'] = 'error';
            $response['message'] = trans('admin::messages.not-updated', ['name' => trans('admin::controller/diet-plan.diet-plan')]) . "<br /><b> Error Details</b> - " . $exceptionDetails;
            Log::error(trans('admin::messages.not-updated', ['name' => trans('admin::controller/diet-plan.diet-plan')]), ['Error Message' => $exceptionDetails, 'Current Action' => Route::getCurrentRoute()->getActionName()]);

            return $response;
        }
    }

    /**
     * Delete actions on diet plan
     *
     * @param  int  $status
     * @return int
     */
    public function deleteAction($inputs) {
        try {

            $resultStatus = false;

            $id = $inputs['ids'];

            $dietPlan = DietPlan::find($id);
            if (!empty($dietPlan)) {
                $dietPlan->delete();
                $resultStatus = true;
            }

            return $resultStatus;
        } catch (Exception $e) {
            $exceptionDetails = $e->getMessage();
            $response['status'] = 'error';
            $response['message'] = trans('admin::messages.not-deleted', ['name' => trans('admin::controller/diet-plan.diet-plan')]) . "<br /><b> Error Details</b> - " . $exceptionDetails;
            Log::error(trans('admin::messages.not-deleted', ['name' => trans('admin::controller/diet-plan.diet-plan')]), ['Error Message' => $exceptionDetails, 'Current Action' => Route::getCurrentRoute()->getActionName()]);

            return $response;
        }
    }

}
