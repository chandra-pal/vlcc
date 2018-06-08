<?php

/**
 * The class to present OfferRecommendation model.
 *
 *
 * @author Sopan Zinjurde <sopanz@iprogrammer.com>
 * @package Admin
 * @since 1.0
 */

namespace Modules\Admin\Models;

class OfferRecommendation extends BaseModel {

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'member_offers_recommendations';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['id', 'member_id', 'offer_id', 'status', 'created_by', 'updated_by'];

    /**
     * get name of the diet plan from DietPlan model when used in join
     *
     * @return type
     */
    public function offer() {
        return $this->hasOne('Modules\Admin\Models\Offer', 'id', 'offer_id');
    }

}
