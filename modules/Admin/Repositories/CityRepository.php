<?php
/**
 * The repository class for managing city specific actions.
 *
 *
 * @author Tushar Dahiwale <tushard@iprogrammer.com>
 * @package Admin
 * @since 1.0
 */
namespace Modules\Admin\Repositories;

use Modules\Admin\Models\Country;
use Modules\Admin\Models\State;
use Modules\Admin\Models\City;
use DB;
use PDO;
use Cache;

class CityRepository extends BaseRepository
{

    /**
     * Create a new CityRepository instance.
     *
     * @param  Modules\Admin\Models\City $city
     * @return void
     */
    public function __construct(City $city)
    {
        $this->model = $city;
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function data($params = [])
    {
        $cacheKey = str_replace(['\\'], [''], __METHOD__) . ':' . md5(json_encode($params));
        $response = Cache::tags(City::table(), Country::table(), State::table())->remember($cacheKey, $this->ttlCache, function() {
            return City::with('States', 'Country')->orderBy('country_id')->orderBy('state_id')->orderBy('name')->get();
        });

        return $response;
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
            $city = new $this->model;
            $allColumns = $city->getTableColumns($city->getTable());
            foreach ($inputs as $key => $value) {
                if (in_array($key, $allColumns)) {
                    $city->$key = $value;
                }
            }

            $country = Country::find($inputs['country_id']);
            $city->country()->associate($country);

            $state = State::find($inputs['state_id']);
            $city->states()->associate($state);

            $save = $city->save();

            if ($save) {
                $response['status'] = 'success';
                $response['message'] = trans('admin::messages.added', ['name' => 'City']);
            } else {
                $response['status'] = 'error';
                $response['message'] = trans('admin::messages.not-added', ['name' => 'City']);
            }

            return $response;
        } catch (Exception $e) {
            $exceptionDetails = $e->getMessage();
            $response['status'] = 'error';
            $response['message'] = trans('admin::messages.not-added', ['name' => 'City']) . "<br /><b> Error Details</b> - " . $exceptionDetails;
            Log::error("City could not be added.", ['Error Message' => $exceptionDetails, 'Current Action' => Route::getCurrentRoute()->getActionName()]);

            return $response;
        }
    }

    /**
     * Update a city.
     *
     * @param  Form data posted from ajax $inputs, Modules\Admin\Models\City $city
     * @return $result array with status and message elements
     */
    public function update($inputs, $city)
    {
        try {

            foreach ($inputs as $key => $value) {
                if (isset($city->$key)) {
                    $city->$key = $value;
                }
            }

            $country = Country::find($inputs['country_id']);
            $city->country()->associate($country);

            $state = State::find($inputs['state_id']);
            $city->states()->associate($state);

            $save = $city->save();
            if ($save) {
                $response['status'] = 'success';
                $response['message'] = trans('admin::messages.updated', ['name' => 'City']);
            } else {
                $response['status'] = 'error';
                $response['message'] = trans('admin::messages.not-updated', ['name' => 'City']);
            }

            return $response;
        } catch (Exception $e) {

            $exceptionDetails = $e->getMessage();
            $response['status'] = 'error';
            $response['message'] = trans('admin::messages.not-updated', ['name' => 'City']) . "<br /><b> Error Details</b> - " . $exceptionDetails;
            Log::error(trans('admin::messages.not-updated', ['name' => 'City']), ['Error Message' => $exceptionDetails, 'Current Action' => Route::getCurrentRoute()->getActionName()]);

            return $response;
        }
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function listCityData($stateId)
    {
        $stateId = (int) $stateId;
        $cacheKey = str_replace(['\\'], [''], __METHOD__) . ':' . md5(json_encode($stateId));
        //Cache::tags not suppport with files and Database
        $response = Cache::tags(State::table())->remember($cacheKey, $this->ttlCache, function() use($stateId) {
            return City::whereStateId($stateId)->orderBY('name')->lists('name', 'id');
        });

        return $response;
    }
}
