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

class MemberSkinHairAnalysis extends BaseModel {

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'member_skin_hair_analysis';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['member_id', 'skin_type', 'skin_condition', 'hyperpigmentation_type', 'hyperpigmentation_size', 'hyperpigmentation_depth', 'scars_depth', 'scars_size', 'scars_pigmented', 'fine_lines_and_wrinkles', 'skin_curvature', 'other_marks', 'hair_type', 'condition_of_scalp', 'hair_density', 'condition_of_hair_shaft', 'history_of_allergy', 'conclusion', 'skin_and_hair_specialist_name', 'analysis_date'];

}
