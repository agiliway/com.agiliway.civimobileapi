<?php

/**
 * Gets custom field values by contact
 *
 * @param array $params
 *
 * @return array
 * @throws API_Exception
 * @throws \Civi\API\Exception\UnauthorizedException
 */
function civicrm_api3_civi_mobile_custom_fields_get($params) {
  if (!CRM_Core_Permission::check('access CiviCRM')) {
    throw new \Civi\API\Exception\UnauthorizedException('Permission denied.');
  }

  $result = (new CRM_CiviMobileAPI_Api_CiviMobileCustomFields_Get($params))->getResult();

  return civicrm_api3_create_success($result);
}

/**
 * Specify Metadata for get action.
 *
 * @param array $params
 */
function _civicrm_api3_civi_mobile_custom_fields_get_spec(&$params) {
  $params['entity'] = [
    'title' => 'Entity',
    'description' => ts('Entity') . implode(', ', CRM_CiviMobileAPI_Api_CiviMobileCustomFields_Get::getAvailableEntities()),
    'type' => CRM_Utils_Type::T_STRING,
    'api.required' => 1,
  ];
  $params['entity_id'] = [
    'title' => 'Entity id',
    'description' => ts('Entity id'),
    'type' => CRM_Utils_Type::T_INT,
    'api.required' => 1,
  ];
}
