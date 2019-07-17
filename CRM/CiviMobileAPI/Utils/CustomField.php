<?php

class CRM_CiviMobileAPI_Utils_CustomField {

  /**
   * Gets Custom Field id by Custom Group name and Custom Field name
   *
   * @param $customGroupName
   * @param $customFieldName
   *
   * @return bool|int
   */
  public static function getId($customGroupName, $customFieldName) {
    try {
      $customGroupId = civicrm_api3('CustomField', 'getvalue', [
        'return' => "id",
        'name' => $customFieldName,
        'custom_group_id' => $customGroupName,
      ]);

    } catch (CiviCRM_API3_Exception $e) {
      return false;
    }

    return (int) $customGroupId;
  }

}
