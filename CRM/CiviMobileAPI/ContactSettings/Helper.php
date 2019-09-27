<?php

class CRM_CiviMobileAPI_ContactSettings_Helper {

  /**
   * Get custom fields names and IDs
   *
   * @return array
   * @throws \CiviCRM_API3_Exception
   */
  public static function getCustomFieldsID() {
    $out = [];
    $customGroupID = civicrm_api3('CustomGroup', 'get', [
      'return' => "id",
      'name' => CRM_CiviMobileAPI_Install_Entity_CustomGroup::CONTACT_SETTINGS,
      'is_active' => 1,
    ]);

    if (!empty($customGroupID["values"])) {
      $customFeilds = civicrm_api3('CustomField', 'get', [
        'return' => [
          "id",
          "name",
        ],
        'custom_group_id' => CRM_CiviMobileAPI_Install_Entity_CustomGroup::CONTACT_SETTINGS,
        'is_active' => 1,
      ]);
      if (isset($customFeilds["values"])) {
        foreach ($customFeilds["values"] as $field) {
          $out[$field['name']] = $field['id'];
        }
      }
    }

    return $out;
  }

  /**
   * Deletes custom group
   *
   * @throws \CiviCRM_API3_Exception
   */
  public static function deleteGroup() {
    $customGroupID = civicrm_api3('CustomGroup', 'get', ['name' => CRM_CiviMobileAPI_Install_Entity_CustomGroup::CONTACT_SETTINGS]);
    if (isset($customGroupID["values"])) {
      foreach ($customGroupID["values"] as $group) {
        civicrm_api3('CustomGroup', 'delete', ['id' => $group['id']]);
      }
    }
  }
}
