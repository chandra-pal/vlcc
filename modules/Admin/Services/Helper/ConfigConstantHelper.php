<?php
/**
 * This class is for fetching Config Constants
 *
 *
 * @author Rahul Kadu <rahulk@iprogrammer.com>
 * @package App\Libraries
 */
namespace Modules\Admin\Services\Helper;

use Modules\Admin\Repositories\ConfigSettingRepository;
use Modules\Admin\Models\ConfigSetting;
use Cache;

/**
 * 	Convenient method to fetch Constant value from ConfigSettings
 */
class ConfigConstantHelper
{

    /**
     * To get value for constant
     * */
    public static function getValue($constant)
    {
        $params = [];
        $params['config_constant'] = $constant;
        $cacheKey = str_replace(['\\'], [''], __METHOD__) . ':' . md5(json_encode($params));
        $configSetting = Cache::tags(ConfigSetting::table())->remember($cacheKey, 60, function() use ($constant) {
            return ConfigSetting::where('config_constant', $constant)->first();
        });
        if (!($configSetting instanceof ConfigSetting)) {
            throw new \Dingo\Api\Exception\ResourceException('Constant Not Found');
        }
        return $configSetting->config_value;
    }
}
