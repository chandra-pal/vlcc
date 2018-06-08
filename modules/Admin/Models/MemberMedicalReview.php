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

class MemberMedicalReview extends BaseModel {

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'member_medical_review';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['id', 'member_id', 'date', 'advice', 'created_by'];

}
