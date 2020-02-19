<?php

use Joomla\CMS\Component\ComponentHelper;

class CRM_CiviMobileAPI_Utils_Cms_Registration_Joomla {

  /**
   * @return bool
   */
  public static function minPasswordLength() {
    $minimumLength = ComponentHelper::getParams('com_users')->get('minimum_length');
    return empty($minimumLength) ? false : $minimumLength;
  }

  /**
   * @return bool
   */
  public static function minPasswordIntegers() {
    $minimumPasswordIntegers = ComponentHelper::getParams('com_users')->get('minimum_integers');
    return empty($minimumPasswordIntegers) ? false : $minimumPasswordIntegers;
  }

  /**
   * @return bool
   */
  public static function minPasswordSymbols() {
    $minimumPasswordSymbols = ComponentHelper::getParams('com_users')->get('minimum_symbols');
    return empty($minimumPasswordSymbols) ? false : $minimumPasswordSymbols;
  }

  /**
   * @return bool
   */
  public static function minPasswordUpperCase() {
    $minimumPasswordUpperCase = ComponentHelper::getParams('com_users')->get('minimum_uppercase');
    return empty($minimumPasswordUpperCase) ? false : $minimumPasswordUpperCase;
  }

  /**
   * @return bool
   */
  public static function minPasswordLowerCase() {
    $minimumPasswordLowerCase = ComponentHelper::getParams('com_users')->get('minimum_lowercase');
    return empty($minimumPasswordLowerCase) ? false : $minimumPasswordLowerCase;
  }

  /**
   * @return bool
   */
  public static function maxPasswordLength() {
    return false;
  }

  /**
   * @return bool
   */
  public static function minUsernameLength() {
    return false;
  }

  /**
   * @return bool
   */
  public static function maxUsernameLength() {
    return false;
  }

}
