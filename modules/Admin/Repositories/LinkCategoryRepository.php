<?php
/**
 * To present LinkCategoryRepository with associated model
 *
 *
 * @author Anagha Athale <anaghaa@iprogrammer.com>
 * @package Admin
 * @since 1.0
 */
namespace Modules\Admin\Repositories;

use Modules\Admin\Models\LinkCategory,
    Modules\Admin\Models\Links,
    Modules\Admin\Models\MenuGroup,
    Cache,
    Log,
    DB,
    PDO,
    Auth;

class LinkCategoryRepository extends BaseRepository
{

    protected $ttlCache = 60; // minutes to leave Cache

    /**
     * Create a new LinkCategoryRepository instance.
     *
     * @param  Modules\Admin\Models\LinkCategory $linkCategory
     * @return void
     */

    public function __construct(LinkCategory $linkCategory)
    {
        $this->model = $linkCategory;
    }

    public function data($params = [])
    {
        $cacheKey = str_replace(['\\'], [''], __METHOD__) . ':' . md5(json_encode($params));
        $menuGroups = MenuGroup::table();
        //Cache::tags not suppport with files and Database
        $response = Cache::tags(LinkCategory::table(), MenuGroup::table())->remember($cacheKey, $this->ttlCache, function() use($menuGroups) {
            return LinkCategory::leftJoin($menuGroups, 'link_categories.menu_group_id', '=', $menuGroups . '.id')
                    ->select(['link_categories.*', $menuGroups . '.name as group_name', $menuGroups . '.position as mgposition'])
                    ->orderBy($menuGroups . '.position')
                    ->orderBy('position')
                    ->get();
        });
        return $response;
    }

    /**
     * Store a Category.
     *
     * @param  array $inputs
     * @return void
     */
    public function create($inputs)
    {
        $response = [];
        try {
            $linkCategory = new $this->model;
            $allColumns = $linkCategory->getTableColumns($linkCategory->getTable());
            foreach ($inputs as $key => $value) {
                if (in_array($key, $allColumns)) {
                    $linkCategory->$key = $value;
                }
            }
            $save = $linkCategory->save();
            if ($save) {
                $response['status'] = 'success';
                $response['message'] = trans('admin::messages.added', ['name' => 'Link Category']);
            } else {
                $response['status'] = 'error';
                $response['message'] = trans('admin::messages.not-added', ['name' => 'Link Category']);
            }
        } catch (Exception $e) {
            $response['status'] = 'error';
            $response['message'] = trans('admin::messages.not-added', ['name' => 'Link Category']) . "<br /><b> Error Details</b> - " . $e->getMessage();
            Log::info(": " . $e->getMessage());
        }

        return $response;
    }

    /**
     * Update a category.
     *
     * @param  array  $inputs
     * @param  Modules\Admin\Models\LinkCategory $linkCategory
     * @return void
     */
    public function update($inputs, $linkCategory)
    {
        $response = [];
        try {
            foreach ($inputs as $key => $value) {
                if (isset($linkCategory->$key)) {
                    $linkCategory->$key = $value;
                }
            }
            $save = $linkCategory->save();

            if ($save) {
                $response['status'] = 'success';
                $response['message'] = trans('admin::messages.updated', ['name' => 'Link Category']);
            } else {
                $response['status'] = 'error';
                $response['message'] = trans('admin::messages.not-updated', ['name' => 'Link Category']);
            }
        } catch (Exception $e) {
            $response['status'] = 'error';
            $response['message'] = trans('admin::messages.not-updated', ['name' => 'Link Category']) . "<br /><b> Error Details</b> - " . $e->getMessage();
            Log::info(": " . $e->getMessage());
        }
        return $response;
    }

    /**
     * Get Category Status collection.
     * @return Array
     */
    public function getCategoryStatusSelect()
    {
        $select = ['0' => trans('admin::messages.inactive'), '1' => trans('admin::messages.active'), '2' => trans('admin::messages.suspend')];
        return $select;
    }

    /**
     * Group actions on Users
     *
     * @param  int  $status
     * @return int
     */
    public function groupAction($inputs)
    {
        if (empty($inputs['action'])) {
            return false;
        }
        $resultStatus = false;
        $action = $inputs['action'];
        switch ($action) {
            case "update":
                $userIds = explode(',', $inputs['ids']);
                foreach ($userIds as $key => $userId) {
                    $id = (int) $userId;
                    $user = $this->getLinkById($id);
                    if (!empty($user)) {
                        if ($inputs['field'] === 'status') {
                            $inputPass['status'] = (bool) $inputs['value'];
                            $this->updateStatus($inputPass, $user);
                            $resultStatus = true;
                        }
                    }
                }

                break;
            case "delete":
                $userIds = explode(',', $inputs['ids']);
                foreach ($userIds as $key => $userId) {
                    $id = (int) $userId;
                    $user = $this->getLinkById($id);
                    if (!empty($user)) {
                        $user->delete();
                        $resultStatus = true;
                    }
                }
                break;
            default:
                break;
        }
        return $resultStatus;
    }

    /**
     * Update category status.
     *
     * @param  array  $inputs
     * @param  Modules\Admin\Models\LinkCategory $LinkCategory
     * @return void
     */
    public function updateStatus($inputs, $LinkCategory)
    {
        if (isset($inputs['status'])) {
            $LinkCategory->status = $inputs['status'] == 'true';
        }

        $this->update($inputs, $LinkCategory);
    }

    /**
     * Get category name
     *
     * @param  array  $inputs
     * @param  Modules\Admin\Models\LinkCategory $LinkCategory
     * @return void
     */
    public function listCategoryData()
    {
        $cacheKey = str_replace(['\\'], [''], __METHOD__);
        //Cache::tags not suppport with files and Database
        $response = Cache::tags(LinkCategory::table())->remember($cacheKey, $this->ttlCache, function() {
            return LinkCategory::orderBY('position')->lists('category', 'id');
        });

        return $response;
    }

    public function getLinkById($categoryId)
    {
        $cacheKey = str_replace(['\\'], [''], __METHOD__) . ':' . md5($categoryId);
        $response = Cache::tags(LinkCategory::table())->remember($cacheKey, $this->ttlCache, function() use ($categoryId) {
            return LinkCategory::find($categoryId);
        });

        return $response;
    }

    /**
     * Get Categorywise Links for sidebar menu
     *
     * @return response
     */
    public function getLinks($user_type_id)
    {
        DB::setFetchMode(PDO::FETCH_ASSOC);

        $rescords = DB::table('link_categories')
            ->leftJoin('links', 'link_categories.id', '=', 'links.link_category_id')
            ->leftJoin('user_type_links', 'links.id', '=', 'user_type_links.link_id')
            ->select('link_categories.id as linkCatId', 'category', 'category_icon', 'link_categories.position as linkCatPos', 'links.id as linkId', 'link_icon', 'link_name', 'link_url', 'links.position as linkPos')
            ->where('user_type_links.user_type_id', '=', $user_type_id)
            ->orderBy('link_categories.position')
            ->orderBy('links.position')
            ->get();

        DB::setFetchMode(PDO::FETCH_CLASS);

        return $response = collect($rescords)->groupBy('linkCatId')->toArray();
    }

    /**
     * Get Categorywise Links for sidebar menu
     *
     * @return response
     */
    /*public function getSidebarLinks($user_type_id)
    {
        DB::setFetchMode(PDO::FETCH_ASSOC);

        $rescords = DB::table('link_categories')
            ->leftJoin('links', 'link_categories.id', '=', 'links.link_category_id')
            ->leftJoin('user_type_links', 'links.id', '=', 'user_type_links.link_id')
            ->leftJoin('user_links', 'links.id', '=', 'user_links.link_id')
            ->select('link_categories.id as linkCatId', 'category', 'category_icon', 'link_categories.position as linkCatPos', 'links.id as linkId', 'link_icon', 'link_name', 'link_url', 'links.position as linkPos')
            ->where('user_type_links.user_type_id', '=', $user_type_id)
            ->where('user_links.user_id', '=', Auth::guard('admin')->user()->id)
            ->where('link_categories.status', '=', 1)
            ->where('links.status', '=', 1)
            ->orderBy('link_categories.position')
            ->orderBy('links.position')
            ->get();

        DB::setFetchMode(PDO::FETCH_CLASS);

        return $response = collect($rescords)->groupBy('linkCatId')->toArray();
    }*/
    
    
public function getSidebarLinks($user_type_id)
    {
        DB::setFetchMode(PDO::FETCH_ASSOC);

        $menu_groups = DB::table('menu_groups')
            ->join('link_categories', 'menu_groups.id', '=', 'link_categories.menu_group_id')
            ->select('menu_groups.id', 'menu_groups.name as menu_group_name', 'menu_groups.position')
            ->where('link_categories.status', '=', 1)
            ->groupBy('menu_groups.id')
            ->orderBy('menu_groups.position')
            ->get();
        $menu_groups_records = collect($menu_groups)->toArray();
        $i = 0;
        foreach ($menu_groups_records as $menu_group) {
            $menu_group_id = $menu_group['id'];
            $rescords = DB::table('link_categories')
                ->leftJoin('links', 'link_categories.id', '=', 'links.link_category_id')
                ->leftJoin('user_type_links', 'links.id', '=', 'user_type_links.link_id')
                ->leftJoin('user_links', 'links.id', '=', 'user_links.link_id')
                ->select('link_categories.id as linkCatId', 'category', 'category_icon', 'link_categories.position as linkCatPos', 'links.id as linkId', 'link_icon', 'link_name', 'link_url', 'links.position as linkPos')
                ->where('user_type_links.user_type_id', '=', $user_type_id)
                ->where('user_links.user_id', '=', Auth::guard('admin')->user()->id)
                ->where('link_categories.status', '=', 1)
                ->where('links.status', '=', 1)
                ->where('link_categories.menu_group_id', '=', $menu_group_id)
                ->orderBy('link_categories.position')
                ->orderBy('links.position')
                ->get();
            $menu_groups_records[$i]['link_categories'] = collect($rescords)->groupBy('linkCatId')->toArray();
            $i++;
        }
        return $menu_groups_records;
    }

    
    /**
     * Get All LinkCategory and Links
     *
     * @param  array  $inputs
     * @param  Modules\Admin\Models\LinkCategory $LinkCategory
     * @return void
     */
    public function getAllLinksByCategory()
    {
        $cacheKey = str_replace(['\\'], [''], __METHOD__);
        $response = Cache::tags(Links::table(), LinkCategory::table())->remember($cacheKey, $this->ttlCache, function() {
            return LinkCategory::with(['Links' => function($query) {
                        $query->orderBy('position', 'asc');
                    }])->orderBy('link_categories.position', 'asc')->get();
        });
        return $response;
    }
}
