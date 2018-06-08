<?php

/**
 * The class to present ActivityType model.
 *
 *
 * @author Gauri Deshmukh <gaurid@iprogrammer.com>
 * @package Admin
 * @since 1.0
 */

namespace Modules\Admin\Models;

class ActivityType extends BaseModel {

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'activity_types';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['id','activity_type', 'calories', 'status'];

    /**
     * get model when used in join
     *
     * @return type
     */
    public function activity() {
        return $this->hasMany('Modules\Admin\Models\memberActivityLog', 'activity_type_id', 'id');
    }

    /**
     * get name of the diet plan from ActivityType model when used in join
     *
     * @return type
     */
    public function activityType() {
        return $this->hasMany('Modules\Admin\Models\MemberActivityRecommendation', 'id', 'activity_type_id');
    }

}
