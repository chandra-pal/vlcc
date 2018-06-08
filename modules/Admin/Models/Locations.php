<?php
/**
 * The class to present Locations model.
 *
 *
 * @author Rahul Kadu <rahulk@iprogrammer.com>
 * @package Admin
 * @since 1.0
 */
namespace Modules\Admin\Models;

use Modules\Admin\Models\BaseModel;

class Locations extends BaseModel
{

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'locations';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['country_id', 'state_id', 'city_id', 'location', 'address_1', 'address_2', 'landmark', 'zipcode', 'latitude', 'longitude', 'status'];

    /**
     * get name of the state from State model when used in join
     *
     * @return type
     */
    public function city()
    {
        return $this->belongsTo('Modules\Admin\Models\City', 'city_id', 'id');
    }

    /**
     * get name of the state from State model when used in join
     *
     * @return type
     */
    public function states()
    {
        return $this->belongsTo('Modules\Admin\Models\State', 'state_id', 'id');
    }

    /**
     * get name of the state from State model when used in join
     *
     * @return type
     */
    public function country()
    {
        return $this->belongsTo('Modules\Admin\Models\Country', 'country_id', 'id');
    }
}
