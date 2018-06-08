<?php
/**
 * The class to present Faq model.
 * 
 * 
 * @author Tushar Dahiwale <tushard@iprogrammer.com>
 * @package Admin
 * @since 1.0
 */

namespace Modules\Admin\Models;

use Modules\Admin\Models\BaseModel;

class Faq extends BaseModel
{

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'faqs';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['question', 'answer', 'position', 'faq_category_id', 'status'];

    /**
     * get name of the category from FaqCategory model when used in join
     * 
     * @return type
     */
    public function faqCategory()
    {
        return $this->belongsTo('Modules\Admin\Models\FaqCategory', 'faq_category_id', 'id');
    }
}
