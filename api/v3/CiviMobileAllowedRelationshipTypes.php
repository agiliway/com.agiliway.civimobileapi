<?php

/**
 * Get get list of available relationship types based on the contact id
 *
 * @param array $params
 * @deprecated will be deleted in version 5, use 'CiviMobileAllowedExtendedRelationshipTypes' api
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
 * @deprecated will be deleted in version 5, use CiviMobileAllowedExtendedRelationshipTypes api
 */
function _civicrm_api3_civi_mobile_allowed_relationship_types_get_spec(&$params) {
  $params['contact_id'] = [
    'title' => 'Contact ID',
    'description' => ts('Contact ID'),
    'api.required' => 1,
    'type' => CRM_Utils_Type::T_INT,
  ];
}

/**
 * Notify caller of deprecated function.
 *
 * @deprecated will be deleted in version 5, use 'CiviMobileAllowedExtendedRelationshipTypes' api
 *
 * @return string
 */
function _civicrm_api3_civi_mobile_allowed_relationship_types_deprecation() {
  return 'The \'CiviMobileAllowedRelationshipTypes\' will be deleted in version 5, please use \'CiviMobileAllowedExtendedRelationshipTypes\' api.';
}
