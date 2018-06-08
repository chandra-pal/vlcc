<?php

/**
 * The class to present member diet deviation model.
 * 
 * 
 * @author Gauri Deshmukh <gaurid@iprogrammer.com>
 * @package Admin
 * @since 1.0
 */

namespace Modules\Admin\Models;

class MemberDietRecommendation extends BaseModel {

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'member_diet_recommendations';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['member_id', 'diet_plan_id', 'diet_schedule_type_id', 'food_id', 'servings_recommended', 'status', 'recommendation_date', 'created_by'];

    /**
     * get model when used in join
     * 
     * @return type
     */
    public function food() {
        return $this->hasOne('Modules\Admin\Models\Food', 'id', 'food_id');
    }

    /**
     * get model when used in join
     * 
     * @return type
     */
    public function schedule() {
        return $this->hasOne('Modules\Admin\Models\DietScheduleType', 'id', 'diet_schedule_type_id');
    }

    /**
     * get model when used in join
     * 
     * @return type
     */
    public function type() {
        return $this->hasOne('Modules\Admin\Models\FoodType', 'id', 'food_type_id');
    }

}
