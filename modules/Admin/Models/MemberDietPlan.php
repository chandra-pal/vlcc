<?php

/**
 * The class to present Member Diet Plan model.
 * 
 * 
 * @author Gauri Deshmukh <gaurid@iprogrammer.com>
 * @package Admin
 * @since 1.0
 */

namespace Modules\Admin\Models;

class MemberDietPlan extends BaseModel {

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'members';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['first_name', 'last_name', 'status'];

    /**
     * get name of the diet plan from DietPlan model when used in join
     * 
     * @return type
     */
    public function dietPlan() {
        return $this->hasOne('Modules\Admin\Models\DietPlan', 'id', 'diet_plan_id');
    }

}
