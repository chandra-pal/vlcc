<?php

/**
 * The class to present Center model.
 *
 *
 * @author Priyanka Deshpande <priyankad@iprogrammer.com>
 * @package Admin
 * @since 1.0
 */

namespace Modules\Admin\Models;

class MachineCenter extends BaseModel {

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'machine_centers';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['center_id', 'machine_id'];

}
