<?php

/**
 * The class to present Offer model.
 *
 *
 * @author Gauri Deshmukh <gaurid@iprogrammer.com>
 * @package Admin
 * @since 1.0
 */

namespace Modules\Admin\Models;

class SessionResources extends BaseModel {

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'member_session_booking_resources';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['session_id', 'member_id', 'resource_id', 'resource_type', 'resource_start_time', 'resource_end_time', 'created_by', 'updated_by'];

}
