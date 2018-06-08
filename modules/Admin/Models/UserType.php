<?php
/**
 * The class to present UserType model.
 * 
 * 
 * @author Tushar Dahiwale <tushard@iprogrammer.com>
 * @package Admin
 * @since 1.0
 */

namespace Modules\Admin\Models;

class UserType extends BaseModel
{

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'user_types';

    /**
     * get type_id from UserTypeLinks model when used in join
     * 
     * @return type
     */
    public function links()
    {
        return $this->belongsToMany('Modules\Admin\Models\Links', 'user_type_links', 'user_type_id', 'link_id');
    }

    /**
     * used to join with Modules\Admin\Models\User
     * 
     * @return type
     */
    public function linkCategory()
    {
        return $this->links()->with('linkCategory');
    }

    /**
     * used to join with Modules\Admin\Models\User
     * 
     * @return type
     */
    public function user()
    {
        return $this->hasMany('Modules\Admin\Models\User', 'user_type_id', 'id');
    }

    /** Attach one record of link in the UserTypeLinks table
     *
     * @param $link
     */
    public function attachLink($link)
    {
        if (is_object($link))
            $link = $link->getKey();

        if (is_array($link))
            $link = $link['id'];

        $this->links()->attach($link);
    }

    /**
     * Attach whole array links in the UserTypeLinks table
     *
     * @param $links
     */
    public function attachLinks($links)
    {
        if (count($links)) {
            foreach ($links as $link) {
                $this->attachLink($link);
            }
        }
    }

    /**
     * Detach one record of link in the UserTypeLinks table
     *
     * @param $link
     */
    public function detachLink($link)
    {
        if (is_object($link))
            $link = $link->getKey();

        if (is_array($link))
            $link = $link['id'];

        $this->links()->detach($link);
    }

    /**
     * Detach whole array link in the UserTypeLinks table
     *
     * @param $links
     */
    public function detachLinks($links)
    {
        foreach ($links as $link) {
            $this->detachLink($link);
        }
    }
}
