<?php

/**
 * The class to present FoodType model.
 *
 *
 * @author Priyanka Deshpande <priyankadd@iprogrammer.com>
 * @package Admin
 * @since 1.0
 */

namespace Modules\Admin\Models;

class FoodType extends BaseModel {

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'food_types';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['id', 'food_type_name', 'status'];

    public function foodtypes() {
        return $this->hasMany('Modules\Admin\Models\Food', 'food_type_id', 'id');
    }

}
