<?php
/**
 * The class to present ConfigCategory model.
 * 
 * 
 * @author Tushar Dahiwale <tushard@iprogrammer.com>
 * @package Admin
 * @since 1.0
 */

namespace Modules\Admin\Models;

use Modules\Admin\Models\BaseModel;

class ConfigCategory extends BaseModel
{

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'config_categories';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['category', 'position'];

    /**
     * get name of the associated settings from ConfigSetting model when used in join
     * 
     * @return type
     */
    public function configSetting()
    {
        return $this->hasMany('Modules\Admin\Models\ConfigSetting', 'id', 'config_category_id');
    }
}
