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

class MemberSessionRecord extends BaseModel {

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'member_session_record';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['package_id', 'member_id', 'session_id', 'recorded_date', 'bp', 'before_weight', 'after_weight', 'a_code', 'diet_and_activity_deviation', 'therapist_id', 'otp_verified', 'created_by', 'updated_by'];

}
