<?php
/**
 * To present Ip Login Fail Model with associated authentication
 *
 * @author Manish S <manishs@iprogrammer.com>
 * @package Admin
 * @since 1.0
 */
namespace Modules\Admin\Models;

use Modules\Admin\Models\BaseModel;

class IpLoginFail extends BaseModel
{

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'ip_login_fails';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['ip_address', 'username', 'access_time'];

    /**
     * The attributes that disable created_at and updated_at in database query
     *
     * @var boolean
     */
    public $timestamps = false;

    /**
     * get model when used in join
     * 
     * @return type
     */
    public function ipAddress()
    {
        return $this->belongsTo('Modules\Admin\Models\IpAddress');
    }
}
