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

class MemberBcaDetails extends BaseModel {

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'member_bca_details';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['package_id', 'member_id', 'recorded_date', 'body_mass_index', 'basal_metabolic_rate', 'fat_weight', 'fat_percent', 'lean_body_mass_weight', 'lean_body_mass_percent', 'water_weight', 'water_percent', 'visceral_fat_level', 'visceral_fat_area', 'target_weight', 'target_fat_percent', 'created_by', 'updated_by'];
    
//    public function member() {
//        return $this->belongsTo('Modules\Admin\Models\Member');
//    }
    
//    public function memberBcaDetails() {
//        return $this->belongsTo('Modules\Admin\Models\MemberBcaDetails');
//    }

}
