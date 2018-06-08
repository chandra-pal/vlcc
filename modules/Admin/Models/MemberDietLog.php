<?php

/**
 * The class to present MemberDietLog model.
 * 
 * 
 * @author Gauri Deshmukh <gaurid@iprogrammer.com>
 * @package Admin
 * @since 1.0
 */

namespace Modules\Admin\Models;

class MemberDietLog extends BaseModel {

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'member_diet_logs';
    protected $memberId;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['id', 'member_diet_logs', 'food_name', 'servings_consumed', 'diet_schedule_type_id', 'measure', 'calories', 'total_calories', 'serving_size', 'serving_unit', 'diet_date'];

    /**
     * get name of the diet schedule type from DietScheduleType model when used in join
     * 
     * @return type
     */
    public function dietScheduleType() {
        return $this->belongsTo('Modules\Admin\Models\DietScheduleType', 'diet_schedule_type_id', 'id');
    }

    /**
     * get name of the diet schedule type from DietScheduleType model when used in join
     * 
     * @return type
     */
    public function deviation() {
        return $this->belongsTo('Modules\Admin\Models\Deviation', 'member_id', 'id');
    }

}
