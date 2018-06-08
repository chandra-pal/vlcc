<?php

/**
 * The class to present Room model.
 *
 *
 * @author Bhawna Thadhani  <bhawnat@iprogrammer.com>
 * @package Admin
 * @since 1.0
 */

namespace Modules\Admin\Models;

use Modules\Admin\Models\BaseModel;

class Room extends BaseModel {

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'rooms';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name', 'center_id', 'room_type', 'status'];

    /**
     * get model when used in join
     *
     * @return type
     */
    public function center() {
        return $this->belongsTo('Modules\Admin\Models\Center', 'center_id', 'id');
    }

}
