<?php
/**
 * To present Links Model with associated authentication
 *
 *
 * @author Anagha Athale <anaghaa@iprogrammer.com>
 * @package Admin
 * @since 1.0
 */

namespace Modules\Admin\Models;

use Modules\Admin\Models\BaseModel;

class Links extends BaseModel
{

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'links';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['link_name', 'link_url', 'link_category_id', 'position', 'page_header', 'page_text', 'tooltip', 'target', 'pagination', 'status'];

    /**
     * get link_id from UserTypeLinks model when used in join
     * 
     * @return type
     */
    public function userType()
    {
        return $this->belongsToMany('Modules\Admin\Models\UserType', 'user_type_links', 'link_id', 'user_type_id');
    }

    /**
     * get name of the category from LinkCategory model when used in join
     * 
     * @return type
     */
    public function linkCategory()
    {
        return $this->belongsTo('Modules\Admin\Models\LinkCategory');
    }
    /* Attach one record of User Types in the UserTypeLinks table
     *
     * @param $userType
     */

    public function attachUserType($userType)
    {
        if (is_object($userType))
            $userType = $userType->getKey();

        if (is_array($userType))
            $userType = $userType['id'];

        $this->userType()->attach($userType);
    }

    /**
     * Attach whole array User Types in the UserTypeLinks table
     *
     * @param $userTypes
     */
    public function attachUserTypes($userTypes)
    {
        if (count($userTypes)) {
            foreach ($userTypes as $userType) {
                $this->attachUserType($userType);
            }
        }
    }

    /**
     * Detach one record of User Types in the UserTypeLinks table
     *
     * @param $userType
     */
    public function detachUserType($userType)
    {
        if (is_object($userType))
            $userType = $userType->getKey();

        if (is_array($userType))
            $userType = $userType['id'];

        $this->userType()->detach($userType);
    }

    /**
     * Detach whole array User Types in the UserTypeLinks table
     *
     * @param $userTypes
     */
    public function detachUserTypes($userTypes)
    {
        foreach ($userTypes as $userType) {
            $this->detachUserType($userType);
        }
    }
}
