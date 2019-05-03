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

  public function upgrade_0003() {
    $files = glob($this->extensionDir . '/xml/settings_install.xml');
    if (is_array($files)) {
      foreach ($files as $file) {
        $this->executeCustomDataFileByAbsPath($file);
      }

      return TRUE;
    } else {
      return FALSE;
    }
  }

  public function upgrade_0004() {
    try {
      $this->executeSqlFile('sql/notification_messages_install.sql');
      return TRUE;
    } catch (Exception $e) {
      return FALSE;
    }
  }

  public function upgrade_0005() {
    try {
      $this->executeSql('ALTER TABLE civicrm_contact_push_notification_messages ADD invoke_contact_id INT(10) UNSIGNED NULL');
      return TRUE;
    } catch (Exception $e) {
      return FALSE;
    }
  }

  public function upgrade_0006() {
    try {
      $this->executeSql('ALTER TABLE civicrm_contact_push_notification_messages ADD message_title varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL');
      return TRUE;
    } catch (Exception $e) {
      return FALSE;
    }
  }

  public function upgrade_0011() {
    $this->ctx->log->info('Applying update 0011');
    $this->deleteOldMenu();

    return TRUE;
  }

  /**
   * Installs scheduled job
   */
  public function install() {
    CRM_CiviMobileAPI_PushNotification_EventReminderHelper::createEventReminder();
    CRM_CiviMobileAPI_Utils_Version::updateSchemaVersion();
    Civi::settings()->set('civimobile_auto_update', 1);
  }

  /**
   * Uninstalls scheduled job
   */
  public function uninstall() {
    CRM_CiviMobileAPI_PushNotification_EventReminderHelper::deleteEventReminder();
    $this->uninstallPushNotificationCustomGroup();
    CRM_CiviMobileAPI_ContactSettings_Helper::deleteGroup();
  }
  
  /**
   * Run a simple query when a module is enabled.
   */
  public function enable() {
    CRM_CiviMobileAPI_PushNotification_EventReminderHelper::setEventReminderActive(1);
    CRM_CiviMobileAPI_ContactSettings_Helper::setActiveFields(1);
    CRM_CiviMobileAPI_ContactSettings_Helper::setActiveGroup(1);
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

    if (isset($pushNotificationCustomGroupId['values']) && !empty($pushNotificationCustomGroupId['values'])) {
      civicrm_api3('CustomGroup', 'delete', [
        'id' => $pushNotificationCustomGroupId,
      ]);
    }
  }

  /**
   * Deletes old menu
   */
  private function deleteOldMenu() {
    $value = ['name' => 'civimobile-settings'];
    CRM_Core_BAO_Navigation::retrieve($value, $navInfo);
    if (!empty($navInfo['id'])) {
      CRM_Core_BAO_Navigation::processDelete($navInfo['id']);
      CRM_Core_BAO_Navigation::resetNavigation();
    }
  }

}
