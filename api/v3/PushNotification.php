<?php

/**
 * Saves push tokens
 *
 * @param array $params
 * 
 * @return mixed
 */
function civicrm_api3_push_notification_create($params) {
  $isContactCreated = CRM_CiviMobileAPI_BAO_PushNotification::getAll($params);
  $params['is_active'] = (isset($params['is_active'])) ? $params['is_active'] : 1;

  $paramsForInsert = [
    'contact_id' => $params['contact_id'],
    'token' => $params['token'],
    'platform' => $params['platform'],
    'modified_date' => gmdate("Y-m-d H:i:s", time()),
    'is_active' => $params['is_active']
  ];

  if (empty($isContactCreated)) {
    $paramsForInsert['created_date'] = gmdate("Y-m-d H:i:s", time());
  } else {
    $paramsForInsert['id'] = array_shift($isContactCreated)['id'];
  }
  CRM_CiviMobileAPI_BAO_PushNotification::create($paramsForInsert);

  return civicrm_api3_create_success($params);
}

/**
 * Adjust Metadata for get action
 *
 * The metadata is used for setting defaults, documentation & validation
 * @param array $params array or parameters determined by getfields
 */
function _civicrm_api3_push_notification_create_spec(&$params) {
  $params['contact_id'] = [
    'title' => 'Contact ID',
    'description' => ts('Contact ID'),
    'api.required' => 1,
    'type' => CRM_Utils_Type::T_INT
  ];
  $params['token'] = [
    'title' => 'Token',
    'description' => ts('Token'),
    'api.required' => 1,
    'type' => CRM_Utils_Type::T_STRING
  ];
  $params['platform'] = [
    'title' => 'Platform',
    'description' => ts('Platform'),
    'api.required' => 1,
    'type' => CRM_Utils_Type::T_STRING,
    'options' => ['Android' => 'Android', 'IOS' => 'IOS']
  ];
  $params['is_active'] = [
    'title' => 'Is active',
    'description' => 'Is active',
    'type' => CRM_Utils_Type::T_BOOLEAN
  ];
}