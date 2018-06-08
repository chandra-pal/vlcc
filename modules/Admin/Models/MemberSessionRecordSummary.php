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

class MemberSessionRecordSummary extends BaseModel {

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'member_session_record_summary';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['package_id', 'member_id', 'session_id', 'recorded_date', 'net_weight_loss', 'net_weight_gain', 'balance_programme_kg', 'created_by', 'updated_by'];
    
}
