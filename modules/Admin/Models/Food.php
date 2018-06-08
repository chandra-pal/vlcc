<?php

/**
 * The class to present Food model.
 *
 *
 * @author Gauri Deshmukh <gaurid@iprogrammer.com>
 * @package Admin
 * @since 1.0
 */

namespace Modules\Admin\Models;

use DB;

class Food extends BaseModel {

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'foods';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['food_type_id', 'food_name', 'measure', 'calories', 'serving_size', 'serving_unit', 'status', 'created_by_user_type', 'created_by', 'updated_by'];

    public function FoodType() {
        return $this->hasOne('Modules\Admin\Models\FoodType', 'id', 'food_type_id');
    }

    public function FoodTypeSelect() {
        return $this->hasOne('Modules\Admin\Models\FoodType', 'id', 'food_type_id')->select('id', 'food_type_name');
    }

}
