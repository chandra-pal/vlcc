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

use Modules\Admin\Models\BaseModel;

class BiochemicalCondition extends BaseModel {

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'biochemical_condition';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['id', 'condition_name'];

    /**
     * get name of the state from State model when used in join
     *
     * @return type
     */
    public function conditionTest() {
        return $this->hasMany('Modules\Admin\Models\BiochemicalConditionTest', 'condition_id', 'id');
    }

}
