<?php

class CRM_CiviMobileAPI_PushNotification_Helper {

  const PUSH_NOTIFICATION = 'contact_push_notification';

  public static function getCustomFieldsID() {
    $out = [];
    $customGroupID = civicrm_api3('CustomGroup', 'get', ['return' => "id", 'name' => self::PUSH_NOTIFICATION]);
    if(!empty($customGroupID["values"])){
      $result = civicrm_api3('CustomField', 'get', ['return' => ["id", "name"], 'custom_group_id' => self::PUSH_NOTIFICATION]);
      if (!empty($result["values"])) {
        foreach ($result["values"] as $field) {
          $out[$field['name']] = $field['id'];
        }
      }
    }
    
    return $out;
  }

  public static function setActive($active = 0) {
    $out = [];
    foreach (self::getCustomFieldsID() as $name => $id) {
      $result_data = civicrm_api3('CustomField', 'getvalue', ['return' => "data_type", 'id' => $id]);
      $result_html = civicrm_api3('CustomField', 'getvalue', ['return' => "html_type", 'id' => $id]);
      $out[$id] = [
        'id' => $id,
        'html_type' => $result_html,
        'data_type' => $result_data,
        'is_active' => $active
      ];
    }
    foreach ($out as $field) {
      civicrm_api3('CustomField', 'create', $field);
    }
  }

  public static function setActiveGroup($active = 0) {
    $customGroupID = civicrm_api3('CustomGroup', 'get', ['return' => "id", 'name' => self::PUSH_NOTIFICATION]);
    if(!empty($customGroupID["values"])){
      $id = array_shift($customGroupID['values'])['id'];
      civicrm_api3('CustomGroup', 'create', ['id' => $id, 'is_active' => $active, 'extends' => "Contact"]);
    }
  }

  public static function deleteGroup() {
    $customGroupID = civicrm_api3('CustomGroup', 'get', ['return' => "id", 'name' => self::PUSH_NOTIFICATION]);
    if(!empty($customGroupID["values"])){
      $id = array_shift($customGroupID['values'])['id'];
      civicrm_api3('CustomGroup', 'delete', ['id' => $id]);
    }
  }
}
