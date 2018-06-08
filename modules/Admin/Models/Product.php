<?php

/**
 * The class to present Food model.
 * 
 * 
 * @author Sopan Zinjurde <sopanz@iprogrammer.com>
 * @package Admin
 * @since 1.0
 */

namespace Modules\Admin\Models;

class Product extends BaseModel {

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'products';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['product_title', 'product_description', 'product_image', 'product_detail_page_url', 'status', 'created_by', 'updated_by'];

}
