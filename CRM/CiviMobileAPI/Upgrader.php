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
      return TRUE;
    } catch (Exception $e) {
      return FALSE;
    }
  }

  public function upgrade_0003() {
    CRM_CiviMobileAPI_Install_Install::run();

    return TRUE;
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
    } catch (Exception $e) {}

    return TRUE;
  }

  public function upgrade_0006() {
    try {
      $this->executeSql('ALTER TABLE civicrm_contact_push_notification_messages ADD message_title varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL');
    } catch (Exception $e) {}

    return TRUE;
  }

  public function upgrade_0011() {
    $this->ctx->log->info('Applying update 0011');
    $this->deleteOldMenu();

    return TRUE;
  }

  public function upgrade_0012() {
    CRM_CiviMobileAPI_Install_Install::run();

    return TRUE;
  }

  public function upgrade_0013() {
    CRM_CiviMobileAPI_Install_Install::run();

    return TRUE;
  }

  public function upgrade_0014() {
    (new CRM_CiviMobileAPI_Install_Entity_ApplicationQrCode())->install();

    return TRUE;
  }

  public function upgrade_0015() {
    try {
      $this->executeSqlFile('sql/civimobile_event_payment_info_install.sql');
      $this->executeSql('ALTER TABLE civicrm_contact_push_notification_messages ADD data varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL');
      CRM_CiviMobileAPI_Utils_CustomGroup::delete(CRM_CiviMobileAPI_Install_Entity_CustomGroup::CONTACT_SETTINGS);
      CRM_Core_Invoke::rebuildMenuAndCaches(TRUE);
      return TRUE;
    } catch (Exception $e) {
      return FALSE;
    }
  }

  /**
   * Installs scheduled job
   *
   * @throws \Exception
   */
  public function install() {
    CRM_CiviMobileAPI_Install_Install::run();
    CRM_CiviMobileAPI_Settings_Calendar::setCalendarIsAllowToUseCiviCalendarSettings(
      CRM_CiviMobileAPI_Utils_Calendar::isCiviCalendarEnable()
      && CRM_CiviMobileAPI_Utils_Calendar::isCiviCalendarCompatible()
    );

    Civi::settings()->set('civimobile_auto_update', 1);
  }

  /**
   * Uninstalls scheduled job
   *
   * @throws \CiviCRM_API3_Exception
   */
  public function uninstall() {
    CRM_CiviMobileAPI_Install_Install::uninstall();
    $this->uninstallPushNotificationCustomGroup();
    CRM_CiviMobileAPI_Utils_CustomGroup::delete(CRM_CiviMobileAPI_Install_Entity_CustomGroup::CONTACT_SETTINGS);
  }

  /**
   * Run a simple query when a module is enabled.
   *
   * @throws \CiviCRM_API3_Exception
   */
  public function enable() {
    CRM_CiviMobileAPI_Install_Install::enable();
  }

  /**
   * Run a simple query when a module is disabled.
   *
   * @throws \CiviCRM_API3_Exception
   */
  public function disable() {
    CRM_CiviMobileAPI_Install_Install::disable();
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
