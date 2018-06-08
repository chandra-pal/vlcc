<?php
/**
 * To present LinksRepository with associated model
 *
 *
 * @author Anagha Athale <anaghaa@iprogrammer.com>
 * @package Admin
 * @since 1.0
 */
namespace Modules\Admin\Repositories;

use Modules\Admin\Models\Links,
    Modules\Admin\Models\LinkCategory,
    Modules\Admin\Models\UserType,
    Cache,
    Log;

class LinksRepository extends BaseRepository
{

    protected $ttlCache = 60; // minutes to leave Cache

    /**
     * Create a new LinksRepository instance.
     *
     * @param  Modules\Admin\Models\Links $links
     * @return void
     */

    public function __construct(Links $links)
    {
        $this->model = $links;
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function data($params = [])
    {
        $cacheKey = str_replace(['\\'], [''], __METHOD__) . ':' . md5(json_encode($params));
        //Cache::tags not suppport with files and Database
        $linkCategories = LinkCategory::table();

        $response = Cache::tags(Links::table(), LinkCategory::table())->remember($cacheKey, $this->ttlCache, function() use($linkCategories) {
            return Links::join($linkCategories, 'links.link_category_id', '=', $linkCategories . '.id')
                    ->select(['links.*', $linkCategories . '.category as cat', $linkCategories . '.position as cposition'])
                    ->orderBy($linkCategories . '.position')
                    ->orderBy('position')->get();
        });

        return $response;
    }

    /**
     * Store a link.
     *
     * @param  array $inputs
     * @return void
     */
    public function create($inputs)
    {
        $response = [];
        try {
            $link = new $this->model;
            $allColumns = $link->getTableColumns($link->getTable());
            foreach ($inputs as $key => $value) {
                if (in_array($key, $allColumns)) {
                    $link->$key = $value;
                }
            }
            $linkCategoryRepository = new LinkCategoryRepository(new LinkCategory);
            $linkCategory = $linkCategoryRepository->getLinkById($inputs['link_category_id']);
            $link->linkCategory()->associate($linkCategory);
            $save = $link->save();

            //inserting records in UserTypeLinks table
            $userTypeIds = $inputs['links_assign'];
            $link->attachUserTypes($userTypeIds);
            if ($save) {
                $response['status'] = 'success';
                $response['message'] = trans('admin::messages.added', ['name' => 'Link']);
            } else {
                $response['status'] = 'error';
                $response['message'] = trans('admin::messages.not-added', ['name' => 'Link']);
            }
        } catch (Exception $e) {
            $response['status'] = 'error';
            $response['message'] = trans('admin::messages.not-added', ['name' => 'Link']) . "<br /><b> Error Details</b> - " . $e->getMessage();
            Log::info(": " . $e->getMessage());
        }

        return $response;
    }

    /**
     * Update a link.
     *
     * @param  array  $inputs
     * @param  Modules\Admin\Models\links $link
     * @return void
     */
    public function update($inputs, $link)
    {
        try {
            foreach ($inputs as $key => $value) {
                if (isset($link->$key)) {
                    $link->$key = $value;
                }
            }
            if (!empty($inputs['link_category_id'])) {
                $linkCategoryRepository = new LinkCategoryRepository(new LinkCategory);
                $linkCategory = $linkCategoryRepository->getLinkById($inputs['link_category_id']);
                $link->linkCategory()->associate($linkCategory);
            }
            $save = $link->save();

            //inserting records in UserTypeLinks table
            if (!empty($inputs['links_assign'])) {
                $userTypeIds = $inputs['links_assign'];
                $existingData = $this->getUserTypeIdsbyLink($link->id);
                $addUserTypeIds = array_diff($userTypeIds, $existingData);
                if (!empty($addUserTypeIds)) {
                    $link->attachUserTypes($addUserTypeIds);
                    Cache::tags(UserType::table())->flush();
                }

                //deleting records from UserTypeLinks table
                $deleteTypeIds = array_diff($existingData, $userTypeIds);
                if (!empty($deleteTypeIds)) {
                    $link->detachUserTypes($deleteTypeIds);
                    Cache::tags(UserType::table())->flush();
                }
            }

            if ($save) {
                $response['status'] = 'success';
                $response['message'] = trans('admin::messages.updated', ['name' => 'Link']);
            } else {
                $response['status'] = 'error';
                $response['message'] = trans('admin::messages.not-updated', ['name' => 'Link']);
            }
        } catch (Exception $e) {
            $response['status'] = 'error';
            $response['message'] = trans('admin::messages.not-updated', ['name' => 'Link']) . "<br /><b> Error Details</b> - " . $e->getMessage();
            Log::info(": " . $e->getMessage());
        }
        return $response;
    }

    /**
     * Group actions on Users
     *
     * @param  array $inputs
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
                $linkIds = explode(',', $inputs['ids']);
                foreach ($linkIds as $linkId) {
                    $id = (int) $linkId;
                    $link = Links::find($id);
                    if (!empty($link)) {
                        if ($inputs['field'] === 'status') {
                            $inputPass['status'] = (bool) $inputs['value'];
                            $this->updateStatus($inputPass, $link);
                            $resultStatus = true;
                        }
                    }
                }
                break;
            default:
                break;
        }
        return $resultStatus;
    }

    /**
     * Update link status.
     *
     * @param  array  $inputs
     * @param  Modules\Admin\Models\Links $links
     * @return void
     */
    public function updateStatus($inputs, $links)
    {
        if (isset($inputs['status'])) {
            $links->status = $inputs['status'] == 'true';
        }

        $this->update($inputs, $links);
    }

    /**
     * UserTypewise links id from UserTypeLinks table
     *
     * @param  $userTypeId
     * @return $response of all typewise links id
     */
    public function listTypewiseLinksData($userTypeId)
    {
        $cacheKey = str_replace(['\\'], [''], __METHOD__) . ':' . md5($userTypeId);
        $response = Cache::tags(UserType::table())->remember($cacheKey, $this->ttlCache, function() use ($userTypeId) {
            return UserType::find($userTypeId)->links()->lists('link_id')->toArray();
        });

        return $response;
    }

    /**
     * Fetch exisitng userTypes from UserTypeLinks
     * @param $link_id
     * @return $response of all exisiting UserTypes with same $link_id
     */
    public function getUserTypeIdsbyLink($link_id)
    {
        $cacheKey = str_replace(['\\'], [''], __METHOD__) . ':' . md5($link_id);
        $response = Cache::tags(UserType::table())->remember($cacheKey, $this->ttlCache, function() use ($link_id) {
            return Links::find($link_id)->userType()->lists('user_type_id')->toArray();
        });

        return $response;
    }

    /**
     * Fetch link category and links data by current route name
     * @param $routeName
     * @return $response of links and link category data
     */
    public function getLinksDataByRoute($routeName)
    {
        $cacheKey = str_replace(['\\'], [''], __METHOD__) . ':' . md5(json_encode($routeName));

        $response = Cache::tags(Links::table())->remember($cacheKey, $this->ttlCache, function() use($routeName) {
            $data = Links::where('link_url', 'LIKE', $routeName)->with('linkCategory')->get()->first();
            if (!empty($data)) {
                return $data->toArray();
            }
        });

        return $response;
    }

    /**
     * pagination values array
     * @return array
     */
    public function getPaginationList()
    {
        return ['' => 'Select Records Per Page', '10' => '10', '20' => '20', '50' => '50', '100' => '100', '150' => '150', 'All' => 'All'];
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function listLinksData($categoryId)
    {
        $categoryId = (int) $categoryId;
        $cacheKey = str_replace(['\\'], [''], __METHOD__) . ':' . md5(json_encode($categoryId));
        //Cache::tags not suppport with files and Database
        $response = Cache::tags(Links::table())->remember($cacheKey, $this->ttlCache, function() use($categoryId) {
            return Links::whereLinkCategoryId($categoryId)->orderBY('id')->lists('link_name', 'id');
        });

        return $response;
    }
}
