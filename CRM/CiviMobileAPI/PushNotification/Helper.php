<?php

class CRM_CiviMobileAPI_PushNotification_Helper {

  const PUSH_NOTIFICATION = 'contact_push_notification';

  public static function deleteGroup() {
    $customGroupID = civicrm_api3('CustomGroup', 'get', ['return' => "id", 'name' => self::PUSH_NOTIFICATION]);
    if(!empty($customGroupID["values"])){
      $id = array_shift($customGroupID['values'])['id'];
      civicrm_api3('CustomGroup', 'delete', ['id' => $id]);
    }
  }
}
