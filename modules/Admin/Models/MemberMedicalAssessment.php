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

class MemberMedicalAssessment extends BaseModel {

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'member_medical_assessment';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['member_id', 'current_associated_medical_problem', 'epilepsy', 'other', 'physical_finding', 'systemic_examination', 'gynae_obstetrics_history', 'clients_birth_weight', 'sleeping_pattern', 'past_mediacl_history', 'family_history_of_diabetes_obesity', 'detailed_history', 'treatment_history', 'suggested_investigation', 'followup_date', 'doctors_name','assessment_date'];

}
