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

class Deviation extends BaseModel {

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'member_diet_deviations';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['member_id', 'diet_schedule_type_id', 'calories_recommended', 'calories_consumed', 'deviation_date'];

    /**
     * get model when used in join
     * 
     * @return type
     */
    public function dietSchedule() {
        return $this->hasMany('Modules\Admin\Models\DietScheduleType', 'diet_schedule_type_id', 'id');
    }

    /**
     * get model when used in join
     * 
     * @return type
     */
    public function member() {
        return $this->hasMany('Modules\Admin\Models\Member', 'member_id', 'id');
    }

}
