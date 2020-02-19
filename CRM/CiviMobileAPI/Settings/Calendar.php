<?php

class CRM_CiviMobileAPI_Settings_Calendar {

  /**
   * Contact Id can be integer or array
   *
   * @var array
   */
  private static $isCivimobileUseCiviCalendarSettings = null;

  /**
   * @return bool
   */
  private static function isCivimobileUseCiviCalendarSettings() {
    if (self::$isCivimobileUseCiviCalendarSettings === null) {
      self::$isCivimobileUseCiviCalendarSettings = CRM_CiviMobileAPI_Utils_Calendar::isCivimobileUseCiviCalendarSettings();
    }

    return self::$isCivimobileUseCiviCalendarSettings;
  }

  /**
   * @return mixed
   */
  public static function getHidePastEvents() {
    if (self::isCivimobileUseCiviCalendarSettings()) {
      return CRM_CiviMobileAPI_Settings_Calendar_CiviCalendar::getSingle('hide_past_events');
    } else {
      return CRM_CiviMobileAPI_Settings_Calendar_CiviMobile::getSingle('hide_past_events');
    }
  }

  /**
   * @return mixed
   */
  public static function getActivityTypes() {
    if (self::isCivimobileUseCiviCalendarSettings()) {
      return CRM_CiviMobileAPI_Settings_Calendar_CiviCalendar::getSingle('activity_types');
    } else {
      return CRM_CiviMobileAPI_Settings_Calendar_CiviMobile::getSingle('activity_types');
    }
  }

  /**
   * @return mixed
   */
  public static function getEventTypes() {
    if (self::isCivimobileUseCiviCalendarSettings()) {
      return CRM_CiviMobileAPI_Settings_Calendar_CiviCalendar::getSingle('event_types');
    } else {
      return CRM_CiviMobileAPI_Settings_Calendar_CiviMobile::getSingle('event_types');
    }
  }

  /**
   * @return mixed
   */
  public static function getCaseTypes() {
    if (self::isCivimobileUseCiviCalendarSettings()) {
      return CRM_CiviMobileAPI_Settings_Calendar_CiviCalendar::getSingle('case_types');
    } else {
      return CRM_CiviMobileAPI_Settings_Calendar_CiviMobile::getSingle('case_types');
    }
  }

  /**
   * Set values in setting 'civimobileapi_calendar_synchronize_with_civicalendar
   *
   * @param $value
   */
  public static function setCalendarIsAllowToUseCiviCalendarSettings($value) {
    Civi::settings()->set('civimobileapi_calendar_synchronize_with_civicalendar', (bool) $value);
  }

}
