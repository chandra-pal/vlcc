<?php
/**
 * The repository class for managing locations specific actions.
 *
 *
 * @author Rahul Kadu <rahulk@iprogrammer.com>
 * @package Admin
 * @since 1.0
 */
namespace Modules\Admin\Repositories;

use Modules\Admin\Models\Country;
use Modules\Admin\Models\State;
use Modules\Admin\Models\City;
use Modules\Admin\Models\Locations;
use DB;
use PDO;
use Cache;

class LocationsRepository extends BaseRepository
{

    /**
     * Create a new RolegRepository instance.
     *
     * @param  Modules\Admin\Models\Locations $model
     * @return void
     */
    public function __construct(Locations $locations)
    {
        $this->model = $locations;
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function data($params = [])
    {
        Cache::tags(Locations::table())->flush();
        $cacheKey = str_replace(['\\'], [''], __METHOD__) . ':' . md5(json_encode($params));
        $response = Cache::tags(Locations::table(), City::table(), Country::table(), State::table())->remember($cacheKey, $this->ttlCache, function() {
            return Locations::with('States', 'Country', 'City')->orderBy('country_id')->orderBy('state_id')->orderBy('city_id')->orderBy('location')->get();
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
            $locations = new $this->model;
            $allColumns = $locations->getTableColumns($locations->getTable());
            foreach ($inputs as $key => $value) {
                if (in_array($key, $allColumns)) {
                    $locations->$key = $value;
                }
            }

            $country = Country::find($inputs['country_id']);
            $locations->country()->associate($country);

            $state = State::find($inputs['state_id']);
            $locations->states()->associate($state);

            $city = City::find($inputs['city_id']);
            $locations->city()->associate($city);

//            $locations->location = $inputs['locations'];
//            $locations->address_1 = $inputs['address_line_1'];
//            $locations->address_2 = $inputs['address_line_2'];

            $save = $locations->save();

            if ($save) {
                $response['status'] = 'success';
                $response['message'] = trans('admin::messages.added', ['name' => 'Location']);
            } else {
                $response['status'] = 'error';
                $response['message'] = trans('admin::messages.not-added', ['name' => 'Location']);
            }

            return $response;
        } catch (Exception $e) {
            $exceptionDetails = $e->getMessage();
            $response['status'] = 'error';
            $response['message'] = trans('admin::messages.not-added', ['name' => 'Locations']) . "<br /><b> Error Details</b> - " . $exceptionDetails;
            Log::error("Locations could not be added.", ['Error Message' => $exceptionDetails, 'Current Action' => Route::getCurrentRoute()->getActionName()]);

            return $response;
        }
    }

    /**
     * Update a locations.
     *
     * @param  Form data posted from ajax $inputs, Modules\Admin\Models\Locations $locations
     * @return $result array with status and message elements
     */
    public function update($inputs, $locations)
    {
        try {

            foreach ($inputs as $key => $value) {
                if (isset($locations->$key)) {
                    $locations->$key = $value;
                }
            }

            $country = Country::find($inputs['country_id']);
            $locations->country()->associate($country);

            $state = State::find($inputs['state_id']);
            $locations->states()->associate($state);

            $city = City::find($inputs['city_id']);
            $locations->city()->associate($city);

//            $locations->location = $inputs['locations'];
//            $locations->address_1 = $inputs['address_line_1'];
//            $locations->address_2 = $inputs['address_line_2'];

            $save = $locations->save();
            if ($save) {
                $response['status'] = 'success';
                $response['message'] = trans('admin::messages.updated', ['name' => 'Location']);
            } else {
                $response['status'] = 'error';
                $response['message'] = trans('admin::messages.not-updated', ['name' => 'Location']);
            }

            return $response;
        } catch (Exception $e) {

            $exceptionDetails = $e->getMessage();
            $response['status'] = 'error';
            $response['message'] = trans('admin::messages.not-updated', ['name' => 'Locations']) . "<br /><b> Error Details</b> - " . $exceptionDetails;
            Log::error(trans('admin::messages.not-updated', ['name' => 'Locations']), ['Error Message' => $exceptionDetails, 'Current Action' => Route::getCurrentRoute()->getActionName()]);

            return $response;
        }
    }
    /**
     * Get post collection.
     *
     * @param  int     $n
     * @param  int     $user_id
     * @param  string  $orderby
     * @param  string  $direction
     * @return Illuminate\Support\Collection
     */
    /* public function indexFront($request)
      {

      $query = $this->model
      ->select('*');

      if (!empty($request['location'])) {
      $datalist = $query->where('location', 'like', '%' . $request['location'] . '%');
      }
      if (isset($request['status']) && $request['status'] != '') {
      $datalist = $query->where('status', '=', $request['status']);
      }
      switch ($request['order'][0]['column']) {
      case 1:
      $orderby = 'id';
      break;
      case 2:
      $orderby = 'location';
      break;
      case 3:
      $orderby = 'address';
      break;
      case 4:
      $orderby = 'country_id';
      break;
      case 5:
      $orderby = 'state_id';
      break;
      case 6:
      $orderby = 'city_id';
      break;
      case 7:
      $orderby = 'status';
      break;
      default:
      $orderby = 'id';
      }
      if (!empty($request['order'][0]['dir'])) {
      $query->orderBy($orderby, $request['order'][0]['dir']);
      }

      $datalist = $query->get();

      $iTotalRecords = count($datalist);

      $iDisplayLength = intval($request['length']);
      $iDisplayLength = $iDisplayLength < 0 ? $iTotalRecords : $iDisplayLength;
      $iDisplayStart = intval($request['start']);

      $records = array();
      $records["data"] = array();

      $end = $iDisplayStart + $iDisplayLength;
      $end = $end > $iTotalRecords ? $iTotalRecords : $end;

      $status = [];

      for ($i = $iDisplayStart; $i < $end; $i++) {
      $id = ($i + 1);
      $statusInt = $datalist[$i]->status;
      $createDate = date('Y-m-d H:i:s', strtotime($datalist[$i]->created_at));
      if ($statusInt == 1) {
      $status = array("success" => "Active");
      } else {
      $status = array("danger" => "Inactive");
      }
      $country = 'India';
      $state = 'Maharastra';
      $city = 'Pune';
      $address = $datalist[$i]->address.',<br/>'.$datalist[$i]->address1.',<br/>'.$datalist[$i]->landmark.',<br/>'.$datalist[$i]->zipcode;
      $records["data"][] = array(
      '<input type="checkbox" name="id[]" value="' . $id . '">',
      $id,
      $datalist[$i]->location,
      $address,
      $country,
      $state,
      $city,
      '<span class="label label-sm label-' . (key($status)) . '">' . (current($status)) . '</span>',
      '<a class="btn btn-xs default yellow-gold margin-bottom-5 edit-form-link" href="javascript:;" title="Edit"><i class="fa fa-pencil"></i> </a>'
      . '<a href="javascript:;" class="btn btn-xs default red-thunderbird margin-bottom-5 trash-form-link" title="Delete"><i class="fa fa-trash-o"></i></a>'
      );
      }
      if (isset($request["customActionType"]) && $request["customActionType"] == "group_action") {
      $records["customActionStatus"] = "OK";
      $inputs = ['id' => $request['id'], 'status' => $request['customActionName']];
      $this->updateStatus($inputs);
      $records["customActionMessage"] = "Record(s) updated successfully!";
      }

      $records["draw"] = intval($request['draw']);
      $records["recordsTotal"] = $iTotalRecords;
      $records["recordsFiltered"] = $iTotalRecords;

      return $records;
      } */
}
