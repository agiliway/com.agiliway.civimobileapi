<?php

class CRM_CiviMobileAPI_Install_Install {

  /**
   * Installs requirements for extension
   */
  public static function run() {
    (new CRM_CiviMobileAPI_Install_Entity_CustomGroup())->install();
    (new CRM_CiviMobileAPI_Install_Entity_CustomField())->install();
    (new CRM_CiviMobileAPI_Install_Entity_UpdateMessageTemplate())->install();
    (new CRM_CiviMobileAPI_Install_Entity_Job())->install();
  }

  /**
   * Disables extension's Entities
   */
  public static function disable() {
    (new CRM_CiviMobileAPI_Install_Entity_CustomGroup())->disableAll();
    (new CRM_CiviMobileAPI_Install_Entity_Job())->disableAll();
  }

  /**
   * Enables extension's Entities
   */
  public static function enable() {
    (new CRM_CiviMobileAPI_Install_Entity_CustomGroup())->enableAll();
    (new CRM_CiviMobileAPI_Install_Entity_Job())->enableAll();
  }

}
