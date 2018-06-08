<?php

/**
 * The class to present SessionBookings model.
 *
 *
 * @author Gauri Deshmukh <gaurid@iprogrammer.com>
 * @package Admin
 * @since 1.0
 */

namespace Modules\Admin\Models;

class MemberPackageServices extends BaseModel {

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'member_package_services';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['id', 'package_id', 'member_id', 'crm_service_id', 'service_name', 'service_validity', 'services_booked', 'services_consumed', 'start_date', 'end_date', 'created_by', 'updated_by', 'created_at', 'updated_at'];

}
