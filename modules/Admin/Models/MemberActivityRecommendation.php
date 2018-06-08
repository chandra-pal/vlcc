<?php

/**
 * The class to present MemberActivityRecommendation model.
 *
 *
 * @author Gauri Deshmukh <gaurid@iprogrammer.com>
 * @package Admin
 * @since 1.0
 */

namespace Modules\Admin\Models;

class MemberActivityRecommendation extends BaseModel {

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'member_activity_recommendation';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['id', 'activity_type_id', 'member_id', 'recommendation_date', 'recommendation_text','duration','calories_recommended'];

    /**
     * get name of the diet plan from ActivityType model when used in join
     *
     * @return type
     */
    public function activityType() {
        return $this->belongsTo('Modules\Admin\Models\ActivityType', 'activity_type_id', 'id');
    }

}
