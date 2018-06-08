<?php

/**
 * The class to present MemberActivityLog model.
 * 
 * 
 * @author Gauri Deshmukh <gaurid@iprogrammer.com>
 * @package Admin
 * @since 1.0
 */

namespace Modules\Admin\Models;

class MemberActivityLog extends BaseModel {

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'member_activity_logs';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['member_id', 'activity_type_id', 'activity', 'duration', 'start_time', 'activity_date'];

    /**
     * get name of the activity type from ActivityType model when used in join
     * 
     * @return type
     */
    public function activityType() {
        return $this->belongsTo('Modules\Admin\Models\ActivityType', 'activity_type_id', 'id');
    }

}
