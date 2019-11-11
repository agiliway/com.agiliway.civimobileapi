<?php

/**
 * Gets tag structure
 *
 * @param array $params
 *
 * @return array API result array
 * @throws \api_Exception
 */
function civicrm_api3_civi_mobile_tag_structure_get($params) {
  if (!CRM_CiviMobileAPI_Utils_Permission::isEnoughPermissionForGetTagStructure()) {
    throw new api_Exception('Permission required.', 'permission_required');
  }

  $result = (new CRM_CiviMobileAPI_Api_CiviMobileTagStructure_Get($params))->getResult();

  return civicrm_api3_create_success($result, $params);
}

/**
 * Specify Metadata for get action.
 *
 * @param array $params
 */
function _civicrm_api3_civi_mobile_tag_structure_get_spec(&$params) {
  $params['entity'] = [
    'title' => 'Entity',
    'description' => 'Available values: ("Contacts", "Activities", "Cases", "Attachements")',
    'api.required' => 1,
    'type' => CRM_Utils_Type::T_STRING,
  ];
  $params['entity_id'] = [
    'title' => 'Entity id',
    'description' => 'Entity id',
    'api.required' => 1,
    'type' => CRM_Utils_Type::T_INT,
  ];
  $params['is_tag_tree_show_in_two_level'] = [
    'title' => 'Is tag tree show in two level',
    'description' => 'Is tag tree show in two level',
    'api.default' => 1,
    'type' => CRM_Utils_Type::T_BOOLEAN,
  ];
}
