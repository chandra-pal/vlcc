<?php

/**
 * The class to present RoomAvailability model.
 *
 *
 * @author Bhawna Thadhani <bhawnat@iprogrammer.com>
 * @package Admin
 * @since 1.0
 */

namespace Modules\Admin\Models;

class RoomAvailability extends BaseModel{
    
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'room_availability';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['center_id','room_id', 'availability_date', 'start_time', 'end_time', 'created_by', 'updated_by', 'status', 'carry_forward_availability', 'carry_forward_availability_days'];
    
    
    /**
     * get model when used in join
     *
     * @return type
     */
    public function roomCenter() {
        return $this->belongsTo('Modules\Admin\Models\Center', 'center_id', 'id');
    }

    
    /**
     * get model when used in join
     *
     * @return type
     */
    public function room() {
        return $this->belongsTo('Modules\Admin\Models\Room', 'room_id', 'id');
    }

}
