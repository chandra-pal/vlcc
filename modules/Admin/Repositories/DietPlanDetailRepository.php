<?php

/**
 * The repository class for managing diet plan details specific actions.
 *
 *
 * @author Gauri Deshmukh <gaurid@iprogrammer.com>
 * @package Admin
 * @since 1.0
 */

namespace Modules\Admin\Repositories;

use Modules\Admin\Models\DietPlanDetail;
use Modules\Admin\Models\DietPlan;
use Modules\Admin\Models\DietScheduleType;
use Modules\Admin\Models\Food;
use Exception;
use Route;
use Log;
use Cache;

class DietPlanDetailRepository extends BaseRepository {

    /**
     * Create a new DietPlanDetailRepository instance.
     *
     * @param  Modules\Admin\Models\DietPlanDetail $model
     * @return void
     */
    public function __construct(DietPlanDetail $dietPlanDetail) {
        $this->model = $dietPlanDetail;
    }

    /**
     * Get a listing of the resource.
     *
     * @return Response
     */
    public function data($params = []) {
        $cacheKey = str_replace(['\\'], [''], __METHOD__) . ':' . md5(json_encode($params));

        $response = Cache::tags(DietPlanDetail::table(), DietPlan::table(), DietScheduleType::table(), Food::table())->remember($cacheKey, $this->ttlCache, function() {
            return DietPlanDetail::with('DietPlan', 'DietScheduleType', 'Food')->orderBy('id')->get();
        });

        return $response;
    }

    /**
     * Get a listing of the selected food list.
     *
     * @return Response
     */
    public function getSelectedFoodlist($params = []) {
        $cacheKey = str_replace(['\\'], [''], __METHOD__) . ':' . md5(json_encode($params));
        $response = Cache::tags(DietPlanDetail::table())->remember($cacheKey, $this->ttlCache, function() use($params) {
            return DietPlanDetail::select('food_id')->where('diet_plan_id', '=', $params['diet_plan_id'])
                            ->where('diet_schedule_type_id', '=', $params['schedule_id'])->lists('food_id')->toArray();
        });
        return $response;
    }

    /**
     * Get a listing of the food list.
     *
     * @return Response
     */
    public function getFoodList($getSelectedFoodlist = [], $params = []) {
        $cacheKey = str_replace(['\\'], [''], __METHOD__) . ':' . md5(json_encode($getSelectedFoodlist));
        Cache::flush();
        $response = Cache::tags(Food::table())->remember($cacheKey, $this->ttlCache, function() use($getSelectedFoodlist, $params) {
            return Food::select('food_name', 'id')->whereNotIn('id', $getSelectedFoodlist)->whereFoodTypeId($params['food_type_id'])->lists('food_name', 'id')->toArray();
        });
        return $response;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  Form data posted from ajax $inputs
     * @return $result array with status and message elements
     */
    public function create($inputs) {
        try {
            foreach ($inputs['food_id'] as $foodKey => $foodId) {
                $dietPlanDetail = new $this->model;

                $allColumns = $dietPlanDetail->getTableColumns($dietPlanDetail->getTable());
                foreach ($inputs as $key => $value) {
                    if (in_array($key, $allColumns)) {
                        $dietPlanDetail->$key = $value;
                    }
                    $dietPlanDetail->food_id = $inputs['food_id'][$foodKey];
                }
//                $dietPlanDetail->status = isset($inputs['status']) ? $inputs['status'] : 0;

                $save = $dietPlanDetail->save();
            }

            if ($save) {
                $response['status'] = 'success';
                $response['message'] = trans('admin::messages.added', ['name' => trans('admin::controller/diet-plan-detail.diet-plan-detail')]);
            } else {
                $response['status'] = 'error';
                $response['message'] = trans('admin::messages.not-added', ['name' => trans('admin::controller/diet-plan-detail.diet-plan-detail')]);
            }

            return $response;
        } catch (Exception $e) {
            $exceptionDetails = $e->getMessage();
            $response['status'] = 'error';
            $response['message'] = trans('admin::messages.not-added', ['name' => trans('admin::controller/diet-plan-detail.diet-plan-detail')]) . "<br /><b> Error Details</b> - " . $exceptionDetails;
            Log::error(trans('admin::messages.not-added', ['name' => trans('admin::controller/diet-plan-detail.diet-plan-detail')]), ['Error Message' => $exceptionDetails, 'Current Action' => Route::getCurrentRoute()->getActionName()]);

            return $response;
        }
    }

    /**
     * Update an diet plan detail.
     *
     * @param  Form data posted from ajax $inputs, Modules\Admin\Models\DietPlanDetail $dietPlanDetail
     * @return $result array with status and message elements
     * @return void
     */
    public function update($inputs, $dietPlanDetail) {
        try {
            $check = DietPlanDetail::select('id')->where('diet_plan_id', '=', $inputs['diet_plan_id'])
                            ->where('diet_schedule_type_id', '=', $inputs['diet_schedule_type_id'])->where('food_id', '=', $inputs['food_id'])->exists();
            if ($check == true) {
                $response['status'] = 'error';
                $response['message'] = trans('admin::messages.error-taken', ['name' => trans('admin::controller/diet-plan-detail.diet-plan-detail')]);
            } else {
                foreach ($inputs['food_id'] as $foodKey => $foodId) {
                    foreach ($inputs as $key => $value) {
                        if (isset($dietPlanDetail->$key)) {
                            $dietPlanDetail->$key = $value;
                        }
                        $dietPlanDetail->food_id = $inputs['food_id'][$foodKey];
                    }

//                    $dietPlanDetail->status = isset($inputs['status']) ? $inputs['status'] : 0;

                    $save = $dietPlanDetail->save();
                }
                if ($save) {
                    $response['status'] = 'success';
                    $response['message'] = trans('admin::messages.updated', ['name' => trans('admin::controller/diet-plan-detail.diet-plan-detail')]);
                } else {
                    $response['status'] = 'error';
                    $response['message'] = trans('admin::messages.not-updated', ['name' => trans('admin::controller/diet-plan-detail.diet-plan-detail')]);
                }
            }
            return $response;
        } catch (Exception $e) {
            $exceptionDetails = $e->getMessage();
            $response['status'] = 'error';
            $response['message'] = trans('admin::messages.not-updated', ['name' => trans('admin::controller/diet-plan-detail.diet-plan-detail')]) . "<br /><b> Error Details</b> - " . $exceptionDetails;
            Log::error(trans('admin::messages.not-updated', ['name' => trans('admin::controller/diet-plan-detail.diet-plan-detail')]), ['Error Message' => $exceptionDetails, 'Current Action' => Route::getCurrentRoute()->getActionName()]);

            return $response;
        }
    }

    /**
     * Delete actions on diet plan detail
     *
     * @param  int  $status
     * @return int
     */
    public function deleteAction($inputs) {
        try {

            $resultStatus = false;

            $id = $inputs['ids'];
            $dietPlanDetail = DietPlanDetail::find($id);
            if (!empty($dietPlanDetail)) {
                $dietPlanDetail->delete();
                $resultStatus = true;
            }

            return $resultStatus;
        } catch (Exception $e) {
            $exceptionDetails = $e->getMessage();
            $response['status'] = 'error';
            $response['message'] = trans('admin::messages.not-deleted', ['name' => trans('admin::controller/diet-plan-detail.diet-plan-detail')]) . "<br /><b> Error Details</b> - " . $exceptionDetails;
            Log::error(trans('admin::messages.not-deleted', ['name' => trans('admin::controller/diet-plan-detail.diet-plan-detail')]), ['Error Message' => $exceptionDetails, 'Current Action' => Route::getCurrentRoute()->getActionName()]);

            return $response;
        }
    }

}
