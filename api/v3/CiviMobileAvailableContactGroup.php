<?php

/**
 * Gets available ContactGroups in create select
 *
 * @param array $params
 *
 * @return array
 * @throws \api_Exception
 */
function civicrm_api3_civi_mobile_available_contact_group_get($params) {
  if (!CRM_CiviMobileAPI_Utils_Permission::isEnoughPermissionForGetAvailableContactGroup()) {
    throw new api_Exception('Permission required.', 'permission_required');
  }

  $result = (new CRM_CiviMobileAPI_Api_CiviMobileAvailableContactGroup_Get($params))->getResult();

  return civicrm_api3_create_success($result);
}

/**
 * Specify Metadata for get action.
 *
 * @param array $params
 */
function _civicrm_api3_civi_mobile_available_contact_group_get_spec(&$params) {
  $params['contact_id'] = [
    'title' => 'Contact ID',
    'description' => ts('Contact ID'),
    'api.required' => 1,
    'type' => CRM_Utils_Type::T_INT,
  ];

  $params['is_hidden'] = [
    'title' => 'Is group hidden?',
    'description' => ts('Is group hidden?'),
    'api.required' => 0,
    'type' => CRM_Utils_Type::T_BOOLEAN,
  ];
}
