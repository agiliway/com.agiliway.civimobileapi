<?php

class CRM_CiviMobileAPI_Utils_CustomGroup {

  /**
   * Deletes custom group by name
   *
   * @param $customGroupName
   */
  public static function delete($customGroupName) {
    $customGroupID = civicrm_api3('CustomGroup', 'get', ['name' => $customGroupName]);
    if (!empty($customGroupID["values"])) {
      foreach ($customGroupID["values"] as $group) {
        civicrm_api3('CustomGroup', 'delete', ['id' => $group['id']]);
      }
    }
  }

}

