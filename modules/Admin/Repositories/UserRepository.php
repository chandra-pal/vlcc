<?php

namespace Modules\Admin\Repositories;

use Modules\Admin\Models\User;
use Modules\Admin\Services\Helper\ImageHelper;
use Modules\Admin\Models\UserType;
use Exception;
use Route;
use Auth;
use Log;
use Cache;
use URL;
use File;
use DB;
use PDO;
use Modules\Admin\Models\Center;
use Modules\Admin\Models\Member;
use Modules\Admin\Repositories\MembersRepository;

class UserRepository extends BaseRepository {

    protected $ttlCache = 60; // minutes to leave Cache

    /**
     * Create a new UserRepository instance.
     *
     * @param  Modules\Admin\Models\User $user
     * @return void
     */

    public function __construct(User $user, Member $member, MembersRepository $membersRepository) {
        $this->model = $user;
        $this->member = $member;
        $this->membersRepository = $membersRepository;
    }

    /**
     * Group actions on Users
     *
     * @param  int  $status
     * @return int
     */
    public function groupAction($inputs) {
        if (empty($inputs['action'])) {
            return ['status' => 'fail', 'message' => trans('admin::messages.error-update')];
        }

        $resultStatus = false;
        $action = $inputs['action'];
        switch ($action) {
            case "update":
                $userIds = explode(',', $inputs['ids']);
                foreach ($userIds as $key => $userId) {
                    $id = (int) $userId;
                    $user = $this->getById($id);
                    if (!empty($user)) {
                        switch ($inputs['value']) {
                            case 'status-0': $user->status = 0;
                                break;
                            case 'status-1': $user->status = 1;
                                break;
                            case 'skip_ip_check-0': $user->skip_ip_check = 0;
                                break;
                            case 'skip_ip_check-1': $user->skip_ip_check = 1;
                                break;
                        }
                        $user->save();
                        $resultStatus = true;
                    }
                }

                break;
            case "delete":
                $userIds = explode(',', $inputs['ids']);
                foreach ($userIds as $key => $userId) {
                    $id = (int) $userId;
                    $user = $this->getById($id);
                    if (!empty($user)) {
                        $user->delete();
                        $resultStatus = true;
                    }
                }
                break;
            case "delete-hard":
                $userIds = explode(',', $inputs['ids']);
                foreach ($userIds as $key => $userId) {
                    $id = (int) $userId;
                    $user = $this->getById($id);
                    if (!empty($user)) {
                        $user->forceDelete();
                        $resultStatus = true;
                    }
                }
                break;
            case "restore":
                $userIds = explode(',', $inputs['ids']);
                foreach ($userIds as $key => $userId) {
                    $id = (int) $userId;
                    $user = $this->getById($id);
                    if (!empty($user)) {
                        $user->restore();
                        $resultStatus = true;
                    }
                }
                break;
            default:
                break;
        }

        if ($resultStatus) {
            $action = (!empty($inputs['action'])) ? $inputs['action'] : 'update';
            switch ($action) {
                case 'delete' :
                    $message = trans('admin::messages.deleted', ['name' => trans('admin::messages.user(s)')]);
                    break;
                case 'delete-hard' :
                    $message = trans('admin::messages.deleted-hard-msg', ['name' => trans('admin::messages.user(s)')]);
                    break;
                case 'restore' :
                    $message = trans('admin::messages.restored', ['name' => trans('admin::messages.user(s)')]);
                    break;
                case 'update' :
                default:
                    $message = trans('admin::messages.group-action-success');
            }
            $response = ['status' => 'success', 'message' => $message];
        } else {
            $response = ['status' => 'fail', 'message' => trans('admin::messages.error-update')];
        }

        return $response;
    }

    /**
     * Admin users listing
     *
     * @param  array  $params
     * @return Response
     */
    public function data($params = []) {
        $cacheKey = str_replace(['\\'], [''], __METHOD__) . ':' . md5(json_encode($params));
        $response = Cache::tags(User::table())->remember($cacheKey, $this->ttlCache, function() {
            return User::with('UserType', 'AdminCenter')->get();
        });
        return $response;
    }

    public function centerList($params = []) {
        $cacheKey = str_replace(['\\'], [''], __METHOD__) . ':' . md5(json_encode($params));
        $response = Cache::tags(Center::table())->remember($cacheKey, $this->ttlCache, function() {
            return Center::groupBy('center_name')->lists('center_name', 'id');
        });

        return $response;
    }

    /**
     * Trashed Admin users listing
     *
     * @param  array  $params
     * @return Response
     */
    public function trashedData($params = []) {
        $cacheKey = str_replace(['\\'], [''], __METHOD__) . ':' . md5(json_encode($params));
        $response = Cache::tags(User::table())->remember($cacheKey, $this->ttlCache, function() {
            return User::with('UserType')->onlyTrashed()->get();
        });

        return $response;
    }

    /**
     * Save the Admin.
     *
     * @param  Modules\Admin\Models\User $user
     * @param  Array  $inputs
     * @return void
     */
    private function save($user, $inputs) {
        $fillable = $user['fillable'];
        if (!empty($inputs)) {
            foreach ($inputs as $key => $val) {
                if ($key != 'password' && in_array($key, $fillable)) {
                    $user->$key = $val;
                }
            }
        }

        if (!empty($inputs['password'])) {
            $user->password = bcrypt($inputs['password']);
        }

        if (!empty($inputs['user_type_id'])) {
            $userType = UserType::find($inputs['user_type_id']);
            $user->userType()->associate($userType);
        }
        $this->updateAvatar($inputs, $user);
        $this->saveUserLinks($inputs, $user);


        return $user->save();
    }

    /**
     * Store a Admin.
     *
     * @param  array $inputs
     * @return void
     */
    public function create($inputs) {
        try {
            if (isset($inputs['email']) && empty($inputs["email"])) {
                $inputs["email"] = null;
            }
            //create user
            $inputs['password'] = bcrypt($inputs['password']);
            $user = User::create($inputs);
            //apply association to save user type
            $userType = UserType::find($inputs['user_type_id']);
            $user->userType()->associate($userType);
            $user->password = $inputs['password'];
            $user->save();
            $this->updateAvatar($inputs, $user);
            $this->saveUserLinks($inputs, $user);

            //inserting records in admin_centers table
            if (isset($inputs['center_id']) && !empty($inputs['center_id'])) {
                $centersIds = $inputs['center_id'];
                $user->attachUserCenters($centersIds);
            }
            if ($user) {
                if (isset($inputs['submit_save'])) {
                    $userLabel = trans('admin::messages.user');
                    $message = trans('admin::messages.added', ['name' => $userLabel]) . ' ' . trans('admin::messages.add-save-message', ['name' => $userLabel]);
                    $response['redirect'] = URL::to('admin/user/create');
                } else {
                    $message = trans('admin::controller/user.created');
                    $response['redirect'] = URL::to('admin/user');
                }
                $response['status'] = 'success';
                $response['message'] = $message;
            } else {
                $response['status'] = 'error';
                $response['message'] = trans('admin::messages.not-added', ['name' => trans('admin::messages.user')]);
            }

            return $response;
        } catch (Exception $e) {

            $exceptionDetails = $e->getMessage();
            $response['status'] = 'error';
            $response['message'] = trans('admin::messages.not-added', ['name' => trans('admin::messages.user')]) . "<br /><b> Error Details</b> - " . $exceptionDetails;
            Log::error(trans('admin::messages.not-added', ['name' => trans('admin::messages.user')]), ['Error Message' => $exceptionDetails, 'Current Action' => Route::getCurrentRoute()->getActionName()]);

            return $response;
        }
    }

    /**
     * Update a admin.
     *
     * @param  array  $inputs
     * @param  Modules\Admin\Models\User $user
     * @return void
     */
    public function update($inputs, $user) {
        try {
            if (isset($inputs['email']) && empty($inputs["email"])) {
                $inputs["email"] = null;
            }
            //$save = $this->save($user, $inputs);
            //inserting records in admin_centers table
            if (isset($inputs['center_id']) && !empty($inputs['center_id'])) {
                // if (!empty($inputs['center_id'])) {
                $centers = $inputs['center_id'];
            } else {
                $centers = [];
            }
                $existingData = $this->getCentersIdsByUser($user->id);
                $addCenterIds = array_diff($centers, $existingData);
                if (!empty($addCenterIds)) {
                    $user->attachUserCenters($addCenterIds);
                    Cache::tags(Center::table())->flush();
                }

                //deleting records from admin_centers table
                $deleteCenterIds = array_diff($existingData, $centers);
                if (!empty($deleteCenterIds)) {
                    $user->detachUserCenters($deleteCenterIds);
                    Cache::tags(Center::table())->flush();
                }
            
                $save = $this->save($user, $inputs);

            if ($save) {
                $response['redirect'] = URL::to('admin/user');
                $response['status'] = 'success';
                $response['message'] = trans('admin::controller/user.updated');
            } else {
                $response['status'] = 'error';
                $response['message'] = trans('admin::messages.not-updated');
            }

            return $response;
        } catch (Exception $e) {

            $exceptionDetails = $e->getMessage();
            $response['status'] = 'error';
            $response['message'] = trans('admin::messages.not-updated') . "<br /><b> Error Details</b> - " . $exceptionDetails;
            Log::error("User update fail.", ['Error Message' => $exceptionDetails, 'Current Action' => Route::getCurrentRoute()->getActionName()]);

            return $response;
        }
    }

    /**
     * Get Admin Bulk Action collection.
     * @return Array
     */
    public function getAdminBulkActionSelect() {
        $selectArray = array();
        if ((!empty(Auth::guard('admin')->user()->hasEdit) || (!empty(Auth::guard('admin')->user()->hasOwnEdit))) && (!empty(Auth::guard('admin')->user()->hasDelete) || (!empty(Auth::guard('admin')->user()->hasOwnDelete)))) {
            $selectArray = ['' => 'Select', 'status-0' => 'Inactivate', 'status-1' => 'Activate', 'delete' => 'Delete', 'skip_ip_check-0' => 'IP Address Check On', 'skip_ip_check-1' => 'IP Address Check Off'];
        } else if (!empty(Auth::guard('admin')->user()->hasEdit) || (!empty(Auth::guard('admin')->user()->hasOwnEdit))) {
            $selectArray = ['' => 'Select', 'status-0' => 'Inactivate', 'status-1' => 'Activate', 'skip_ip_check-0' => 'IP Address Check On', 'skip_ip_check-1' => 'IP Address Check Off'];
        } else if (!empty(Auth::guard('admin')->user()->hasDelete) || (!empty(Auth::guard('admin')->user()->hasOwnDelete))) {
            $selectArray = ['' => 'Select', 'delete' => 'Delete'];
        } else {
            $selectArray = ['' => 'Select'];
        }
        return $selectArray;
    }

    /**
     * Get Admin Bulk Action collection.
     * @return Array
     */
    public function getTrashBulkActionSelect() {
        return ['' => 'Select', 'delete-hard' => 'Delete', 'restore' => 'Restore'];
    }

    /**
     * Update user avatar.
     *
     * @param  array  $inputs
     * @param  Modules\Admin\Models\User $user
     * @return void
     */
    public function updateAvatar($inputs, $user) {
        if (!empty($inputs['avatar'])) {
            //unlink old file
            if (!empty($user->avatar)) {
                File::Delete(public_path() . ImageHelper::getUserUploadFolder($user->id) . $user->avatar);
            }
            $user->avatar = ImageHelper::uploadUserAvatar($inputs['avatar'], $user);
            $user->save();
        } else if ($inputs['remove'] == 'remove') {
            $user->avatar = '';
            $user->save();
        } else {
            $user->save();
        }
    }

    /**
     * chekc field value present
     *
     * @param  string  $inputs
     * @return int
     */
    public function checkField($inputs = []) {
        if (!empty($inputs['field']) && !empty($inputs['value'])) {
            return $this->model
                            ->where($inputs['field'], '=', $inputs['value'])->count();
        }

        return false;
    }

    /**
     * To checke login credential require Ip address check validation or not
     *
     * @param  string $userLoginInput
     * @return boolean
     */
    public function isIpaddressCheckRequire($userLoginInput) {
        $checkStatus = true;
        $userFieldInput = filter_var($userLoginInput, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';
        $user = User::where($userFieldInput, '=', $userLoginInput)->first();
        if ($user->skip_ip_check) {
            $checkStatus = false;
        }
        return $checkStatus;
    }

    /**
     * give userId by username
     *
     * @param  string $userName
     * @return string
     */
    public function getUserIdByUsername($userName) {
        $cacheKey = str_replace(['\\'], [''], __METHOD__) . ':' . md5($userName);
        $response = Cache::tags(User::table())->remember($cacheKey, $this->ttlCache, function() use ($userName) {
            $user = User::where('username', $userName)->get()->first();
            if (!empty($user)) {
                $data = $user->toArray();
                return $data['id'];
            }
        });

        return $response;
    }

    /* save user links
     *
     * @param  string  $inputs
     * @return int
     */

    public function saveUserLinks($inputs, $user) {
        if (isset($inputs['links']) && !empty($inputs['links'])) {
            foreach ($inputs['links'] as $link) {
                if (empty($inputs['user_links'][$link])) {
                    $inputs['user_links'][$link] = array('is_add' => 0, 'is_edit' => 0, 'is_delete' => 0, 'own_view' => 0, 'own_edit' => 0, 'own_delete' => 0);
                }
            }
        }
        $userLinks = (!empty($inputs['user_links'])) ? $inputs['user_links'] : [];
        //format modified link array to assign 0 to unchecked elements
        foreach ($userLinks as $kk => $val) {
            $userLinks[$kk] = $user->assignTableColums($val);
        }
        $selectedLinks = (!empty($inputs['user_links'])) ? array_keys($inputs['user_links']) : [];

        $exisitingLinks = $this->listUserLinks($user->id);
        $insertRecords = array_diff($selectedLinks, $exisitingLinks);
        $updateRecords = $this->getModifiedUserLinks($user->id, $userLinks);
        $deleteRecords = array_diff($exisitingLinks, $selectedLinks);

        //logic for inserting newly selected checkboxes by filtering old records
        if (!empty($insertRecords)) {
            $insertUserLinks = array_intersect_key($userLinks, array_flip($insertRecords));
            $user->attachLinks($insertUserLinks);
            Cache::tags(User::table())->flush();
        }

        //logic for update existing records
        if (!empty($updateRecords)) {
            $user->updateLinks($updateRecords);
            Cache::tags(User::table())->flush();
        }

        //logic for deleting previously present and current deselected checkboxes records
        if (!empty($deleteRecords)) {
            $user->detachLinks($deleteRecords);
            Cache::tags(User::table())->flush();
        }
    }

    /**
     * User wise links id and link permissions from user_links table
     *
     * @param  $userId
     * @return $response of all typewise links id
     */
    public function listUserLinks($userId) {
        $cacheKey = str_replace(['\\'], [''], __METHOD__) . ':' . md5($userId);
        $response = Cache::tags(User::table())->remember($cacheKey, $this->ttlCache, function() use ($userId) {
            return User::find($userId)->links()->lists('link_id')->toArray();
        });

        return $response;
    }

    /**
     * User wise links with columns from user_links table
     *
     * @param  $userId
     * @return $response of all links
     */
    public function listUserLinksWithColumns($userId = '') {
        $cacheKey = str_replace(['\\'], [''], __METHOD__);
        $response = Cache::tags(User::table())->remember($cacheKey, $this->ttlCache, function() use($userId) {
            if ($userId) {
                $userLinks = User::find($userId)->links()->get()->toArray();
                $result = [];
                foreach ($userLinks as $link) {
                    unset($link['pivot']['user_id']);
                    unset($link['pivot']['link_id']);
                    $result[$link['id']] = $link['pivot'];
                }
                return $result;
            }
        });

        return $response;
    }

    /**
     * Get drop down of user selected links with caegory
     * @params user model obj
     * @return response
     */
    public function getUserSelectLinks($userId = '') {

        $cacheKey = str_replace(['\\'], [''], __METHOD__) . '_' . $userId;
        $response = Cache::tags(User::table())->remember($cacheKey, $this->ttlCache, function() use($userId) {
            DB::setFetchMode(PDO::FETCH_ASSOC);

            $rescords = DB::table('link_categories')
                    ->leftJoin('links', 'link_categories.id', '=', 'links.link_category_id')
                    ->leftJoin('user_links', 'links.id', '=', 'user_links.link_id')
                    ->select('category', 'links.id', 'link_name')
                    ->where('user_links.user_id', '=', $userId)
                    ->orderBy('link_categories.position')
                    ->orderBy('links.position')
                    ->get();

            DB::setFetchMode(PDO::FETCH_CLASS);
            $response = collect($rescords)->groupBy('category');
            $dropdown = [];
            if (!empty($response)) {
                foreach ($response as $key => $category) {
                    $option = [];
                    foreach ($category as $link) {
                        $option[$link['id']] = $link['link_name'];
                    }
                    $dropdown[$key] = $option;
                }
            }

            return $dropdown;
        });

        return $response;
    }

    /**
     * get modified user links from posted and old records
     *
     * @param  $userId
     * @param  $userLinks
     * @return $response of match records
     */
    public function getModifiedUserLinks($userId = '', $userLinks = []) {
        $links = [];
        //Fetch all links assigned to user
        $exisitingUserLinks = $this->listUserLinksWithColumns($userId);
        //Get currently modified links
        $present = array_intersect(array_keys($userLinks), array_keys($exisitingUserLinks));
        //format the modified link array in the format of existing array
        $updateUserLinks = array_intersect_key($exisitingUserLinks, array_flip($present));

        //check for modification in each link
        foreach ($updateUserLinks as $key => $link) {
            if (!empty($link) && !empty($userLinks[$key])) {
                if (!empty(array_diff_assoc($userLinks[$key], $link))) {
                    $links[$key] = $userLinks[$key];
                }
            }
        }
        return $links;
    }

    /**
     * Update a admin.
     *
     * @param  array  $inputs
     * @param  Modules\Admin\Models\User $user
     * @return void
     */
    public function updateProfile($inputs, $user) {
        try {

            $fillable = $user['fillable'];
            if (!empty($inputs)) {
                foreach ($inputs as $key => $val) {
                    if (in_array($key, $fillable)) {
                        $user->$key = $val;
                    }
                }
            }

            if (!empty($inputs['password'])) {
                $user->password = bcrypt($inputs['password']);
            }

            if (!empty($inputs['user_type_id'])) {
                $userType = UserType::find($inputs['user_type_id']);
                $user->userType()->associate($userType);
            }

            $save = $user->save();

            if ($save) {
                $response['redirect'] = URL::to('admin/user');
                $response['status'] = 'success';
                $response['message'] = trans('admin::controller/user.updated');
            } else {
                $response['status'] = 'error';
                $response['message'] = trans('admin::messages.not-updated');
            }

            return $response;
        } catch (Exception $e) {

            $exceptionDetails = $e->getMessage();
            $response['status'] = 'error';
            $response['message'] = trans('admin::messages.not-updated') . "<br /><b> Error Details</b> - " . $exceptionDetails;
            Log::error("User update fail.", ['Error Message' => $exceptionDetails, 'Current Action' => Route::getCurrentRoute()->getActionName()]);

            return $response;
        }
    }

    public function addUserCenters($userId, $centerId) {
        DB::connection()->getPdo()->setAttribute(PDO::ATTR_EMULATE_PREPARES, true);
        $response = DB::select('CALL insertAdminCenters(' . $userId . ',"' . $centerId . '")');
        DB::connection()->getPdo()->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
        return $response[0];
    }

    /**
     * Display a listing of the resource.(users for selected center(staff availability))
     *
     * @return Response
     */
    public function listStaffData($centerId) {
        $centerId = (int) $centerId;
        DB::setFetchMode(PDO::FETCH_ASSOC);
        $request_details = [];
        if (isset($centerId) && $centerId != '') {
            $request_details = DB::table('admin_centers')
                    ->join('admins', 'admin_centers.user_id', '=', 'admins.id')
                    ->select(DB::raw('CONCAT(admins.first_name, " ", admins.last_name) AS first_name'), 'admins.id')
                    ->where('admin_centers.center_id', '=', $centerId)
                    ->where('admins.status', '=', 1)
                    //->whereIn('admins.user_type_id', [4, 5, 7, 8, 9, 10])
                    ->whereNotIn('admins.user_type_id', [1,2,3,9,11])
                    ->orderBy('admins.first_name')
                    ->get();
        }
        $usersData = collect($request_details)->toArray();
        $users = array_column($usersData, 'first_name', 'id');
        return $users;
    }

    /**
     * Fetch exisitng centers from admin_centers
     * @param $user_id
     * @return $response of all exisiting Centers with same $user_id
     */
    public function getCentersIdsByUser($user_id) {
        $cacheKey = str_replace(['\\'], [''], __METHOD__) . ':' . md5($user_id);
        $response = Cache::tags(Center::table())->remember($cacheKey, $this->ttlCache, function() use ($user_id) {
            return User::find($user_id)->center()->lists('center_id')->toArray();
        });
        return $response;
    }

    public function syncUsers($userName, $userId) {
        $crmCenterId = User::find($userId)->center()->lists('crm_center_id')->toArray();
        $crmCenterId = isset($crmCenterId[0]) ? $crmCenterId[0] : '';
        $response["message"] = "Center not available for this user";
        $response["status"] = "error";
        if (!empty($crmCenterId)) {
            $membersRepository = new $this->membersRepository($this->member);
            $importResponse = $membersRepository->importMemberData(['username' => $userName]);
            $importResponse['crm_center_id'] = (isset($importResponse['crm_center_id']) && ($importResponse['crm_center_id'] != "")) ? $importResponse['crm_center_id'] : "";
            $crmCenterId = ($crmCenterId != "") ? $crmCenterId : $importResponse['crm_center_id'];
            if ($crmCenterId != "") {
                $crmCenterIdArr = explode(",", $crmCenterId);
                foreach ($crmCenterIdArr as $crmCenterIdElet) {
                    $addCenterResponse = $this->addUserCenters($userId, $crmCenterIdElet);
                    if ($addCenterResponse->status == 0 && $addCenterResponse->lastInsertId == 0) {
                        //$this->info("Center ". $crmCenterIdElet. " already exists or center insert failed.");
                        $response["message"] = "Center " . $crmCenterIdElet . " already exists or center insert failed.";
                    } else {
                        //$this->info("Center ". $crmCenterIdElet. " added successfully.");
                        $response["message"] = "Center " . $crmCenterIdElet . " added successfully.";
                        $response["status"] = "success";
                    }
                }
            }
        }
        return $response;
    }

}
