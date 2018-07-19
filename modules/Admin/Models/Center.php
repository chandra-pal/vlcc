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

class Center extends BaseModel {

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'vlcc_centers';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['address', 'area', 'crm_center_id', 'city_id', 'state_id', 'country_id', 'pincode', 'latitude', 'longitude', 'phone_number', 'status'];

    /**
     * get name of the state from State model when used in join
     *
     * @return type
     */
    public function city() {
        return $this->belongsTo('Modules\Admin\Models\City', 'city_id', 'id');
    }

    /**
     * get name of the state from State model when used in join
     *
     * @return type
     */
    public function states() {
        return $this->belongsTo('Modules\Admin\Models\State', 'state_id', 'id');
    }

    /**
     * get name of the state from State model when used in join
     *
     * @return type
     */
    public function country() {
        return $this->belongsTo('Modules\Admin\Models\Country', 'country_id', 'id');
    }

    /**
     * get name of the state from State model when used in join
     *
     * @return type
     */
    public function center() {
        return $this->belongsTo('Modules\Admin\Models\AdminCenter', 'center_id', 'id');
    }

    /**
     * used to join with Modules\Admin\Models\User
     *
     * @return type
     */
    public function userCenter() {
        return $this->belongsToMany('Modules\Admin\Models\User', 'admin_centers');
    }

}
