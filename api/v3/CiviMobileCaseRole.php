<?php

/**
 * Get get list of available case roles for case based on case type
 *
 * @param array $params
 *
 * @return array
 */
function civicrm_api3_civi_mobile_case_role_get($params) {
  $caseRoleManager = new CRM_CiviMobileAPI_Utils_CaseRole($params['case_id'], $params['contact_id']);
  $listOfRolesForCurrentCase = $caseRoleManager->getListOfRolesForCurrentCase();
  $convertedListOfRolesForCurrentCase = $caseRoleManager->convertListOfRoles($listOfRolesForCurrentCase);

  return civicrm_api3_create_success($convertedListOfRolesForCurrentCase);
}

/**
 * Adjust Metadata for get action
 *
 * The metadata is used for setting defaults, documentation & validation
 * @param array $params array or parameters determined by getfields
 */
function _civicrm_api3_civi_mobile_case_role_get_spec(&$params) {
  $params['case_id'] = [
    'title' => 'Case ID',
    'description' => ts('Case ID'),
    'api.required' => 1,
    'type' => CRM_Utils_Type::T_INT,
  ];
  $params['contact_id'] = [
    'title' => 'Contact ID',
    'description' => ts('Contact ID'),
    'api.required' => 1,
    'type' => CRM_Utils_Type::T_INT,
  ];
}
