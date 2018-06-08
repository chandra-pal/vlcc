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

class SessionBookings extends BaseModel
{

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
    protected $fillable = ['member_id', 'dietician_id', 'package_id', 'session_date', 'start_time', 'end_time', 'dietitian_comment', 'doctor_comment', 'physiotherpist_comment', 'ola_cab_required', 'attendance_status', 'session_comment', 'cancellation_comment', 'status', 'created_by', 'updated_by', 'created_at', 'updated_at'];

    public function member()
    {
        return $this->belongsTo('Modules\Admin\Models\Member', 'member_id', 'id');
    }

    public function memberPackage()
    {
        return $this->hasOne('Modules\Admin\Models\MemberPackage', 'id', 'package_id')->with('services');
    }

    public function availability()
    {
        return $this->belongsTo('Modules\Admin\Models\MemberSessionBookingResources', 'session_id', 'id');
    }
}
