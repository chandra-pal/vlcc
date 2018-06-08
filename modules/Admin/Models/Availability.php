<?php

/**
 * The class to present Availability model.
 * 
 * 
 * @author Sachin Gend <saching@iprogrammer.com>
 * @package Admin
 * @since 1.0
 */

namespace Modules\Admin\Models;

class Availability extends BaseModel {

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'dietician_availability';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['availability_date', 'start_time', 'end_time', 'break_time', 'dietician_id', 'created_by', 'updated_by', 'status', 'carry_forward_availability', 'carry_forward_availability_days'];

    /**
     * get model when used in join
     * 
     * @return type
     */
    public function activity() {
        return $this->hasMany('Modules\Admin\Models\Admin', 'dietician_id', 'id');
    }

}
