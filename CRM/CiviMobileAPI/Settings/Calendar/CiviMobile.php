<?php

class CRM_CiviMobileAPI_Settings_Calendar_CiviMobile extends CRM_CiviMobileAPI_Settings_Calendar_Base {

  /**
   * Get settings prefix name
   *
   * @return string
   */
  public static function getPrefix() {
    return 'civimobileapi_calendar_';
  }

  /**
   * Get filter of valid settings
   *
   * @return array
   */
  public static function getFilter() {
    return ['group' => 'civimobileapi_calendar'];
  }

}
