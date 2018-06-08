<?php

/**
 * The class to present SessionBookings model.
 *
 *
 * @author Sachin Gend <saching@iprogrammer.com>
 * @package Admin
 * @since 1.0
 */

namespace Modules\Admin\Models;

class MemberSessionBookingResources extends BaseModel {

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'member_session_booking_resources';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['session_id', 'member_id', 'resource_id', 'resource_type', 'resource_start_time', 'resource_end_time', 'created_by', 'updated_by', 'created_at', 'updated_at'];

    public function sessionBooking() {
        return $this->belongsTo('Modules\Admin\Models\SessionBookings', 'session_id', 'id');
    }

    public function member() {
        return $this->belongsTo('Modules\Admin\Models\Member', 'member_session_bookings', 'id', 'member_id');
    }

}
