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
  
  public function upgrade_0001() {
    $this->ctx->log->info('Applying update 0001');
    CRM_CiviMobileAPI_PushNotification_Helper::deleteGroup();

    return TRUE;
  }

}
