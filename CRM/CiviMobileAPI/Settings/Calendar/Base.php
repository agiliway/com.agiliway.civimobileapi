<?php

class CRM_CiviMobileAPI_Settings_Calendar_Base {

  /**
   * Get settings prefix name
   *
   * @return string
   */
  public static function getPrefix() {
    return '';
  }

  /**
   * Get filter of valid settings
   *
   * @return array
   */
  public static function getFilter() {
    return [];
  }

  /**
   * Get name of setting
   *
   * @param $name
   * @param bool $prefix
   *
   * @return mixed|string
   */
  public static function getName($name, $prefix = FALSE) {
    $prepareName = str_replace(static::getPrefix(), '', $name);

    if ($prefix) {
      $prepareName = static::getPrefix() . $prepareName;
    }

    return $prepareName;
  }
  
  /**
   * Get settings
   *
   * @param array $settings of settings (eg. array(username, password))
   *
   * @return array
   * @throws \CiviCRM_API3_Exception
   */
  public static function get($settings) {
    $domainID = CRM_Core_Config::domainID();
    $prefixedSettings = [];

    foreach ($settings as $name) {
      $prefixedSettings[] = static::getName($name, TRUE);
    }

    try {
      $settingsResult = civicrm_api3('setting', 'get', ['return' => $prefixedSettings]);
    } catch (CiviCRM_API3_Exception $e) {
      return [];
    }

    if (isset($settingsResult['values'][$domainID])) {
      foreach ($settingsResult['values'][$domainID] as $name => $value) {
        $nonPrefixedSettings[static::getName($name)] = $value;
      }

      return empty($nonPrefixedSettings) ? NULL : $nonPrefixedSettings;
    }

    return [];
  }

  /**
   * Get single settings
   *
   * @param $setting
   *
   * @return mixed
   */
  public static function getSingle($setting) {
    $domainID = CRM_Core_Config::domainID();

    try {
      $settingsResult = civicrm_api3('setting', 'get', ['return' => static::getName($setting, TRUE)]);
    } catch (CiviCRM_API3_Exception $e) {
      return '';
    }

    if (isset($settingsResult['values'][$domainID])) {
      foreach ($settingsResult['values'][$domainID] as $name => $value) {
        return $value;
      }
    }

    return '';
  }

}
