<?php

/**
 * The class to present SessionBookings model.
 *
 *
 * @author Priyanka Deshpande <priyankad@iprogrammer.com>
 * @package Admin
 * @since 1.0
 */

namespace Modules\Admin\Models;

class BeautyServices extends BaseModel {

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'beauty_services';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['id', 'service_name', 'status', 'created_by', 'updated_by', 'created_at', 'updated_at'];

}
