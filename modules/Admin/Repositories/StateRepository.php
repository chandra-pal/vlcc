<?php namespace Modules\Admin\Repositories;

use Modules\Admin\Models\State;
use Modules\Admin\Models\Country;
use Exception;
use Route;
use Log;
use Cache;

class StateRepository
{

    // minutes to leave Cache
    protected $ttlCache = 60;

    /**
     * Create a new RolegRepository instance.
     *
     * @param  Modules\Admin\Models\State $model
     * @return void
     */
    public function __construct(State $model)
    {
        $this->model = $model;
    }

    /**
     * Display a listing
     *
     * @return Response
     */
    public function data($params = [])
    {
        //return State::select('*')->with('Country')->orderBy('id')->get();
        $cacheKey = str_replace(['\\'], [''], __METHOD__) . ':' . md5(json_encode($params));
        $response = Cache::tags(State::table())->remember($cacheKey, $this->ttlCache, function() {
            return State::select('*')->with('Country')->orderBy('country_id')->orderBy('name')->get();
        });

        return $response;
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function listStateData($countryId)
    {
        $countryId = (int) $countryId;
        $cacheKey = str_replace(['\\'], [''], __METHOD__) . ':' . md5(json_encode($countryId));
        //Cache::tags not suppport with files and Database
        $response = Cache::tags(State::table())->remember($cacheKey, $this->ttlCache, function() use($countryId) {
            return State::whereCountryId($countryId)->orderBY('name')->lists('name', 'id');
        });

        return $response;
    }

    /**
     * Update a category.
     *
     * @param  Form data posted from ajax $inputs, Modules\Admin\Models\State $model
     * @return $result array with status and message elements
     * @return void
     */
    public function update($inputs, $model)
    {
        try {

            foreach ($inputs as $key => $value) {
                if (isset($model->$key)) {
                    $model->$key = $value;
                }
            }
            $model->country()->associate(Country::find($inputs['country_id']));
            $save = $model->save();

            if ($save) {
                $response['status'] = 'success';
                $response['message'] = trans('admin::messages.updated', ['name' => trans('admin::controller/state.state')]);
            } else {
                $response['status'] = 'error';
                $response['message'] = trans('admin::messages.not-updated', ['name' => trans('admin::controller/state.state')]);
            }

            return $response;
        } catch (Exception $e) {

            $exceptionDetails = $e->getMessage();
            $response['status'] = 'error';
            $response['message'] = trans('admin::messages.not-updated', ['name' => trans('admin::controller/state.state')]) . "<br /><b> Error Details</b> - " . $exceptionDetails;
            Log::error(trans('admin::messages.not-updated', ['name' => trans('admin::controller/state.state')]), ['Error Message' => $exceptionDetails, 'Current Action' => Route::getCurrentRoute()->getActionName()]);

            return $response;
        }
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
            $model = new $this->model;
            $allColumns = $model->getTableColumns($model->getTable());
            foreach ($inputs as $key => $value) {
                if (in_array($key, $allColumns)) {
                    $model->$key = $value;
                }
            }
            $model->country()->associate(Country::find($inputs['country_id']));
            $save = $model->save();

            if ($save) {
                $response['status'] = 'success';
                $response['message'] = trans('admin::messages.added', ['name' => trans('admin::controller/state.state')]);
            } else {
                $response['status'] = 'error';
                $response['message'] = trans('admin::messages.not-added', ['name' => trans('admin::controller/state.state')]);
            }

            return $response;
        } catch (Exception $e) {
            $exceptionDetails = $e->getMessage();
            $response['status'] = 'error';
            $response['message'] = trans('admin::messages.not-added', ['name' => trans('admin::controller/state.state')]) . "<br /><b> Error Details</b> - " . $exceptionDetails;
            Log::error(trans('admin::messages.not-added', ['name' => trans('admin::controller/state.state')]), ['Error Message' => $exceptionDetails, 'Current Action' => Route::getCurrentRoute()->getActionName()]);

            return $response;
        }
    }
}
