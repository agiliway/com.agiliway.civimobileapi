<?php

/**
 * Get get list of available activity types
 *
 * @param array $params
 *
 * @return array API result array
 */
function civicrm_api3_civi_mobile_allowed_activity_types_get($params) {
  $allowedActivityTypes = CRM_CiviMobileAPI_Utils_ActivityType::getContactActivityTypes($params);

  return civicrm_api3_create_success($allowedActivityTypes);
}

/**
 * Adjust Metadata for get action
 *
 * The metadata is used for setting defaults, documentation & validation
 * @param array $params array or parameters determined by getfields
 */
function _civicrm_api3_civi_mobile_allowed_activity_types_get_spec(&$params) {
  $params['contact_id'] = [
    'title' => 'Contact ID',
    'description' => ts('Contact ID'),
    'api.required' => 1,
    'type' => CRM_Utils_Type::T_INT,
  ];
  $params['sort'] = [
    'title' => 'Sort',
    'description' => ts('Sort filed (default "label")'),
    'api.required' => 0,
    'type' => CRM_Utils_Type::T_STRING,
  ];
  $params['limit'] = [
    'title' => 'Limit',
    'description' => ts('Limit (default unlimited)'),
    'api.required' => 0,
    'type' => CRM_Utils_Type::T_INT,
  ];
}
