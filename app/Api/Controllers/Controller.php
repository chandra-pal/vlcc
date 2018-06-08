<?php

namespace App\Api\Controllers;

use Arrilot\Api\Skeleton\BaseController;
use Exception;

abstract class Controller extends BaseController {

    /**
     * Constants for error codes
     * @var const
     */
    const SUCCESS_RESPONSE_CODE = 200;
    const VALIDATION_FAIL = 400;
    const AUTH_RESPONSE_CODE = 101;
    const INVALID_PARAM_RESPONSE_CODE = 102;
    const NO_DATA_FOUND = 103;
    const RECORD_ALREADY_EXISTS = 104;
    const UNAUTH_RESPONSE_CODE = 401;
    const TOO_MANY_REQUEST_RESPONSE_CODE = 529;

    public function getResponse($apiStatus, $statusCode, $data = NULL, $statusDescription) {
        $status['status'] = array();
        $status['status']['code'] = $statusCode;
        $status['status']['success'] = $apiStatus;
        $status['response']['message'] = $statusDescription;
        $response = $status;
//        $response['data'] = $data;
        return $response;
    }

    public function validate($input, $requiredData) {
        $missingInput = array(); //array to contain error
        foreach ($input as $key => $value) {

            $key = array_search($key, $requiredData);
            if ($key !== false)
                unset($requiredData[$key]);
        }
        if (count($requiredData) !== 0) {
            $missingInput[] = 'Parameter missing : ' . implode(',', $requiredData);
        }
        if (!empty($missingInput)) {
            throw new Exception(implode(", ", $missingInput));
        }
    }

}
