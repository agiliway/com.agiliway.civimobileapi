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
function civicrm_api3_civi_mobile_custom_fields_by_contacts_get($params) {
  _civicrm_api3_custom_fields_check_permission($params);

  $entity = 'Contact';
  $entityId = $params['contact_id'];
  $result = [];

  try {
    $customGroups = civicrm_api3('CustomGroup', 'get', [
      'sequential' => 1,
      'extends' => $entity,
      'is_active' => 1,
    ]);
  } catch (CiviCRM_API3_Exception $e) {
    throw new \API_Exception(ts("Something wrong with getting info for custom group: " . $e->getMessage()));
  }

  if (empty($customGroups['values'])) {
    return civicrm_api3_create_success($result);
  }

  foreach ($customGroups['values'] as $customGroup) {
    $result[$customGroup['name']] = [
      'custom_group_id'    => $customGroup['id'],
      'custom_group_name'  => $customGroup['name'],
      'custom_group_title' => $customGroup['title']
    ];

    try {
      $customFields = civicrm_api3('CustomField', 'get', [
        'sequential' => 1,
        'custom_group_id' => $customGroup['id'],
        'is_active' => 1,
      ]);
    } catch (CiviCRM_API3_Exception $e) {
      throw new \API_Exception(ts("Something wrong with getting info for custom field: " . $e->getMessage()));
    }

    if (empty($customFields['values'])) {
      continue;
    }
    $returnFields = [];
    foreach ($customFields['values'] as $customField) {
      $returnFields[] = "custom_" . CRM_CiviMobileAPI_Utils_CustomField::getId($customGroup['name'],  $customField['name']);
    }

    try {
      $contact = civicrm_api3($entity, 'getsingle', [
        'sequential' => 1,
        'id' => $entityId,
        'return' => $returnFields
      ]);
    } catch (CiviCRM_API3_Exception $e) {
      $contact = NULL;
    }

    foreach ($customFields['values'] as $customField) {
      $customFieldName = "custom_" . CRM_CiviMobileAPI_Utils_CustomField::getId($customGroup['name'],  $customField['name']);
      $availableValues = CRM_CiviMobileAPI_Utils_OptionValue::getGroupValues($customField['option_group_id']);

      if ($customField['html_type'] == 'Radio' && $customField['data_type'] == "Boolean") {
        $availableValues = ['0','1'];
      }

      $result[$customGroup['name']]['custom_fields'][] = [
        "cf_id" => $customField['id'],
        "cf_name" => $customField['name'],
        "cf_label" => $customField['label'],
        "cf_data_type" => $customField['data_type'],
        "cf_html_type"=> $customField['html_type'],
        "cf_checked_value"=> (!empty($contact[$customFieldName])) ? $contact[$customFieldName] : "0",
        "cf_available_values" => $availableValues
      ];
    }
  }

  return civicrm_api3_create_success($result);
}

/**
 * @param $params
 * @throws \Civi\API\Exception\UnauthorizedException
 */
function _civicrm_api3_custom_fields_check_permission($params) {
  if (!CRM_Core_Permission::check('access CiviCRM')) {
    throw new \Civi\API\Exception\UnauthorizedException('Permission denied.');
  }
}

/**
 * Specify Metadata for get action.
 *
 * @param array $params
 */
function _civicrm_api3_civi_mobile_custom_fields_by_contacts_get_spec(&$params) {
  $params['contact_id'] = [
    'title' => 'Contact id',
    'description' => ts('Contact id'),
    'type' => CRM_Utils_Type::T_INT,
    'api.required' => 1,
  ];
}
