<?php
/**
 * The repository class for managing menu groups specific actions.
 *
 *
 * @author Prashant Birajdar <prashantb@iprogrammer.com>
 * @package Admin
 * @since 1.0
 */
namespace Modules\Admin\Repositories;

use Modules\Admin\Models\MenuGroup;
use Exception;
use Route;
use Log;
use Cache;

class MenuGroupRepository extends BaseRepository
{

    /**
     * Create a new MenuGroupRepository instance.
     *
     * @param  Modules\Admin\Models\MenuGroup $model
     * @return void
     */
    public function __construct(MenuGroup $menuGroup)
    {
        $this->model = $menuGroup;
    }

    /**
     * Get a listing of the resource.
     *
     * @return Response
     */
    public function data($params = [])
    {
        $cacheKey = str_replace(['\\'], [''], __METHOD__) . ':' . md5(json_encode($params));
        //Cache::tags not suppport with files and Database
        $response = Cache::tags(MenuGroup::table())->remember($cacheKey, $this->ttlCache, function() {
            return MenuGroup::select([
                    'id', 'name', 'position', 'created_by', 'updated_by', 'created_at', 'updated_at'
                ])->orderBy('id')->get();
        });

        return $response;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  Form data posted from ajax $inputs
     * @return $result array with status and message elements
     */
    public function create($inputs, $user_id = null)
    {
        try {
            $menuGroup = new $this->model;

            $allColumns = $menuGroup->getTableColumns($menuGroup->getTable());

            foreach ($inputs as $key => $value) {
                if (in_array($key, $allColumns)) {
                    $menuGroup->$key = $value;
                }
            }
            

            $save = $menuGroup->save();

            if ($save) {
                $response['status'] = 'success';
                $response['message'] = trans('admin::messages.added', ['name' => trans('admin::controller/menu-group.add_new_group')]);
            } else {
                $response['status'] = 'error';
                $response['message'] = trans('admin::messages.not-added', ['name' => trans('admin::controller/menu-group.add_new_group')]);
            }

            return $response;
        } catch (Exception $e) {
            $exceptionDetails = $e->getMessage();
            $response['status'] = 'error';
            $response['message'] = trans('admin::messages.not-added', ['name' => trans('admin::controller/menu-group.add_new_group')]) . "<br /><b> Error Details</b> - " . $exceptionDetails;
            Log::error(trans('admin::messages.not-added', ['name' => trans('admin::controller/menu-group.add_new_group')]), ['Error Message' => $exceptionDetails, 'Current Action' => Route::getCurrentRoute()->getActionName()]);

            return $response;
        }
    }

    /**
     * Update an menu group.
     *
     * @param  Form data posted from ajax $inputs, Modules\Admin\Models\MenuGroup $menuGroup
     * @return $result array with status and message elements
     * @return void
     */
    public function update($inputs, $menuGroup)
    {
        try {

            foreach ($inputs as $key => $value) {
                if (isset($menuGroup->$key)) {
                    $menuGroup->$key = $value;
                }
            }
            

            $save = $menuGroup->save();

            if ($save) {
                $response['status'] = 'success';
                $response['message'] = trans('admin::messages.updated', ['name' => trans('admin::controller/menu-group.add_new_group')]);
            } else {
                $response['status'] = 'error';
                $response['message'] = trans('admin::messages.not-updated', ['name' => trans('admin::controller/menu-group.add_new_group')]);
            }

            return $response;
        } catch (Exception $e) {
            $exceptionDetails = $e->getMessage();
            $response['status'] = 'error';
            $response['message'] = trans('admin::messages.not-updated', ['name' => trans('admin::controller/menu-group.add_new_group')]) . "<br /><b> Error Details</b> - " . $exceptionDetails;
            Log::error(trans('admin::messages.not-updated', ['name' => trans('admin::controller/menu-group.add_new_group')]), ['Error Message' => $exceptionDetails, 'Current Action' => Route::getCurrentRoute()->getActionName()]);

            return $response;
        }
    }
    
    /**
     * Get menu group name
     *
     * @param  array  $inputs
     * @param  Modules\Admin\Models\MenuGroup $menuGroup
     * @return void
     */
    public function listMenuGroupData()
    {
        $cacheKey = str_replace(['\\'], [''], __METHOD__);
        //Cache::tags not suppport with files and Database
        $response = Cache::tags(MenuGroup::table())->remember($cacheKey, $this->ttlCache, function() {
            return MenuGroup::orderBY('position')->lists('name', 'id');
        });

        return $response;
    }
    
}
