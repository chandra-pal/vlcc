<?php

/**
 * The class to present ReminderType model.
 * 
 * 
 * @author Gauri Deshmukh <gaurid@iprogrammer.com>
 * @package Admin
 * @since 1.0
 */

namespace Modules\Admin\Models;

class ReminderType extends BaseModel {

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'reminder_types';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['type_name', 'status'];

}
