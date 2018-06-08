<?php
/**
 * The class to present ConfigSetting model.
 * 
 * 
 * @author Tushar Dahiwale <tushard@iprogrammer.com>
 * @package Admin
 * @since 1.0
 */

namespace Modules\Admin\Models;

use Modules\Admin\Models\BaseModel;

class ConfigSetting extends BaseModel
{

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'config_settings';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['config_constant', 'config_value', 'desc', 'config_category_id'];

    /**
     * get name of the category from ConfigCategory model when used in join
     * 
     * @return type
     */
    public function configCategory()
    {
        return $this->belongsTo('Modules\Admin\Models\ConfigCategory', 'config_category_id', 'id');
    }
}
