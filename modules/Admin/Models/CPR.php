<?php

/**
 * The class to present CPR model.
 * 
 * 
 * @author Sachin Gend <saching@iprogrammer.com>
 * @package Admin
 * @since 1.0
 */

namespace Modules\Admin\Models;

class CPR extends BaseModel {

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'member_session_bookings';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [];

    /**
     * get model when used in join
     * 
     * @return type
     */
    public function activity() {
        return $this->hasMany('Modules\Admin\Models\SessionBookings', 'session_booking_id', 'id');
    }

}
