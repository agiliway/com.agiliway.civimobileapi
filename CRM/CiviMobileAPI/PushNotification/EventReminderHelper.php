<?php

class CRM_CiviMobileAPI_PushNotification_EventReminderHelper {

  /**
   * API Entity
   */
  const API_ENTITY = 'PushNotificationEventReminder';
  
  public static function setEventReminderActive($active) {
    $jobs = civicrm_api3('Job', 'get', ['api_entity' => self::API_ENTITY]);
    foreach($jobs['values'] as $job) {
      civicrm_api3('Job', 'create', [
        'id' => $job['id'],
        'run_frequency' => $job['run_frequency'],
        'name' => $job['name'],
        'api_entity' => $job['api_entity'],
        'api_action' => $job['api_action'],
        'is_active' => $active,
      ]);
    }
  }
  
  public static function deleteEventReminder() {
    $jobs = civicrm_api3('Job', 'get', ['api_entity' => self::API_ENTITY]);
    foreach($jobs['values'] as $job) {
      civicrm_api3('Job', 'delete', ['id' => $job['id']]);
    }
  }
  
  public static function createEventReminder() {
    try {
      civicrm_api3('Job', 'getvalue', ['return' => 'id', 'api_entity' => self::API_ENTITY]);
    } catch (Exception $e) {
      $domainID = CRM_Core_Config::domainID();

      $params = [
        'name' => 'Notify all participants that event is going to start',
        'description' => 'Notify all participants that event is going to start',
        'api_entity' => self::API_ENTITY,
        'api_action' => 'send',
        'run_frequency' => 'Hourly',
        'domain_id' => $domainID,
        'is_active' => '1',
        'parameters' => '',
      ];

      CRM_Core_BAO_Job::create($params);
    }
  }

}
