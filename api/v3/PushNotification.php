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

/**
 * Returns all push tokens by user
 *
 * @param array $params
 *
 * @return mixed
 */
function civicrm_api3_push_notification_get_by_user($params) {
  if (!CRM_Core_Permission::check('administer CiviCRM')) {
    throw new api_Exception('Permission required.', 'permission_required');
  }

  $pushNotifications = CRM_CiviMobileAPI_BAO_PushNotification::getAll(['contact_id' => $params['contact_id']]);

  return civicrm_api3_create_success($pushNotifications);
}

/**
 * Adjust Metadata for get action
 *
 * The metadata is used for setting defaults, documentation & validation
 * @param array $params array or parameters determined by getfields
 */
function _civicrm_api3_push_notification_get_by_user_spec(&$params) {
  $params['contact_id'] = [
    'title' => 'Contact ID',
    'description' => ts('Contact ID'),
    'api.required' => 1,
    'type' => CRM_Utils_Type::T_INT
  ];
}

/**
 * Deletes all push tokens
 *
 * @param array $params
 *
 * @return mixed
 */
function civicrm_api3_push_notification_delete_by_user($params) {
  if (!CRM_Core_Permission::check('administer CiviCRM')) {
    throw new api_Exception('Permission required.', 'permission_required');
  }

  $contact = new CRM_Contact_BAO_Contact();
  $contact->id = $params['contact_id'];
  $contactExistence = $contact->find(TRUE);
  if (empty($contactExistence)) {
    throw new api_Exception('Contact(id=' . $params['contact_id'] . ') does not exist.', 'contact_does_not_exist');
  }

  $pushNotifications = CRM_CiviMobileAPI_BAO_PushNotification::getAll(['contact_id' => $params['contact_id']]);
  foreach ($pushNotifications as $pushNotification) {
    CRM_CiviMobileAPI_BAO_PushNotification::del($pushNotification['id']);
  }

  return civicrm_api3_create_success([['message' => 'All push tokens for contact(id=' . $params['contact_id'] . ') is deleted!']]);
}

/**
 * Adjust Metadata for get action
 *
 * The metadata is used for setting defaults, documentation & validation
 * @param array $params array or parameters determined by getfields
 */
function _civicrm_api3_push_notification_delete_by_user_spec(&$params) {
  $params['contact_id'] = [
    'title' => 'Contact ID',
    'description' => ts('Contact ID'),
    'api.required' => 1,
    'type' => CRM_Utils_Type::T_INT
  ];
}
