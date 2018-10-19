<?php

/**
 * Collection of upgrade steps.
 */
class CRM_CiviMobileAPI_Upgrader extends CRM_CiviMobileAPI_Upgrader_Base {

  public function upgrade_0001() {
    $this->ctx->log->info('Applying update 0001');
    CRM_CiviMobileAPI_PushNotification_Helper::deleteCustomGroup("contact_push_notification");

    return TRUE;
  }

  public function upgrade_0002() {
    try {
      $this->executeSqlFile('sql/auto_install.sql');
      CRM_CiviMobileAPI_PushNotification_EventReminderHelper::createEventReminder();
      return TRUE;
    } catch (Exception $e) {
      return FALSE;
    }
  }

  /**
   * Installs scheduled job
   */
  public function install() {
    CRM_CiviMobileAPI_PushNotification_EventReminderHelper::createEventReminder();
  }
  /**
   * Uninstalls scheduled job
   */
  public function uninstall() {
    CRM_CiviMobileAPI_PushNotification_EventReminderHelper::deleteEventReminder();
    $this->uninstallPushNotificationCustomGroup();
  }
  
  /**
   * Run a simple query when a module is enabled.
   */
  public function enable() {
    CRM_CiviMobileAPI_PushNotification_EventReminderHelper::setEventReminderActive(1);
  }

  /**
   * Run a simple query when a module is disabled.
   */
  public function disable() {
    CRM_CiviMobileAPI_PushNotification_EventReminderHelper::setEventReminderActive(0);
  }

  /**
   * Deletes 'push notification' custom group
   */
  private function uninstallPushNotificationCustomGroup() {
    $pushNotificationCustomGroupId = civicrm_api3('CustomGroup', 'get', [
      'return' => "id",
      'name' => "contact_push_notification",
    ]);

    if(isset($pushNotificationCustomGroupId['values']) && !empty($pushNotificationCustomGroupId['values'])){
      civicrm_api3('CustomGroup', 'delete', [
        'id' => $pushNotificationCustomGroupId,
      ]);
    }
  }

}