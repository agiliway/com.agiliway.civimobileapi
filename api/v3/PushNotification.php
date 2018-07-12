<?php

/**
 * Save push tockens
 *
 * @param array $params
 */
function civicrm_api3_push_notification_create($params) {
  $listCustomFieldsID = CRM_CiviMobileAPI_PushNotification_Helper::getCustomFieldsID();
  if (!empty($listCustomFieldsID)) {
    $isContactCreated = civicrm_api3('Contact', 'getvalue', [
      'return' => 'custom_' . $listCustomFieldsID['token'],
      'id' => $params['contact_id']
    ]);

    $params['is_active'] = (isset($params['is_active'])) ? $params['is_active'] : 1;
    
    $paramsForInsert = [
      'contact_id' => $params['contact_id'],
      'custom_' . $listCustomFieldsID['token'] => $params['token'],
      'custom_' . $listCustomFieldsID['platform'] => $params['platform'],
      'custom_' . $listCustomFieldsID['dt_update'] => gmdate("Y-m-d H:i:s", time()),
      'custom_' . $listCustomFieldsID['is_active'] => $params['is_active']
    ];
    if (!$isContactCreated) {
      $paramsForInsert['custom_' . $listCustomFieldsID['dt_create']] = gmdate("Y-m-d H:i:s", time());
    }
    $result = civicrm_api3('Contact', 'create', $paramsForInsert);
  }
  
  return civicrm_api3_create_success($params);
}

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
