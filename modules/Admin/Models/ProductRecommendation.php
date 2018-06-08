<?php

/**
 * The class to present ProductRecommendation model.
 *
 *
 * @author Gauri Deshmukh <gaurid@iprogrammer.com>
 * @package Admin
 * @since 1.0
 */

namespace Modules\Admin\Models;

class ProductRecommendation extends BaseModel {

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'member_product_recommendations';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['id', 'member_id', 'product_id', 'status', 'created_by', 'updated_by'];

    /**
     * get name of the diet plan from DietPlan model when used in join
     *
     * @return type
     */
    public function product() {
        return $this->hasOne('Modules\Admin\Models\Product', 'id', 'product_id');
    }

}
