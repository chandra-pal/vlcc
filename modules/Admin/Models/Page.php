<?php

namespace Modules\Admin\Models;

use Modules\Admin\Models\BaseModel;

class Page extends BaseModel
{

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'site_pages';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['page_name', 'page_url', 'slug', 'page_desc', 'browser_title', 'meta_keywords', 'meta_description', 'page_content', 'status','created_by'];

}
