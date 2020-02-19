<?php

/**
 * Gets settings for contact
 *
 * @param array $params
 *
 * @return array
 * @throws \CiviCRM_API3_Exception
 */
function civicrm_api3_contact_settings_get($params) {
  $result = (new CRM_CiviMobileAPI_Api_ContactSettings_Get($params))->getResult();
  
  return civicrm_api3_create_success($result, $params);
}

/**
 * Adjust Metadata for get action
 *
 * The metadata is used for setting defaults, documentation & validation
 * @param array $params array or parameters determined by getfields
 */
function _civicrm_api3_contact_settings_get_spec(&$params) {
  $params['contact_id'] = [
    'title' => 'Contact ID',
    'description' => ts('Contact ID'),
    'api.required' => 1,
    'type' => CRM_Utils_Type::T_INT
  ];
}
