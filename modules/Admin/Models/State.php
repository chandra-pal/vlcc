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

class State extends BaseModel
{

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'states';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name', 'country_id', 'state_code', 'status'];

    /**
     * get model when used in join
     * 
     * @return type
     */
    public function country()
    {
        return $this->belongsTo('Modules\Admin\Models\Country', 'country_id', 'id');
    }

    /**
     * get name of the country from Country model when used in join
     * 
     * @return type
     */
    public function city()
    {
        return $this->hasMany('Modules\Admin\Models\City');
    }
}
