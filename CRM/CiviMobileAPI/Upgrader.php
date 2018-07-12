<?php
use CRM_CiviMobileAPI_ExtensionUtil as E;

/**
 * Collection of upgrade steps.
 */
class CRM_CiviMobileAPI_Upgrader extends CRM_CiviMobileAPI_Upgrader_Base {

  /**
   * Example: Run an external SQL script when the module is uninstalled.
   */
  public function uninstall() {
    CRM_CiviMobileAPI_PushNotification_Helper::deleteGroup();
  }

  /**
   * Example: Run a simple query when a module is enabled.
   */
  public function enable() {
    CRM_CiviMobileAPI_PushNotification_Helper::setActive(1);
    CRM_CiviMobileAPI_PushNotification_Helper::setActiveGroup(1);
  }

  /**
   * Example: Run a simple query when a module is disabled.
   */
  public function disable() {
    CRM_CiviMobileAPI_PushNotification_Helper::setActive(0);
    CRM_CiviMobileAPI_PushNotification_Helper::setActiveGroup(0);
  }

}
