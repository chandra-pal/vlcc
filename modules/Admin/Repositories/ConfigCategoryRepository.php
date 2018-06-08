<?php
/**
 * The repository class for managing categories specific actions.
 * 
 * 
 * @author Tushar Dahiwale <tushard@iprogrammer.com>
 * @package Admin
 * @since 1.0
 */

namespace Modules\Admin\Repositories;

use Modules\Admin\Models\ConfigCategory;
use Exception;
use Route;
use Log;
use Cache;

class ConfigCategoryRepository extends BaseRepository
{

    /**
     * Create a new model ConfigCategory instance.
     *
     * 
     * @param Modules\Admin\Models\ConfigCategory $configCategory
     * @return void
     */
    public function __construct(ConfigCategory $configCategory)
    {
        $this->model = $configCategory;
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function data($params = [])
    {
        $cacheKey = str_replace(['\\'], [''], __METHOD__) . ':' . md5(json_encode($params));
        //Cache::tags not suppport with files and Database
        $response = Cache::tags(ConfigCategory::table())->remember($cacheKey, $this->ttlCache, function() {
            return ConfigCategory::select([
                    'id', 'category', 'created_by'
                ])->orderBy('id')->get();
        });

        return $response;
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function listCategoryData()
    {
        $cacheKey = str_replace(['\\'], [''], __METHOD__);
        //Cache::tags not suppport with files and Database
        $response = Cache::tags(ConfigCategory::table())->remember($cacheKey, $this->ttlCache, function() {
            return ConfigCategory::orderBY('id')->lists('category', 'id');
        });

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
            $configCategory = new $this->model;

            $allColumns = $configCategory->getTableColumns($configCategory->getTable());

            foreach ($inputs as $key => $value) {
                if (in_array($key, $allColumns)) {
                    $configCategory->$key = $value;
                }
            }

            $save = $configCategory->save();

            if ($save) {
                $response['status'] = 'success';
                $response['message'] = trans('admin::messages.added', ['name' => trans('admin::controller/config-category.config-cat')]);
            } else {
                $response['status'] = 'error';
                $response['message'] = trans('admin::messages.not-added', ['name' => trans('admin::controller/config-category.config-cat')]);
            }

            return $response;
        } catch (Exception $e) {
            $exceptionDetails = $e->getMessage();
            $response['status'] = 'error';
            $response['message'] = trans('admin::messages.not-added', ['name' => trans('admin::controller/config-category.config-cat')]) . "<br /><b> Error Details</b> - " . $exceptionDetails;
            Log::error(trans('admin::messages.not-added', ['name' => trans('admin::controller/config-category.config-cat')]), ['Error Message' => $exceptionDetails, 'Current Action' => Route::getCurrentRoute()->getActionName()]);

            return $response;
        }
    }

    /**
     * Update a category.
     *
     * @param  Form data posted from ajax $inputs, Modules\Admin\Models\ConfigCategory $configCategory
     * @return $result array with status and message elements
     * @return void
     */
    public function update($inputs, $configCategory)
    {
        try {

            foreach ($inputs as $key => $value) {
                if (isset($configCategory->$key)) {
                    $configCategory->$key = $value;
                }
            }

            $save = $configCategory->save();

            if ($save) {
                $response['status'] = 'success';
                $response['message'] = trans('admin::messages.updated', ['name' => trans('admin::controller/config-category.config-cat')]);
            } else {
                $response['status'] = 'error';
                $response['message'] = trans('admin::messages.not-updated', ['name' => trans('admin::controller/config-category.config-cat')]);
            }

            return $response;
        } catch (Exception $e) {

            $exceptionDetails = $e->getMessage();
            $response['status'] = 'error';
            $response['message'] = trans('admin::messages.not-updated', ['name' => trans('admin::controller/config-category.config-cat')]) . "<br /><b> Error Details</b> - " . $exceptionDetails;
            Log::error(trans('admin::messages.not-updated', ['name' => trans('admin::controller/config-category.config-cat')]), ['Error Message' => $exceptionDetails, 'Current Action' => Route::getCurrentRoute()->getActionName()]);

            return $response;
        }
    }
}
