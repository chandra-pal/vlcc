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

class MemberFitnessAssessment extends BaseModel {

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'member_fitness_assessment';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['member_id', 'static_posture', 'sit_and_reach_test', 'shoulder_flexibility_right', 'shoulder_flexibility_left', 'pulse', 'back_problem_test', 'current_activity_pattern', 'current_activity_type', 'current_activity_frequency', 'current_activity_duration', 'remark', 'home_care_kit', 'physiotherapist_name', 'assessment_date', 'created_at'];

}
