<?php

/**
 * The class to present Center model.
 *
 *
 * @author Gauri Deshmukh <gaurid@iprogrammer.com>
 * @package Admin
 * @since 1.0
 */

namespace Modules\Admin\Models;

class AdminCenter extends BaseModel {

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'admin_centers';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['user_id', 'center_id'];

}
