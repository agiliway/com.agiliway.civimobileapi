<?php

class CRM_CiviMobileAPI_Utils_Calendar {

  /**
   * Is "com.agiliway.civicalendar" installed and options use in CiviMobile
   *
   * @return bool
   */
  public static function isCivimobileUseCiviCalendarSettings() {
    return static::isCiviCalendarEnable() && static::isActivateCiviCalendarSettings();
  }

  /**
   * Is checked 'synchronize_with_civicalendar' setting option in CiviMobile
   *
   * @return bool
   */
  public static function isActivateCiviCalendarSettings() {
    try {
      $civiCalendarSetting = civicrm_api3('Setting', 'getsingle', [
        'return' => CRM_CiviMobileAPI_Settings_Calendar_CiviMobile::getPrefix() . 'synchronize_with_civicalendar',
      ]);
    } catch (Exception $e) {
      return FALSE;
    }

    if ($civiCalendarSetting[CRM_CiviMobileAPI_Settings_Calendar_CiviMobile::getPrefix() . 'synchronize_with_civicalendar'] == 1) {
      return TRUE;
    }

    return FALSE;
  }

  /**
   * Is "com.agiliway.civicalendar" installed
   *
   * @return bool
   */
  public static function isCiviCalendarEnable() {
    try {
      $extensionStatus = civicrm_api3('Extension', 'getsingle', [
        'return' => "status",
        'full_name' => "com.agiliway.civicalendar",
      ]);
    } catch (Exception $e) {
      return FALSE;
    }

    if ($extensionStatus['status'] == 'installed') {
      return TRUE;
    }

    return FALSE;
  }

  /**
   * Checks if the СiviCalendar version and settings format is compatible with a CiviMobileApi
   *
   * @return bool
   */
  public static function isCiviCalendarCompatible() {
    $minimalMajorVersion = 3.4;
    try {
      $calendarVersion = civicrm_api3('Extension', 'getsingle', [
        'return' => ["version"],
        'full_name' => "com.agiliway.civicalendar",
      ])['version'];
    } catch (Exception $e) {
      return FALSE;
    }

    if (!(floatval($calendarVersion) >= $minimalMajorVersion)) {
      return FALSE;
    }

    $settings = civicrm_api3('Setting', 'getsingle');
    $requiredSettings = [
      'civicalendar_activity_types',
      'civicalendar_event_types',
      'civicalendar_case_types',
      'civicalendar_hide_past_events',
    ];

    foreach ($requiredSettings as $settingName) {
      if (!isset($settings[$settingName])) {
        return FALSE;
      }
    }

    return TRUE;
  }

}
