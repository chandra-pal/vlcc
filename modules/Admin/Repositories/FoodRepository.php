<?php

/**
 * The repository class for managing food specific actions.
 *
 *
 * @author Gauri Deshmukh <gaurid@iprogrammer.com>
 * @package Admin
 * @since 1.0
 */

namespace Modules\Admin\Repositories;

use Modules\Admin\Models\Food;
use Exception;
use Route;
use Log;
use Cache;

class FoodRepository extends BaseRepository {

    /**
     * Create a new FoodRepository instance.
     *
     * @param  Modules\Admin\Models\Food $model
     * @return void
     */
    public function __construct(Food $food) {
        $this->model = $food;
    }

    /**
     * Get a listing of the resource.
     *
     * @return Response
     */
    public function data($params = []) {
        $cacheKey = str_replace(['\\'], [''], __METHOD__) . ':' . md5(json_encode($params));
        //Cache::tags not suppport with files and Database
        Cache::tags(Food::table())->flush();
        $response = Cache::tags(Food::table())->remember($cacheKey, $this->ttlCache, function() {

            return Food::select('id', 'food_name', 'food_type_id', 'measure', 'calories')->with('FoodTypeSelect')
                            ->orderBy('id', 'DESC')
                            ->get();
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
        $response = Cache::tags(Food::table())->remember($cacheKey, $this->ttlCache, function() {
            return Food::orderBY('id')->lists('id', 'food_name', 'measure', 'calories', 'serving_size', 'serving_unit');
        });

        return $response;
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function listFoodData() {
        $cacheKey = str_replace(['\\'], [''], __METHOD__);
        //Cache::tags not suppport with files and Database
        $response = Cache::tags(Food::table())->remember($cacheKey, $this->ttlCache, function() {
            return Food::orderBY('id')->lists('food_name', 'id');
        });

        return $response;
    }

    public function getFoodType($food_id) {
        $cacheKey = str_replace(['\\'], [''], __METHOD__);
        //Cache::tags not suppport with files and Database
        $response = Cache::tags(Food::table())->remember($cacheKey, $this->ttlCache, function() use($food_id) {
            return Food::select('food_type_id')->whereId($food_id)->first();
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
            $food = new $this->model;

            $allColumns = $food->getTableColumns($food->getTable());

            foreach ($inputs as $key => $value) {
                if (in_array($key, $allColumns)) {
                    $food->$key = $value;
                }
            }
            $food->food_name = ucfirst($inputs['food_name']);
            $save = $food->save();
            if ($save) {
                $response['status'] = 'success';
                $response['food_id'] = $food->id;
                $response['message'] = trans('admin::messages.added', ['name' => trans('admin::controller/food.food')]);
            } else {
                $response['status'] = 'error';
                $response['message'] = trans('admin::messages.not-added', ['name' => trans('admin::controller/food.food')]);
                $response['food_id'] = '';
            }

            return $response;
        } catch (Exception $e) {
            $exceptionDetails = $e->getMessage();
            $response['status'] = 'error';
            $response['message'] = trans('admin::messages.not-added', ['name' => trans('admin::controller/food.food')]) . "<br /><b> Error Details</b> - " . $exceptionDetails;
            Log::error(trans('admin::messages.not-added', ['name' => trans('admin::controller/food.food')]), ['Error Message' => $exceptionDetails, 'Current Action' => Route::getCurrentRoute()->getActionName()]);

            return $response;
        }
    }

    /**
     * Update an food.
     *
     * @param  Form data posted from ajax $inputs, Modules\Admin\Models\Food $food
     * @return $result array with status and message elements
     * @return void
     */
    public function update($inputs, $food) {
        try {

            foreach ($inputs as $key => $value) {
                if (isset($food->$key)) {
                    $food->$key = $value;
                }
            }
            $food->food_name = ucfirst($inputs['food_name']);
            $save = $food->save();

            if ($save) {
                $response['status'] = 'success';
                $response['message'] = trans('admin::messages.updated', ['name' => trans('admin::controller/food.food')]);
            } else {
                $response['status'] = 'error';
                $response['message'] = trans('admin::messages.not-updated', ['name' => trans('admin::controller/food.food')]);
            }

            return $response;
        } catch (Exception $e) {
            $exceptionDetails = $e->getMessage();
            $response['status'] = 'error';
            $response['message'] = trans('admin::messages.not-updated', ['name' => trans('admin::controller/food.food')]) . "<br /><b> Error Details</b> - " . $exceptionDetails;
            Log::error(trans('admin::messages.not-updated', ['name' => trans('admin::controller/food.food')]), ['Error Message' => $exceptionDetails, 'Current Action' => Route::getCurrentRoute()->getActionName()]);

            return $response;
        }
    }

    /**
     * Delete actions on food
     *
     * @param  int  $status
     * @return int
     */
    public function deleteAction($inputs) {
        try {

            $resultStatus = false;

            $id = $inputs['ids'];

            $food = Food::find($id);
            if (!empty($food)) {
                $food->delete();
                $resultStatus = true;
            }

            return $resultStatus;
        } catch (Exception $e) {
            $exceptionDetails = $e->getMessage();
            $response['status'] = 'error';
            $response['message'] = trans('admin::messages.not-deleted', ['name' => trans('admin::controller/food.food')]) . "<br /><b> Error Details</b> - " . $exceptionDetails;
            Log::error(trans('admin::messages.not-deleted', ['name' => trans('admin::controller/food.food')]), ['Error Message' => $exceptionDetails, 'Current Action' => Route::getCurrentRoute()->getActionName()]);

            return $response;
        }
    }

    public function getFoodDetails($params) {
        return Food::select([
                    'measure', 'calories', 'serving_size', 'serving_unit'
                ])->where('id', $params['food_id'])->orderBy('id')->get();
    }

    // Function to Update food status as Inactive if same food name exists & created by Customer
    public function updateFood($foodName, $foodTypeId) {
        return Food::where('created_by_user_type', '=', 0)
                        ->where('food_name', "=", $foodName)
                        ->where('food_type_id', "=", $foodTypeId)
                        ->update(array('status' => 0));
    }

}
