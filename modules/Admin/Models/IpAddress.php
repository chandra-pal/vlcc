<?php

namespace Modules\Admin\Models;

use Modules\Admin\Models\BaseModel;

class IpAddress extends BaseModel
{

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'ip_addresses';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['ip_address', 'status', 'created_by'];

    /**
     * get model when used in join
     * 
     * @return type
     */
    public function ipLoginFail()
    {
        return $this->hasMany('Modules\Admin\Models\IpLoginFail', 'ip_address', 'ip_address')->orderBy('id', 'desc')->limit(5);
    }
}
