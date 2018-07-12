<?php
/**
 * Sends JSON response
 */

class CRM_CiviMobileAPI_Utils_JsonResponse
{
    /**
     * Sends JSON response
     * @param $http_code
     * @param $data
     */
    public static function sendResponse($http_code, $data)
    {
        http_response_code($http_code);
        CRM_Utils_JSON::output($data);
    }
}