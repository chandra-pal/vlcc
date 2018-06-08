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

class MemberDietaryAssessment extends BaseModel {

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'member_dietary_assessment';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['member_id', 'food_allergy', 'smoking', 'smoking_frequency', 'meals_per_day', 'food_habbit', 'eat_out_per_week', 'alcohol', 'alcohol_frequency', 'diet_total_calories', 'diet_cho', 'diet_protein', 'diet_fat', 'remark', 'wellness_counsellor_name', 'assessment_date', 'created_at'];

}
