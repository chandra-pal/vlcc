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

class City extends BaseModel
{

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'cities';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['country_id', 'state_id', 'name', 'status'];

    /**
     * get name of the state from State model when used in join
     * 
     * @return type
     */
    public function states()
    {
        return $this->belongsTo('Modules\Admin\Models\State', 'state_id', 'id');
    }

    /**
     * get name of the state from State model when used in join
     * 
     * @return type
     */
    public function country()
    {
        return $this->belongsTo('Modules\Admin\Models\Country', 'country_id', 'id');
    }
}
