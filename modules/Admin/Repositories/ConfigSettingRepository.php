<?php
/**
 * The repository class for managing configuration settings specific actions.
 *
 *
 * @author Tushar Dahiwale <tushard@iprogrammer.com>
 * @package Admin
 * @since 1.0
 */
namespace Modules\Admin\Repositories;

use Modules\Admin\Models\ConfigSetting;
use Modules\Admin\Models\ConfigCategory;
use Exception;
use Route;
use Log;
use Cache;

class ConfigSettingRepository extends BaseRepository
{

    /**
     * Create a new model ConfigSetting instance.
     *
     * @param Modules\Admin\Models\ConfigSetting $configSetting
     * @return void
     */
    public function __construct(ConfigSetting $configSetting)
    {
        $this->model = $configSetting;
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
        $response = Cache::tags(ConfigCategory::table(), ConfigSetting::table())->remember($cacheKey, $this->ttlCache, function() {
            return ConfigSetting::with('ConfigCategory')->orderBy('config_category_id')->get();
        });

        return $response;
    }

    /**
     * Display a listing of the resource. Using this to fetch the config constants gloabally
     *
     * @return Response
     */
    public function getSettingsData($params = [])
    {
        $list = array();
        $cacheKey = str_replace(['\\'], [''], __METHOD__) . ':' . md5(json_encode($params));
        //Cache::tags not suppport with files and Database
        $configCategories = ConfigCategory::table();
        $response = Cache::tags(ConfigCategory::table(), ConfigSetting::table())->remember($cacheKey, $this->ttlCache, function() use($configCategories) {
            return ConfigSetting::join($configCategories, 'config_settings.config_category_id', '=', $configCategories . '.id')
                    ->select(['config_settings.id', $configCategories . '.category', 'config_category_id', 'description', 'config_constant', 'config_value'])
                    ->orderBy($configCategories . '.id')->get();
        });
        $response->toArray();
        foreach ($response as $category => $settingsArray) {
            $list[$settingsArray['config_constant']] = $settingsArray['config_value'];
        }

        return $list;
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
            $configSetting = new $this->model;
            $allColumns = $configSetting->getTableColumns($configSetting->getTable());
            foreach ($inputs as $key => $value) {
                if (in_array($key, $allColumns)) {
                    $configSetting->$key = $value;
                }
            }

            $configCategory = ConfigCategory::find($inputs['config_category_id']);
            $configSetting->configCategory()->associate($configCategory);

            $save = $configSetting->save();

            if ($save) {
                $response['status'] = 'success';
                $response['message'] = trans('admin::messages.added', ['name' => trans('admin::controller/config-setting.conf-set')]);
            } else {
                $response['status'] = 'error';
                $response['message'] = trans('admin::messages.not-added', ['name' => trans('admin::controller/config-setting.conf-set')]);
            }
            Cache::tags(ConfigCategory::table())->flush();
            return $response;
        } catch (Exception $e) {
            $exceptionDetails = $e->getMessage();
            $response['status'] = 'error';
            $response['message'] = trans('admin::messages.not-added', ['name' => trans('admin::controller/config-setting.conf-set')]) . "<br /><b> Error Details</b> - " . $exceptionDetails;
            Log::error(trans('admin::messages.not-added', ['name' => trans('admin::controller/config-setting.conf-set')]), ['Error Message' => $exceptionDetails, 'Current Action' => Route::getCurrentRoute()->getActionName()]);

            return $response;
        }
    }

    /**
     * Update a configuration settting.
     *
     * @param  Form data posted from ajax $inputs, Modules\Admin\Models\ConfigSetting $configSetting
     * @return $result array with status and message elements
     */
    public function update($inputs, $configSetting)
    {
        try {

            foreach ($inputs as $key => $value) {
                if (isset($configSetting->$key)) {
                    $configSetting->$key = $value;
                }
            }

            $configCategory = ConfigCategory::find($inputs['config_category_id']);
            $configSetting->configCategory()->associate($configCategory);

            $save = $configSetting->save();

            if ($save) {
                $response['status'] = 'success';
                $response['message'] = trans('admin::messages.updated', ['name' => trans('admin::controller/config-setting.conf-set')]);
            } else {
                $response['status'] = 'error';
                $response['message'] = trans('admin::messages.not-updated', ['name' => trans('admin::controller/config-setting.conf-set')]);
            }
            Cache::tags(ConfigCategory::table())->flush();
            return $response;
        } catch (Exception $e) {

            $exceptionDetails = $e->getMessage();
            $response['status'] = 'error';
            $response['message'] = trans('admin::messages.not-updated', ['name' => trans('admin::controller/config-setting.conf-set')]) . "<br /><b> Error Details</b> - " . $exceptionDetails;
            Log::error(trans('admin::messages.not-updated', ['name' => trans('admin::controller/config-setting.conf-set')]), ['Error Message' => $exceptionDetails, 'Current Action' => Route::getCurrentRoute()->getActionName()]);

            return $response;
        }
    }
}
