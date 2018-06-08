<?php

/**
 * The class to present Admin Notifications model.
 *
 *
 * @author Tushar Dahiwale <tushard@iprogrammer.com>
 * @package Admin
 * @since 1.0
 */

namespace Modules\Admin\Models;

use Modules\Admin\Models\BaseModel;

class AdminNotification extends BaseModel {

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'admin_notifications';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['admin_id', 'notification_text', 'deep_linking', 'notification_date', 'notification_type', 'read_status'];
}
