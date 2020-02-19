<?php

/**
 * Gets Relationship
 *
 * @param array $params
 *
 * @return array
 * @throws \Civi\API\Exception\UnauthorizedException
 */
function civicrm_api3_civi_mobile_confirm_token_run($params) {
  _civicrm_api3_confirm_token_check_permission();
  $config = &CRM_Core_Config::singleton();
  $baseUrl = $config->userFrameworkBaseURL;

  $awgWordpressApiAccessToken = '06fb687ffd4bfb1965035adcc757f5e8c1835facbc18d104e099580314b41554459';
  $awgWordpressDomain = 'https://civimobile.org';
  $json = "{\n\t\"civicrm_server_token\" : \"" . trim($params['civicrm_server_token']) . "\"\n,";
  $json .= "\n\t\"civicrm_server_domain\" : \"" . $baseUrl . "\"\n }";

  $curl = curl_init();
  curl_setopt_array($curl, [
    CURLOPT_URL => $awgWordpressDomain . "/wp-json/api/confirm-token",
    CURLOPT_RETURNTRANSFER => TRUE,
    CURLOPT_ENCODING => "",
    CURLOPT_MAXREDIRS => 10,
    CURLOPT_TIMEOUT => 30,
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    CURLOPT_CUSTOMREQUEST => "POST",
    CURLOPT_POSTFIELDS => $json,
    CURLOPT_HTTPHEADER => [
      "api-access-token: " . $awgWordpressApiAccessToken,
      "content-type: application/json",
    ],
  ]);
  $response = curl_exec($curl);
  $err = curl_error($curl);
  curl_close($curl);

  if ($err) {
    $result = ['error' => 1, 'message' => ts('Error. Can not connect to server.')];
  } else {
    $json = json_decode($response, true);
    if (!empty($json) && isset($json['error']) && isset($json['message'])) {
      $result = ['error' => (int) $json['error'], 'message' => (string) $json['message']];
    } else {
      throw new \Civi\API\Exception\UnauthorizedException('Error. Can not read fields in json.');
    }
  }

  return civicrm_api3_create_success(["response" => $result], $params);
}

/**
 * Checks permissions
 */
function _civicrm_api3_confirm_token_check_permission() {
  if (!CRM_Core_Permission::check('administer CiviCRM')) {
    throw new \Civi\API\Exception\UnauthorizedException('Permission denied.');
  }
}

/**
 * Specify Metadata for get action.
 *
 * @param array $params
 */
function _civicrm_api3_civi_mobile_confirm_token_run_spec(&$params) {
  $params['civicrm_server_token'] = [
    'title' => 'CiviCRM server token',
    'description' => 'CiviCRM server token',
    'api.required' => 1,
    'type' => CRM_Utils_Type::T_STRING,
  ];
}
