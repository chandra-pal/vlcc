<?php

/**
 * The class to present DietPlanDetail model.
 * 
 * 
 * @author Gauri Deshmukh <gaurid@iprogrammer.com>
 * @package Admin
 * @since 1.0
 */

namespace Modules\Admin\Models;

class DietPlanDetail extends BaseModel {

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'diet_plan_details';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['food_id', 'diet_plan_id', 'diet_schedule_type_id', 'servings_recommended', 'status', 'created_by'];

    /**
     * get name of the diet plan from DietPlan model when used in join
     * 
     * @return type
     */
    public function dietPlan() {
        return $this->hasOne('Modules\Admin\Models\DietPlan', 'id', 'diet_plan_id');
    }

    /**
     * get name of the diet schedule type from DietScheduleType model when used in join
     * 
     * @return type
     */
    public function dietScheduleType() {
        return $this->hasOne('Modules\Admin\Models\DietScheduleType', 'id', 'diet_schedule_type_id');
    }

    /**
     * get name of the food from Food model when used in join
     * 
     * @return type
     */
    public function food() {
        return $this->hasOne('Modules\Admin\Models\Food', 'id', 'food_id');
    }

}
