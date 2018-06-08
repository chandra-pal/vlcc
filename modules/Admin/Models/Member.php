<?php
/**
 * The class to present Member model.
 *
 *
 * @author Tushar Dahiwale <tushard@iprogrammer.com>
 * @package Admin
 * @since 1.0
 */

namespace Modules\Admin\Models;

use Modules\Admin\Models\BaseModel;

class Member extends BaseModel
{

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'members';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['crm_customer_id', 'crm_center_id', 'first_name', 'last_name', 'email', 'mobile_number', 'app_version', 'registered_from', 'diet_plan_id', 'status', 'date_of_birth'];

    /**
     * get model when used in join
     * 
     * @return type
     */
    public function activity()
    {
        return $this->hasMany('Modules\Admin\Models\SessionBookings');
    }

    public function memberPackage()
    {
        return $this->hasMany('Modules\Admin\Models\MemberPackage');
    }

    public function memberPackageOne()
    {
        return $this->hasOne('Modules\Admin\Models\MemberPackage');
    }

    public function centers()
    {
        return $this->belongsTo('Modules\Admin\Models\Center', 'crm_center_id', 'crm_center_id');
    }
}
