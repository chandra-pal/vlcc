<?php
/**
 * trait UserHasLink
 * 
 * @author Nilesh Pangul <nileshp@iprogrammer.com>
 * @package Modules\Admin\Services\Access\Traits
 * @since 1.0
 */

namespace Modules\Admin\Services\Access\Traits;

trait UserHasLink
{

    /**
     * Add access is_add column value for user link from users_links
     * @var $hasAdd is_add
     */
    public $hasAdd;

    /**
     * Edit access is_edit and own_edit column value for user link from users_links
     * @var $hasEdit is_add and own_edit
     */
    public $hasEdit;

    /**
     * Delete access is_delete and own_delete column value for user link from users_links
     * @var $hasDelete is_delete and own_delete
     */
    public $hasDelete;

    /**
     * View access own_view column value for user link from users_links
     * @var $hasView own_view
     */
    public $hasOwnView;
    public $hasOwnEdit;
    public $hasOwnDelete;

    /**
     * set the user permissions to variables
     *
     * @param int $link
     * @param string $action
     * @return bool
     */
    public function setPermissions($permissions)
    {
        $this->hasAdd = $this->assignPermission('is_add', $permissions);
        $this->hasEdit = $this->assignPermission('is_edit', $permissions);
        $this->hasDelete = $this->assignPermission('is_delete', $permissions);
        $this->hasOwnView = $this->assignPermission('own_view', $permissions);
        $this->hasOwnEdit = $this->assignPermission('own_edit', $permissions);
        $this->hasOwnDelete = $this->assignPermission('own_delete', $permissions);
    }

    /**
     * Many-to-Many relations with Link.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function links()
    {
        return $this->belongsToMany('Modules\Admin\Models\Links', 'user_links', 'user_id', 'link_id')
                ->withPivot('is_add', 'is_edit', 'is_delete', 'own_view', 'own_edit', 'own_delete');
    }

    /**
     * Check if user has a link by its name.
     *
     * @param string $link
     * @param string $action
     * @return bool
     */
    public function can($link, $action = '')
    {
        $perm = (isset($action)) ? $this->mapPermission($action) : '';

        if (!empty($link) && in_array($perm, $this->actions())) {

            //$result = $this->links()->where('link_url', $link)->first();
            $result = $this->links()->where('link_url', $link)->first();
            $actionArr = explode(".", $link, 3);
            if (empty($result) && (!empty($actionArr) && count($actionArr) >= 2)) {

                //if exact mathc is not runs then search the part of link ex. admin.user
                $searchRoute = $actionArr[0] . '.' . $actionArr[1];
                $data = $this->links()->where('link_url', 'like', $searchRoute . '.%')->get()->toArray();

                //If more than one links associated present then give priority to parent link which has resource index method. ex. admin.user.index 
                $result = collect($data)->filter(function ($item) {
                        $niddle = substr(strrchr($item['link_url'], "."), 1);
                        $hystack = ['index', 'list', 'apilist', 'getdata', 'update'];
                        if (in_array($niddle, $hystack)) {
                            return $item;
                        }
                    })->first();
            }
            if (!empty($result['pivot'])) {
                $response = collect($result['pivot'])->toArray();
                return $this->assignPermission($perm, $response);
            }
        } else if (!empty($link) && empty($action)) {
            $result = $this->links()->where('link_url', $link)->first();
            if (!empty($result->pivot)) {
                return collect($result->pivot)->toArray();
            }
        }
        return false;
    }

    /**
     * assign the permission logic
     *
     * @param string $perm permission name
     * @param array $response 
     * @return bool
     */
    public function assignPermission($perm, $response = [])
    {
        //dd($response['own_edit']);
        switch ($perm) {
            case 'is_edit':
                return (!empty($response['is_edit'])) ? 1 : 0;
            case 'is_delete':
                return (!empty($response['is_delete'])) ? 1 : 0;
            case 'own_view':
                return (!empty($response['is_edit']) || !empty($response['is_delete'])) ? 0 : $response['own_view'];
            case 'is_add':
                return (!empty($response['is_add'])) ? 1 : '';
            case 'own_edit':
                return (!empty($response['is_edit'])) ? 0 : $response['own_edit'];
            case 'own_delete':
                return (!empty($response['is_delete'])) ? 0 : $response['own_delete'];
            default:
                return '';
        }
    }

    /**
     * get user links and permissions 
     *
     * @return array result
     */
    public function permissions()
    {
        $result = $this->links()->get();
        return collect($result)->pluck('pivot')->toArray();
    }

    /**
     * get all actions array 
     *
     * @return array result
     */
    public function actions()
    {
        return ['is_add', 'is_edit', 'is_delete', 'own_view', 'own_edit', 'own_delete'];
    }

    /**
     * Check if user has a link assinged
     *
     * @param string $routeLinkAs
     * @return bool
     */
    public function linkPermissions($routeLinkAs, $onlyCount = false)
    {

        $result = [];
        if (!empty($routeLinkAs)) {
            //MAtch the exact link
            $result = $this->links()->where('link_url', $routeLinkAs)->first();
            $actionArr = explode(".", $routeLinkAs, 3);
            if (empty($result) && (!empty($actionArr) && count($actionArr) >= 2)) {

                //if exact mathc is not runs then search the part of link ex. admin.user
                $searchRoute = $actionArr[0] . '.' . $actionArr[1];
                $data = $this->links()->where('link_url', 'like', $searchRoute . '.%')->get()->toArray();

                //If more than one links associated present then give priority to parent link which has resource index method. ex. admin.user.index 
                $result = collect($data)->filter(function ($item) {
                        $niddle = substr(strrchr($item['link_url'], "."), 1);
                        $hystack = ['index', 'list', 'apilist', 'getdata', 'update'];
                        if (in_array($niddle, $hystack)) {
                            return $item;
                        }
                    })->first();
            }
        }

        $permissions = (!empty($result['pivot'])) ? $result['pivot'] : [];
        $resultArr = collect($permissions)->toArray();
        if (!empty($onlyCount)) {
            return collect($resultArr)->count();
        }

        return $resultArr;
    }

    /**
     * Attach one link not associated with a role directly to a user
     *
     * @param $link
     */
    public function attachLink($link, $extraCloumns)
    {
        $this->links()->attach($link, $extraCloumns);
    }

    /**
     * Attach other links not associated with a role directly to a user
     *
     * @param $links
     */
    public function attachLinks($links)
    {
        if (count($links)) {
            foreach ($links as $link => $fieldArr) {
                //format input array with default values to non-zero elements
                $columns = $this->assignTableColums($fieldArr);
                $this->attachLink($link, $columns);
            }
        }
    }

    /**
     * Assign values to key pair of array
     *
     * @param $fieldArr
     * @return type Array
     */
    public function assignTableColums($fieldArr)
    {
        return $columns = [
            'is_add' => (!empty($fieldArr['is_add'])) ? 1 : 0,
            'is_edit' => (!empty($fieldArr['is_edit'])) ? 1 : 0,
            'is_delete' => (!empty($fieldArr['is_delete'])) ? 1 : 0,
            'own_view' => (!empty($fieldArr['own_view'])) ? 1 : 0,
            'own_edit' => (!empty($fieldArr['own_edit'])) ? 1 : 0,
            'own_delete' => (!empty($fieldArr['own_delete'])) ? 1 : 0
        ];
    }

    /**
     * Detach one link not associated with a role directly to a user
     *
     * @param $link
     */
    public function detachLink($link)
    {
        $this->links()->detach($link);
    }

    /**
     * Detach other links not associated with a role directly to a user
     *
     * @param $links
     */
    public function detachLinks($links)
    {
        foreach ($links as $link) {
            $this->detachLink($link);
        }
    }

    /**
     * Update one link not associated with a link directly to a user
     *
     * @param $link
     */
    public function updateLink($link, $extraCloumns)
    {
        $this->links()->updateExistingPivot($link, $extraCloumns);
    }

    /**
     * Update other links not associated with a link directly to a user
     *
     * @param $links
     */
    public function updateLinks($links)
    {
        if (count($links)) {
            foreach ($links as $link => $fieldArr) {
                $columns = $this->assignTableColums($fieldArr);
                $this->updateLink($link, $columns);
            }
        }
    }

    /**
     * Check if user has permission to a route
     * 
     * @param  String $routeName
     * @return Boolean true/false
     */
    public function hasRoute($routeName)
    {
        $route = app('router')->getRoutes()->getByName($routeName);

        if ($route) {

            $action = $route->getAction();

            if (isset($action['as'])) {

                $array = explode('.', $action['as']);

                return $this->can($action['as']);
            }
        }

        return false;
    }

    /**
     * map the permissions to action fields of users_links table
     * 
     * @param  String $routeName
     * @return Boolean true/false
     */
    public function mapPermission($perm)
    {
        switch ($perm) {
            case 'add':
            case 'create':
            case 'store':
            case 'is_add':
                return 'is_add';
            case 'edit':
            case 'update':
            case 'put':
            case 'is_edit':
                return 'is_edit';
            case 'show':
            case 'index':
            case 'view':
            case 'list':
            case 'own_view':
                return 'own_view';
            case 'destroy':
            case 'delete':
            case 'trash':
            case 'is_delete':
                return 'is_delete';

            default:
                return $perm;
        }

        return false;
    }
}
