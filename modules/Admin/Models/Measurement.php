<?php

/**
 * The class to present Measurement model.
 * 
 * 
 * @author Gauri Deshmukh <gaurid@iprogrammer.com>
 * @package Admin
 * @since 1.0
 */

namespace Modules\Admin\Models;

class Measurement extends BaseModel {

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'measurements';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['title', 'meaning', 'status'];

}
