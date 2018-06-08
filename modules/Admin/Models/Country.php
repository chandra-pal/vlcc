<?php
/**
 * The class to present Country model.
 * 
 * 
 * @author Tushar Dahiwale <tushard@iprogrammer.com>
 * @package Admin
 * @since 1.0
 */

namespace Modules\Admin\Models;

use Modules\Admin\Models\BaseModel;

class Country extends BaseModel
{

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'countries';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name', 'iso_code_2', 'iso_code_3', 'isd_code', 'status'];

    /**
     * get model when used in join
     * 
     * @return type
     */
    public function state()
    {
        return $this->hasMany('Modules\Admin\Models\State');
    }
}
