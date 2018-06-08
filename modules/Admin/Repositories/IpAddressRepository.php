<?php

namespace Modules\Admin\Repositories;

use Modules\Admin\Models\IpAddress;
use Modules\Admin\Models\IpLoginFail;
use Exception;
use Route;
use Auth;
use Log;
use Cache;

class IpAddressRepository extends BaseRepository
{

    protected $ttlCache = 60; // minutes to leave Cache
    /**
     * The Role instance.
     *
     * @var Modules\Admin\Models\Ipaddress
     */
    protected $ipaddress;

    /**
     * Create a new RolegRepository instance.
     *
     * @param  Modules\Admin\Models\Ipaddress $model
     * @return void
     */
    public function __construct(IpAddress $model)
    {
        $this->ipaddress = $model;
    }

    /**
     * Display a listing 
     *
     * @return Response
     */
    public function data($params = [])
    {
        $cacheKey = str_replace(['\\'], [''], __METHOD__) . ':' . md5(json_encode($params));
        $response = Cache::tags(IpAddress::table(), IpLoginFail::table())->remember($cacheKey, $this->ttlCache, function() {
            return IpAddress::select('*')->with('IpLoginFail')->orderBy('id')->get();
        });

        return $response;
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
            return ['status' => 'fail', 'message' => trans('admin::messages.error-update')];
        }

        $resultStatus = false;
        $action = $inputs['action'];
        switch ($action) {
            case "status":
                $ipaddressIds = explode(',', $inputs['ids']);
                foreach ($ipaddressIds as $key => $ipaddressId) {
                    $id = (int) $ipaddressId;
                    $ipaddress = $this->ipaddress->find($id);
                    if (!empty($ipaddress)) {
                        $ipaddress->status = $inputs['value'];
                        $ipaddress->save();
                        $resultStatus = true;
                    }
                }
                break;
            default:
                break;
        }

        if ($resultStatus) {
            $message = trans('admin::messages.updated', ['name' => 'IP Address status']);
            $response = ['status' => 'success', 'message' => $message];
        } else {
            $response = ['status' => 'fail', 'message' => trans('admin::messages.error-update')];
        }

        return $response;
    }

    /**
     * Update a category.
     *
     * @param  Form data posted from ajax $inputs, Modules\Admin\Models\IPAddress $ipAddress
     * @return $result array with status and message elements
     * @return void
     */
    public function update($inputs, $ipAddress)
    {
        try {
            $ipAddress->ip_address = $inputs['ip_address'];
            $ipAddress->status = $inputs['status'];
            $ipAddress->updated_by = Auth::guard('admin')->user()->id;

            $save = $ipAddress->save();

            if ($save) {
                $response['status'] = 'success';
                $response['message'] = trans('admin::messages.updated', ['name' => 'IP Address']);
            } else {
                $response['status'] = 'error';
                $response['message'] = trans('admin::messages.not-updated', ['name' => 'IP Address']);
            }

            return $response;
        } catch (Exception $e) {

            $exceptionDetails = $e->getMessage();
            $response['status'] = 'error';
            $response['message'] = trans('admin::messages.not-updated', ['name' => 'IP Address']) . "<br /><b> Error Details</b> - " . $exceptionDetails;
            Log::error("Configuration category could not be added.", ['Error Message' => $exceptionDetails, 'Current Action' => Route::getCurrentRoute()->getActionName()]);

            return $response;
        }
    }

    /**
     * Create or update a post.
     *
     * @param  array  $inputs
     * @param  bool   $user_id
     * @return Modules\Admin\Models\Ipaddress
     */
    public function store($inputs, $user_id = null)
    {
        $model = new $this->ipaddress;
        $model->ip_address = $inputs['ip_address'];
        $model->status = isset($inputs['status']) ? $inputs['status'] : 0;
        if ($user_id)
            $model->created_by = $user_id;

        $model->save();

        return $model;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  Form data posted from ajax $inputs
     * @return $result array with status and message elements
     */
    public function create($inputs)
    {
        try {
            $ipAddress = new $this->ipaddress;
            $ipAddress->ip_address = $inputs['ip_address'];
            $ipAddress->status = $inputs['status'];
            $ipAddress->created_by = Auth::guard('admin')->user()->id;

            $save = $ipAddress->save();

            if ($save) {
                $response['status'] = 'success';
                $response['message'] = trans('admin::messages.added', ['name' => 'IP Address']);
            } else {
                $response['status'] = 'error';
                $response['message'] = trans('admin::messages.not-added', ['name' => 'IP Address']);
            }

            return $response;
        } catch (Exception $e) {
            $exceptionDetails = $e->getMessage();
            $response['status'] = 'error';
            $response['message'] = trans('admin::messages.not-added', ['name' => 'IP Address']) . "<br /><b> Error Details</b> - " . $exceptionDetails;
            Log::error(trans('admin::messages.not-added', ['name' => 'IP Address']), ['Error Message' => $exceptionDetails, 'Current Action' => Route::getCurrentRoute()->getActionName()]);

            return $response;
        }
    }

    /**
     * Insert ipaddress record if unauthenticate
     * @param array $inputs
     */
    public function invalidIpAddressAttempt($inputs)
    {
        $insertData = [
            'ip_address' => $inputs['ip_address'],
            'status' => 0,/*If login attempts fail set Pending status*/
        ];

        $cacheKey = str_replace(['\\'], [''], __METHOD__);
        //Cache::tags not suppport with files and Database
        $ifexits = Cache::tags(IpAddress::table())->remember($cacheKey, $this->ttlCache, function() use( $insertData) {
            return $this->ipaddress->where('ip_address', $insertData['ip_address'])->first();
        });

        if (!$ifexits) {
            $this->ipaddress->create($insertData);
        }
    }
}
