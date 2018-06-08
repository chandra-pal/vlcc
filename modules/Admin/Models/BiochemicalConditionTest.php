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
class BiochemicalConditionTest extends BaseModel {

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'biochemical_condition_test';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['id', 'condition_id', 'test_name'];

}
