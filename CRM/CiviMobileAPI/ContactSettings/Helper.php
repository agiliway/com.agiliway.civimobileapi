<?php

class CRM_CiviMobileAPI_ContactSettings_Helper {

  const CONTACT_SETTINGS = 'contact_settings';

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
      'name' => self::CONTACT_SETTINGS,
      'is_active' => 1,
    ]);

    if (!empty($customGroupID["values"])) {
      $customFeilds = civicrm_api3('CustomField', 'get', [
        'return' => [
          "id",
          "name",
        ],
        'custom_group_id' => self::CONTACT_SETTINGS,
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
   * Activates/disables custom fields
   *
   * @param $active
   *
   * @throws \CiviCRM_API3_Exception
   */
  public static function setActiveFields($active) {
    if (!self::isCustomGroupExist(self::CONTACT_SETTINGS)) {
      return;
    }

    $customFields = civicrm_api3('CustomField', 'get', ['custom_group_id' => self::CONTACT_SETTINGS]);
    if (isset($customFields["values"])) {
      foreach ($customFields["values"] as $field) {
        $field['is_active'] = $active;
        civicrm_api3('CustomField', 'create', $field);
      }
    }
  }

  /**
   * Activates/disables custom group
   *
   * @param $active
   *
   * @throws \CiviCRM_API3_Exception
   */
  public static function setActiveGroup($active) {
    $customGroupID = civicrm_api3('CustomGroup', 'get', ['name' => self::CONTACT_SETTINGS]);
    if (isset($customGroupID["values"])) {
      foreach ($customGroupID["values"] as $group) {
        $group['is_active'] = $active;
        civicrm_api3('CustomGroup', 'create', $group);
      }
    }
  }

  /**
   * Deletes custom group
   *
   * @throws \CiviCRM_API3_Exception
   */
  public static function deleteGroup() {
    $customGroupID = civicrm_api3('CustomGroup', 'get', ['name' => self::CONTACT_SETTINGS]);
    if (isset($customGroupID["values"])) {
      foreach ($customGroupID["values"] as $group) {
        civicrm_api3('CustomGroup', 'delete', ['id' => $group['id']]);
      }
    }
  }

  /**
   * Checks if custom group exists
   *
   * @param $customGroupName
   *
   * @return bool
   * @throws \CiviCRM_API3_Exception
   */
  private static function isCustomGroupExist($customGroupName) {
    $result = civicrm_api3('CustomGroup', 'getcount', [
      'name' => $customGroupName,
    ]);

    return $result == 1 ? TRUE : FALSE;
  }

}
