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

class MemberActivityFitnessReview extends BaseModel {

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'member_fintness_activity_review';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['id', 'member_id', 'static_posture_score', 'sit_and_reach_test', 'right_shoulder_flexibility', 'left_shoulder_flexibility', 'pulse', 'slr', 'specific_activity_advice', 'specific_activity_duration', 'physiotherapist_name', 'precautions_and_contraindications', 'review_date'];

}
