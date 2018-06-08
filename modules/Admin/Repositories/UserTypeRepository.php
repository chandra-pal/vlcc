<?php
/**
 * The repository class for managing user type specific actions.
 *
 *
 * @author Tushar Dahiwale <tushard@iprogrammer.com>
 * @package Admin
 * @since 1.0
 */
namespace Modules\Admin\Repositories;

use Modules\Admin\Models\UserType;
use Modules\Admin\Repositories\LinksRepository;
use Modules\Admin\Models\Links;
use Exception;
use Route;
use Log;
use Cache;

class UserTypeRepository extends BaseRepository
{

    /**
     * Create a new UserTypeRepository instance.
     *
     * @param  Modules\Admin\Models\UserType $userType
     *
     * @return void
     */
    public function __construct(UserType $userType)
    {
        $this->model = $userType;
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
        $response = Cache::tags(UserType::table())->remember($cacheKey, $this->ttlCache, function() {
            return UserType::select(['id', 'name', 'description', 'priority', 'status', 'created_by'])->orderBy('id')->get();
        });

        return $response;
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function listPriorityData($params = [])
    {
        $cacheKey = str_replace(['\\'], [''], __METHOD__) . ':' . md5(json_encode($params));
        //Cache::tags not suppport with files and Database
        $response = Cache::tags(UserType::table())->remember($cacheKey, $this->ttlCache, function() {
            return $priorityList = collect(["1" => 1, "2" => 2, "3" => 3, "4" => 4, "5" => 5, "6" => 6, "7" => 7, "8" => 8, "9" => 9, "10" => 10]);
        });

        return $response;
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function listUserTypeData()
    {
        $userinfo = \Modules\Admin\Services\Helper\UserInfoHelper::getAuthUserInfo();
        $user_type_id = $userinfo->user_type_id;
        $usertypeinfo = $this->getUserTypeById($user_type_id);
        $priority = (int) $usertypeinfo->priority;
        
        //For flush all cache admin users Priority permissions
        Cache::tags(UserType::table())->flush();
        
        $cacheKey = str_replace(['\\'], [''], __METHOD__);
        //Cache::tags not suppport with files and Database
        $response = Cache::tags(UserType::table())->remember($cacheKey, $this->ttlCache, function() use($priority) {

            return UserType::where('priority', '>=', $priority)->orderBY('id')->lists('name', 'id');
        });

        return $response;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  Form data posted from ajax $inputs
     *
     * @return $result array with status and message elements
     */
    public function create($inputs)
    {
        try {
            $userType = new $this->model;

            $allColumns = $userType->getTableColumns($userType->getTable());

            foreach ($inputs as $key => $value) {
                if (in_array($key, $allColumns)) {
                    $userType->$key = $value;
                }
            }

            $save = $userType->save();

            if ($save) {
                $response['status'] = 'success';
                $response['message'] = trans('admin::messages.added', ['name' => 'User Type']);
            } else {
                $response['status'] = 'error';
                $response['message'] = trans('admin::messages.not-added', ['name' => 'User Type']);
            }

            return $response;
        } catch (Exception $e) {

            $exceptionDetails = $e->getMessage();
            $response['status'] = 'error';
            $response['message'] = trans('admin::messages.not-added', ['name' => 'User Type']) . "<br /><b> Error Details</b> - " . $exceptionDetails;
            Log::error(trans('admin::messages.not-added', ['name' => 'User Type']), ['Error Message' => $exceptionDetails, 'Current Action' => Route::getCurrentRoute()->getActionName()]);

            return $response;
        }
    }

    /**
     * Update a user type.
     *
     * @param  Form data posted from ajax $inputs, Modules\Admin\Models\UserType $userType
     *
     * @return $result array with status and message elements
     */
    public function update($inputs, $userType)
    {
        try {
            foreach ($inputs as $key => $value) {
                if (isset($userType->$key)) {
                    $userType->$key = $value;
                }
            }

            $save = $userType->save();

            if ($save) {
                $response['status'] = 'success';
                $response['message'] = trans('admin::messages.updated', ['name' => 'User Type']);
            } else {
                $response['status'] = 'error';
                $response['message'] = trans('admin::messages.not-updated', ['name' => 'User Type']);
            }

            return $response;
        } catch (Exception $e) {

            $exceptionDetails = $e->getMessage();
            $response['status'] = 'error';
            $response['message'] = trans('admin::messages.not-updated', ['name' => 'User Type']) . "<br /><b> Error Details</b> - " . $exceptionDetails;
            Log::error("User Type could not be updated.", ['Error Message' => $exceptionDetails, 'Current Action' => Route::getCurrentRoute()->getActionName()]);

            return $response;
        }
    }

    /**
     * For assigning links to user type
     *
     * @param  int  $userType
     * @return $response array with status and message elements
     */
    public function saveLinks($linksData, $userTypeId)
    {
        $linkRepository = new LinksRepository(new Links);
        $userType = $this->getUserTypeById($userTypeId);
        $exisitinglinks = $linkRepository->listTypewiseLinksData($userTypeId);
        $insertRecords = array_diff($linksData, $exisitinglinks);
        $deleteRecords = array_diff($exisitinglinks, $linksData);

        //logic for inserting newly selected checkboxes records
        if (!empty($insertRecords)) {
            $userType->attachLinks($insertRecords);
            Cache::tags(UserType::table())->flush();
        }

        //logic for deleting selected checkboxes records
        if (!empty($deleteRecords)) {
            $userType->detachLinks($deleteRecords);
            Cache::tags(UserType::table())->flush();
        }
        $response['status'] = 'success';
        $response['message'] = trans('admin::messages.updated', ['name' => trans('admin::controller/links.default-links')]);
        return $response;
    }

    /**
     * Get data by Id
     *
     * @return $response of UserType table
     */
    public function getUserTypeById($userType)
    {
        $cacheKey = str_replace(['\\'], [''], __METHOD__) . ':' . (int) $userType;
        $response = Cache::tags(UserType::table())->remember($cacheKey, $this->ttlCache, function() use ($userType) {
            return UserType::find($userType);
        });

        return $response;
    }

    public function getLinksByUserType($userTypeId)
    {
        $cacheKey = str_replace(['\\'], [''], __METHOD__) . ':' . (int) $userTypeId;
        $response = Cache::tags(UserType::table())->remember($cacheKey, $this->ttlCache, function() use ($userTypeId) {
            return UserType::with('Links')->where('id', $userTypeId)->get();
        });

        return $response;
    }
}
