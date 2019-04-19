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
  $result = [];
  $listCustomFieldsID = CRM_CiviMobileAPI_ContactSettings_Helper::getCustomFieldsID();
  
  if (!empty($listCustomFieldsID)) {
    foreach ($listCustomFieldsID as $customFieldName => $customFieldID) {
      $fieldsReturn[] = 'custom_' . $customFieldID;
      $fieldsNames['custom_' . $customFieldID] = $customFieldName;
    }
    
    $contactSettings = civicrm_api3('Contact', 'get', [
      'return' => $fieldsReturn,
      'id' => $params['contact_id'],
    ]);

    $outValues = [];
    if ($contactSettings["is_error"] == 0) {
      $outValues['contact_id'] = $params['contact_id'];
      
      foreach ($contactSettings['values'][$params['contact_id']] as $settingName => $setting) {
        if (isset($fieldsNames[$settingName])) {
          $outValues[$fieldsNames[$settingName]] = $setting;
        }
      }
    }
    $result[] = $outValues;
  }
  
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
