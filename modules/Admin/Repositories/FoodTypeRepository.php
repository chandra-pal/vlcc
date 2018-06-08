<?php

/**
 * The repository class for managing food type specific actions.
 *
 *
 * @author Priyanka Deshpande <priyankadd@iprogrammer.com>
 * @package Admin
 * @since 1.0
 */

namespace Modules\Admin\Repositories;

use Modules\Admin\Models\FoodType;
use Exception;
use Route;
use Log;
use Cache;

class FoodTypeRepository extends BaseRepository {

    /**
     * Create a new FoodTypeRepository instance.
     *
     * @param  Modules\Admin\Models\FoodType $model
     * @return void
     */
    public function __construct(FoodType $foodType) {
        $this->model = $foodType;
    }

    /**
     * Get a listing of the resource.
     *
     * @return Response
     */
    public function data($params = []) {
        $cacheKey = str_replace(['\\'], [''], __METHOD__) . ':' . md5(json_encode($params));
        //Cache::tags not suppport with files and Database
        $response = Cache::tags(FoodType::table())->remember($cacheKey, $this->ttlCache, function() {
            return FoodType::select([
                        'id', 'food_type_name', 'status'
                    ])->orderBy('id','DESC')->get();
        });

        return $response;
    }

    public function listFoodTypesData() {
        //Cache::tags('home')->flush();
        $cacheKey = str_replace(['\\'], [''], __METHOD__);
        //Cache::tags not suppport with files and Database
        $response = Cache::tags(FoodType::table())->remember($cacheKey, $this->ttlCache, function() {
            return FoodType::where('status','=',1)->orderBY('id', 'desc')->lists('food_type_name', 'id');
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
            $foodType = new $this->model;

            $allColumns = $foodType->getTableColumns($foodType->getTable());

            foreach ($inputs as $key => $value) {
                if (in_array($key, $allColumns)) {
                    $foodType->$key = $value;
                }
            }
            $foodType->status = isset($inputs['status']) ? $inputs['status'] : 0;
            $foodType->food_type_name = ucwords($foodType->food_type_name);

            $save = $foodType->save();

            if ($save) {
                $response['status'] = 'success';
                $response['message'] = trans('admin::messages.added', ['name' => trans('admin::controller/food-type.food-type')]);
            } else {
                $response['status'] = 'error';
                $response['message'] = trans('admin::messages.not-added', ['name' => trans('admin::controller/food-type.food-type')]);
            }

            return $response;
        } catch (Exception $e) {
            $exceptionDetails = $e->getMessage();
            $response['status'] = 'error';
            $response['message'] = trans('admin::messages.not-added', ['name' => trans('admin::controller/food-type.food-type')]) . "<br /><b> Error Details</b> - " . $exceptionDetails;
            Log::error(trans('admin::messages.not-added', ['name' => trans('admin::controller/food-type.food-type')]), ['Error Message' => $exceptionDetails, 'Current Action' => Route::getCurrentRoute()->getActionName()]);

            return $response;
        }
    }

    /**
     * Update an activity type.
     *
     * @param  Form data posted from ajax $inputs, Modules\Admin\Models\FoodType $foodType
     * @return $result array with status and message elements
     * @return void
     */
    public function update($inputs, $foodType) {
        try {

            foreach ($inputs as $key => $value) {
                if (isset($foodType->$key)) {
                    $foodType->$key = $value;
                }
            }
            $foodType->status = isset($inputs['status']) ? $inputs['status'] : 0;
            $foodType->food_type_name = ucwords($foodType->food_type_name);
            
            $save = $foodType->save();

            if ($save) {
                $response['status'] = 'success';
                $response['message'] = trans('admin::messages.updated', ['name' => trans('admin::controller/food-type.food-type')]);
            } else {
                $response['status'] = 'error';
                $response['message'] = trans('admin::messages.not-updated', ['name' => trans('admin::controller/food-type.food-type')]);
            }

            return $response;
        } catch (Exception $e) {
            $exceptionDetails = $e->getMessage();
            $response['status'] = 'error';
            $response['message'] = trans('admin::messages.not-updated', ['name' => trans('admin::controller/food-type.food-type')]) . "<br /><b> Error Details</b> - " . $exceptionDetails;
            Log::error(trans('admin::messages.not-updated', ['name' => trans('admin::controller/food-type.food-type')]), ['Error Message' => $exceptionDetails, 'Current Action' => Route::getCurrentRoute()->getActionName()]);

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
            $foodType = FoodType::find($id);
            if (!empty($foodType)) {
                $foodType->delete();
                $resultStatus = true;
            }

            return $resultStatus;
        } catch (Exception $e) {
            $exceptionDetails = $e->getMessage();
            $response['status'] = 'error';
            $response['message'] = trans('admin::messages.not-deleted', ['name' => trans('admin::controller/food-type.food-type')]) . "<br /><b> Error Details</b> - " . $exceptionDetails;
            Log::error(trans('admin::messages.not-deleted', ['name' => trans('admin::controller/food-type.food-type')]), ['Error Message' => $exceptionDetails, 'Current Action' => Route::getCurrentRoute()->getActionName()]);

            return $response;
        }
    }

}
