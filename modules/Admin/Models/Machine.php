<?php

/**
 * The class to present Machine model.
 *
 *
 * @author Bhawna Thadhani  <bhawnat@iprogrammer.com>
 * @package Admin
 * @since 1.0
 */

namespace Modules\Admin\Models;

use Modules\Admin\Models\BaseModel;

class Machine extends BaseModel {

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'machines';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['machine_type_id', 'name', 'description', 'status'];

    /**
     * get model when used in join
     *
     * @return type
     */
    public function machinetype() {
        return $this->belongsTo('Modules\Admin\Models\MachineType', 'machine_type_id', 'id');
    }

    /**
     * get model when used in join
     *
     * @return type
     */
    public function center() {
        return $this->belongsToMany('Modules\Admin\Models\Center', 'machine_centers', 'machine_id', 'center_id');
    }

    /**
     * get model when used in join
     *
     * @return type
     */
    public function centers() {
        return $this->hasMany('Modules\Admin\Models\MachineCenter', 'machine_id', 'id');
    }

    /**
     * used to join with Modules\Admin\Models\Center
     *
     * @return type
     */
    public function machineCenter() {
        return $this->belongsToMany('Modules\Admin\Models\Center', 'machine_centers');
    }

    /* Attach one record of Center in the machine_centers table
     *
     * @param $center
     */

    public function attachCenter($center) {
        if (is_object($center)) {
            $center = $center->getKey();
        }

        if (is_array($center)) {
            $center = $center['id'];
        }
        $this->center()->attach($center);
    }

    /**
     * Attach whole array Centers in the machine_centers table
     *
     * @param $centers
     */
    public function attachCenters($centers) {
        if (count($centers)) {
            foreach ($centers as $center) {
                $this->attachCenter($center);
            }
        }
    }

    /**
     * Detach one record of Center in the machine_centers table
     *
     * @param $center
     */
    public function detachCenter($center) {
        if (is_object($center)) {
            $center = $center->getKey();
        }

        if (is_array($center)) {
            $center = $center['id'];
        }

        $this->center()->detach($center);
    }

    /**
     * Detach whole array Centers in the machine_centers table
     *
     * @param $centers
     */
    public function detachCenters($centers) {
        foreach ($centers as $center) {
            $this->detachCenter($center);
        }
    }

}
