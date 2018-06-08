<?php

/**
 * The class to present SessionBookings model.
 * 
 * 
 * @author Gauri Deshmukh <gaurid@iprogrammer.com>
 * @package Admin
 * @since 1.0
 */

namespace Modules\Admin\Models;

class MemberMeasurementDetails extends BaseModel {

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'member_measurement_details';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['package_id', 'member_id', 'recorded_date', 'neck', 'chest', 'arms', 'tummy', 'waist', 'hips', 'thighs', 'total_cm_loss', 'therapist_name', 'created_by', 'updated_by'];

}
