<?php

namespace Modules\Admin\Services;

use Illuminate\Validation\Validator;
use Hash;
use Illuminate\Support\Facades\Route;
use Modules\Admin\Models\Food;
use Modules\Admin\Models\MachineCenter;
use Modules\Admin\Http\Requests\Request;
use DB;
use PDO;

class Validation extends Validator
{

    public function validateTags($attribute, $value, $parameters)
    {
        return preg_match("/^[A-Za-z0-9-éèàù]{1,50}?(,[A-Za-z0-9-éèàù]{1,50})*$/", $value);
    }

    public function validateAlphaSpaces($attribute, $value, $parameters)
    {
        return preg_match('/^[\pL\s]+$/u', $value);
    }

    public function validateEmailMulti($attribute, $value)
    {
        $emails = explode(',', $value);
        foreach ($emails as $email) {
            $status = $this->validateEmail($attribute, $email);
            if ($status != '1') {
                return false;
            }
        }
        return true;
    }

    public function validateAddrSpecEmail($attribute, $value, $parameters)
    {
        if (preg_match('/\</', $value)) {
            $str = explode('<', $value);
            $email = preg_replace('/\>/', '', $str[1]);
            return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
        } else {
            return filter_var($value, FILTER_VALIDATE_EMAIL) !== false;
        }
    }

    public function validateCurrentPassword($attribute, $value, $parameters)
    {
        $this->requireParameterCount(1, $parameters, 'current_password');
        if (Hash::check($value, $parameters[2])) {
            return true;
        } else {
            return false;
        }
    }

    public function validateCurrentNewPassword($attribute, $value, $parameters)
    {
        $this->requireParameterCount(1, $parameters, 'password');
        if (Hash::check($value, $parameters[2])) {
            return false;
        } else {
            return true;
        }
    }

    public function validateLinkRoute($attribute, $value, $parameters)
    {
        if (Route::has($value)) {
            return true;
        } else {
            return false;
        }
    }

    public function validateUniqueMasterFoodName($attribute, $value, $parameters)
    {
        $foods = Food::select(['id', 'food_name'])
                ->where('food_type_id', $parameters[0])
                ->where('food_name', ucfirst($value))
                ->whereIn('created_by_user_type', [1, 2, 3, 4])->get();

        if (count($foods) > 0) {
            return false;
        }
        return true;
    }

    public function validateUniqueMachineNameCenterWise($attribute, $value, $parameters)
    {
        $inputSize = count($parameters);
        $updateFlag = $inputSize - 4;
        if (isset($parameters[$updateFlag]) && $parameters[$updateFlag] == "machine_id") {
            $machineId = $parameters[$inputSize - 3];
            $machineName = $parameters[$inputSize - 1];
            $centerIds = [];
            $existingCenterIds = DB::setFetchMode(PDO::FETCH_ASSOC);
            $existingCenterIds = DB::select("SELECT m1.center_id, m2.name FROM machine_centers m1 INNER JOIN machines m2 ON m1.machine_id= m2.id WHERE m1.machine_id=".$machineId);
            DB::setFetchMode(PDO::FETCH_CLASS);
            $existingCenterIds = array_column($existingCenterIds, 'center_id');
            $requestedCenterIds = array_slice($parameters, 0, $updateFlag);
            $diff = array_diff($requestedCenterIds, $existingCenterIds);
            if (!empty($diff)) {
                DB::setFetchMode(PDO::FETCH_ASSOC);
                $machineNames = DB::select("SELECT m1.id, m1.name FROM machines m1 LEFT OUTER JOIN machine_centers m2 ON  m1.id = m2.    machine_id WHERE m1.name = '" . ucfirst($machineName) . "' AND m2.center_id IN (" . implode(",", $diff) . ")");
                DB::setFetchMode(PDO::FETCH_CLASS);
                if (count($machineNames) > 0) {
                    return false;
                }
                return true;
            } else {
                return true;
            }
        } else {
            DB::setFetchMode(PDO::FETCH_ASSOC);
            $machineNames = DB::select("SELECT m1.id, m1.name FROM machines m1 LEFT OUTER JOIN machine_centers m2 ON  m1.id = m2.    machine_id WHERE m1.name = '" . ucfirst($value) . "' AND m2.center_id IN (" . implode(",", $parameters) . ")");
            DB::setFetchMode(PDO::FETCH_CLASS);
            if (count($machineNames) > 0) {
                return false;
            }
            return true;
        }
    }
}
