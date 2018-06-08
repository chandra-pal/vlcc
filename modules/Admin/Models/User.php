<?php

/**
 * To present User Model with associated authentication
 *
 *
 * @author Gaurav Patel <gauravp@iprogrammer.com>
 * @package Admin
 * @since 1.0
 */

namespace Modules\Admin\Models;

use Illuminate\Auth\Authenticatable;
use Modules\Admin\Models\BaseModel;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Admin\Services\Access\Traits\UserHasLink;

class User extends BaseModel implements AuthenticatableContract, CanResetPasswordContract {

    use Authenticatable,
        CanResetPassword,
        SoftDeletes,
        UserHasLink;

    protected $guard = "admin";

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'admins';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['username', 'email', 'first_name', 'password', 'last_name', 'gender', 'contact', 'user_type_id', 'status', 'skip_ip_check', 'created_by'];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = ['password', 'remember_token'];

    /**
     * Serves as a "black-list" instead of a "white-list":
     *
     *  @var array
     */
    protected $guarded = ['id'];

    /**
     * Enables soft delete to
     *
     *  @var array
     */
    //protected $softDelete = true;
    protected $dates = ['deleted_at'];

    /**
     * used to join with Modules\Admin\Models\UserType
     *
     * @return type
     */
    public function userType() {
        return $this->belongsTo('Modules\Admin\Models\UserType');
    }

    /**
     * used to join with Modules\Admin\Models\Center
     *
     * @return type
     */
    public function adminCenter() {
        return $this->belongsToMany('Modules\Admin\Models\Center','admin_centers');
    }
    
    /**
     * get model when used in join
     *
     * @return type
     */
    public function center() {
        return $this->belongsToMany('Modules\Admin\Models\Center', 'admin_centers', 'user_id', 'center_id');
    }

    /**
     * get model when used in join
     *
     * @return type
     */
    public function centers() {
        return $this->hasMany('Modules\Admin\Models\AdminCenter', 'user_id', 'id');
    }

    /* Attach one record of Center in the admin_centers table
     *
     * @param $center
     */

    public function attachUserCenter($center) {
        if (is_object($center)) {
            $center = $center->getKey();
        }

        if (is_array($center)) {
            $center = $center['id'];
        }
        $this->center()->attach($center);
    }

    /**
     * Attach whole array Centers in the admin_centers table
     *
     * @param $centers
     */
    public function attachUserCenters($centers) {
        if (count($centers)) {
            foreach ($centers as $center) {
                $this->attachUserCenter($center);
            }
        }
    }

    /**
     * Detach one record of Center in the admin_centers table
     *
     * @param $center
     */
    public function detachUserCenter($center) {
        if (is_object($center)) {
            $center = $center->getKey();
        }

        if (is_array($center)) {
            $center = $center['id'];
        }

        $this->center()->detach($center);
    }

    /**
     * Detach whole array Centers in the admin_centers table
     *
     * @param $centers
     */
    public function detachUserCenters($centers) {
        foreach ($centers as $center) {
            $this->detachUserCenter($center);
        }
    }
}
