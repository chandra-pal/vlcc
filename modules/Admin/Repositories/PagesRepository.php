<?php
/**
 * The repository class for managing user type specific actions.
 * 
 * 
 * @author Anagha Athale <anaghaa@iprogrammer.com>
 * @package Admin
 * @since 1.0
 */

namespace Modules\Admin\Repositories;

use Modules\Admin\Models\Page,
    Illuminate\Support\Facades\Cache;

class PagesRepository extends BaseRepository
{

    /**
     * Create a new PagesRepository instance.
     *
     * @param  Modules\Admin\Models\Pages $page
     * 
     * @return void
     */
    public function __construct(Page $page)
    {
        $this->model = $page;
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
        $response = Cache::tags(Page::table())->remember($cacheKey, $this->ttlCache, function() {
            return Page::select(['id', 'page_name', 'page_url', 'slug', 'page_desc', 'browser_title', 'meta_keywords', 'meta_description', 'page_content', 'status', 'created_by'])->orderBy('id')->get();
        });

        return $response;
    }

    /**
     * Store a page.
     *
     * @param  array $inputs
     * @return void
     */
    public function create($inputs)
    {
        $response = [];
        try {
            $page = new $this->model;
            $allColumns = $page->getTableColumns($page->getTable());
            foreach ($inputs as $key => $value) {
                if (in_array($key, $allColumns)) {
                    $page->$key = $value;
                }
            }
            $save = $page->save();

            if ($save) {
                $response['status'] = 'success';
                $response['message'] = trans('admin::messages.added', ['name' => 'Page Details']);
            } else {
                $response['status'] = 'error';
                $response['message'] = trans('admin::messages.not-added', ['name' => 'Page Details']);
            }
        } catch (Exception $e) {
            $response['status'] = 'error';
            $response['message'] = trans('admin::messages.not-added', ['name' => 'Page Details']) . "<br /><b> Error Details</b> - " . $e->getMessage();
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
                $pageIds = explode(',', $inputs['ids']);
                foreach ($pageIds as $pageId) {
                    $id = (int) $pageId;
                    $page = Page::find($id);
                    if (!empty($page)) {
                        if ($inputs['field'] === 'status') {
                            $inputPass['status'] = (bool) $inputs['value'];
                            $this->updateStatus($inputPass, $page);
                            $resultStatus = true;
                        }
                    }
                }
                break;
            case "delete":
                $pageIds = explode(',', $inputs['ids']);
                foreach ($pageIds as $key => $pageId) {
                    $id = (int) $pageId;
                    $page = Page::find($id);
                    if (!empty($page)) {
                        $page->delete();
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
     * Update Page status.
     *
     * @param  array  $inputs
     * @param  Modules\Admin\Models\Page $page
     * @return void
     */
    public function updateStatus($inputs, $page)
    {
        if (isset($inputs['status'])) {
            $page->status = $inputs['status'] == 'true';
        }

        $this->update($inputs, $page);
    }

    /**
     * Update a page.
     *
     * @param  array  $inputs
     * @param  Modules\Admin\Models\Page $page
     * @return void
     */
    public function update($inputs, $page)
    {
        try {
            foreach ($inputs as $key => $value) {
                if (isset($page->$key)) {
                    $page->$key = $value;
                }
            }
            $save = $page->save();

            if ($save) {
                $response['status'] = 'success';
                $response['message'] = trans('admin::messages.updated', ['name' => 'Page Details']);
            } else {
                $response['status'] = 'error';
                $response['message'] = trans('admin::messages.not-updated', ['name' => 'Page Details']);
            }
        } catch (Exception $e) {
            $response['status'] = 'error';
            $response['message'] = trans('admin::messages.not-updated', ['name' => 'Page Details']) . "<br /><b> Error Details</b> - " . $e->getMessage();
            Log::info(": " . $e->getMessage());
        }
        return $response;
    }
}
