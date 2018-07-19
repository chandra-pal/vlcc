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

class MemberPackage extends BaseModel {

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'member_packages';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['member_id', 'crm_package_id', 'package_title', 'start_date', 'end_date', 'height', 'weight', 'waist', 'total_payment', 'payment_made', 'package_start_weight', 'package_target_weight', 'conversion', 'programme_re_booked', 'remarks', 'created_by', 'updated_by', 'created_at', 'updated_at'];

    public function member() {
        return $this->belongsTo('Modules\Admin\Models\Member');
    }

    public function memberPackage() {
        return $this->belongsTo('Modules\Admin\Models\MemberPackage');
    }
    
    public function services() {
        return $this->hasMany('Modules\Admin\Models\MemberPackageServices', 'package_id', 'id');
    }
    
    public function packageCenter() {
        return $this->belongsTo('Modules\Admin\Models\Center', 'crm_center_id', 'crm_center_id');
    }
}
