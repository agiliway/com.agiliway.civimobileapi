<?php

class CRM_CiviMobileAPI_Settings_Calendar_CiviCalendar extends CRM_CiviMobileAPI_Settings_Calendar_Base {

  /**
   * Get settings prefix name
   *
   * @return string
   */
  public static function getPrefix() {
    return 'civicalendar_';
  }

  /**
   * Get filter of valid settings
   *
   * @return array
   */
  public static function getFilter() {
    return ['group' => 'civicalendar'];
  }

}
