<?php

/**
 * The class to present Offer model.
 * 
 * 
 * @author Sopan Zinjurde <sopanz@iprogrammer.com>
 * @package Admin
 * @since 1.0
 */

namespace Modules\Admin\Models;

class Offer extends BaseModel {

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'offers';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['offer_title', 'offer_description', 'offer_image', 'offer_detail_page_url', 'status', 'created_by', 'updated_by'];

}
