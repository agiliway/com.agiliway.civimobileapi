<?php

/**
 * Get get list of available relationship types based on the contact id
 *
 * @param array $params
 *
 * @return array API result array
 */
function civicrm_api3_civi_mobile_allowed_relationship_types_get($params) {
  $allowedRelationshipTypes = CRM_Contact_BAO_Relationship::getContactRelationshipType($params['contact_id']);

  return civicrm_api3_create_success($allowedRelationshipTypes);
}

/**
 * Adjust Metadata for get action
 *
 * The metadata is used for setting defaults, documentation & validation
 * @param array $params array or parameters determined by getfields
 */
function _civicrm_api3_civi_mobile_allowed_relationship_types_get_spec(&$params) {
  $params['contact_id'] = [
    'title' => 'Contact ID',
    'description' => ts('Contact ID'),
    'api.required' => 1,
    'type' => CRM_Utils_Type::T_INT,
  ];
}
