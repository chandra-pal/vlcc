<?php

/**

 * The class to present StaffAvailability model.
 *
 *
 * @author Bhawna Thadhani <bhawnat@iprogrammer.com>
 * @package Admin
 * @since 1.0
 */

namespace Modules\Admin\Models;

class StaffAvailability extends BaseModel {

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'staff_availability';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['id', 'center_id', 'staff_id', 'availability_date', 'start_time', 'end_time', 'break_time', 'carry_forward_availability', 'carry_forward_availability_days', 'status', 'created_at', 'updated_at'];

    /**
     * get model when used in join
     *
     * @return type
     */
    public function staffCenter() {
        return $this->belongsTo('Modules\Admin\Models\Center', 'center_id', 'id');
    }

    /**
     * get model when used in join
     *
     * @return type
     */
    public function availabilityDietianStaff() {
        return $this->belongsTo('Modules\Admin\Models\User', 'staff_id', 'id');
    }

}
