<?php

/**
 * The class to present Diet Schedule Type model.
 * 
 * 
 * @author Gauri Deshmukh <gaurid@iprogrammer.com>
 * @package Admin
 * @since 1.0
 */

namespace Modules\Admin\Models;

use Modules\Admin\Models\BaseModel;

class DietScheduleType extends BaseModel {

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'diet_schedule_types';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['id', 'schedule_name', 'status'];

    /**
     * get model when used in join
     * 
     * @return type
     */
    public function memberDietLog() {
        return $this->hasMany('Modules\Admin\Models\MemberDietLog', 'diet_schedule_type_id', 'id');
    }

    /**
     * get model when used in join
     * 
     * @return type
     */
    public function dietSchedule() {
        return $this->hasMany('Modules\Admin\Models\Deviation', 'diet_schedule_type_id', 'id');
    }

}
