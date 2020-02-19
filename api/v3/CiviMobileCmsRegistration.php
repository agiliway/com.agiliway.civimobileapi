<?php

/**
 * @param $params
 * @return array
 * @throws api_Exception
 */
function civicrm_api3_civi_mobile_cms_registration_create($params) {
  $result = (new CRM_CiviMobileAPI_Api_CiviMobileCmsRegistration_Create($params))->getResult();

  return civicrm_api3_create_success($result);
}

/**
 * Adjust Metadata for get action
 *
 * The metadata is used for setting defaults, documentation & validation
 * @param array $params array or parameters determined by getfields
 */
function _civicrm_api3_civi_mobile_cms_registration_create_spec(&$params) {
  $params['email'] = [
    'title' => 'Email',
    'description' => ts('Email'),
    'api.required' => 1,
    'type' => CRM_Utils_Type::T_STRING,
  ];
  $params['username'] = [
    'title' => 'Username',
    'description' => ts('Username'),
    'api.required' => 1,
    'type' => CRM_Utils_Type::T_STRING,
  ];
  $params['first_name'] = [
    'title' => 'First name',
    'description' => ts('First name'),
    'api.required' => 1,
    'type' => CRM_Utils_Type::T_STRING,
  ];
  $params['last_name'] = [
    'title' => 'Last name',
    'description' => ts('Last name'),
    'api.required' => 1,
    'type' => CRM_Utils_Type::T_STRING,
  ];
  $params['password'] = [
    'title' => 'password',
    'description' => ts('Password'),
    'api.required' => 1,
    'type' => CRM_Utils_Type::T_STRING,
  ];
}
