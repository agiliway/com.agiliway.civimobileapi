<?php

class CRM_CiviMobileAPI_Utils_Cms_Registration {

  const DEFAULT_MIN_PASSWORD_LENGTH = 8;
  const DEFAULT_MAX_PASSWORD_LENGTH = 64;
  const DEFAULT_MIN_USERNAME_LENGTH = 3;
  const DEFAULT_MAX_USERNAME_LENGTH = 60;
  const DEFAULT_MIN_PASSWORD_INTEGERS = 0;
  const DEFAULT_MIN_PASSWORD_SYMBOLS = 0;
  const DEFAULT_MIN_PASSWORD_UPPER_CASE = 0;
  const DEFAULT_MIN_PASSWORD_LOWER_CASE = 0;
  const DEFAULT_MAX_FIRST_NAME_LENGTH = 64;
  const DEFAULT_MAX_LAST_NAME_LENGTH = 64;

  /**
   * @return string
   */
  public static function minPasswordLength() {
    switch (CRM_CiviMobileAPI_Utils_CmsUser::getInstance()->getSystem()) {
      case CRM_CiviMobileAPI_Utils_CmsUser::CMS_DRUPAL7:
      case CRM_CiviMobileAPI_Utils_CmsUser::CMS_DRUPAL6:
        $minPasswordLength = CRM_CiviMobileAPI_Utils_Cms_Registration_Drupal::minPasswordLength();
        break;
      case CRM_CiviMobileAPI_Utils_CmsUser::CMS_WORDPRESS:
        $minPasswordLength = CRM_CiviMobileAPI_Utils_Cms_Registration_Wordpress::minPasswordLength();
        break;
      case CRM_CiviMobileAPI_Utils_CmsUser::CMS_JOOMLA:
        $minPasswordLength = CRM_CiviMobileAPI_Utils_Cms_Registration_Joomla::minPasswordLength();
        break;
      default:
        return self::DEFAULT_MIN_PASSWORD_LENGTH;
    }

    return ($minPasswordLength !== false) ? $minPasswordLength : self::DEFAULT_MIN_PASSWORD_LENGTH;
  }

  /**
   * @return string
   */
  public static function maxPasswordLength() {
    switch (CRM_CiviMobileAPI_Utils_CmsUser::getInstance()->getSystem()) {
      case CRM_CiviMobileAPI_Utils_CmsUser::CMS_DRUPAL7:
      case CRM_CiviMobileAPI_Utils_CmsUser::CMS_DRUPAL6:
        $maxPasswordLength = CRM_CiviMobileAPI_Utils_Cms_Registration_Drupal::maxPasswordLength();
        break;
      case CRM_CiviMobileAPI_Utils_CmsUser::CMS_WORDPRESS:
        $maxPasswordLength = CRM_CiviMobileAPI_Utils_Cms_Registration_Wordpress::maxPasswordLength();
        break;
      case CRM_CiviMobileAPI_Utils_CmsUser::CMS_JOOMLA:
        $maxPasswordLength = CRM_CiviMobileAPI_Utils_Cms_Registration_Joomla::maxPasswordLength();
        break;
      default:
        return self::DEFAULT_MAX_PASSWORD_LENGTH;
    }

    return ($maxPasswordLength !== false) ? $maxPasswordLength : self::DEFAULT_MAX_PASSWORD_LENGTH;
  }

  /**
   * @return string
   */
  public static function minUsernameLength() {
    switch (CRM_CiviMobileAPI_Utils_CmsUser::getInstance()->getSystem()) {
      case CRM_CiviMobileAPI_Utils_CmsUser::CMS_DRUPAL7:
      case CRM_CiviMobileAPI_Utils_CmsUser::CMS_DRUPAL6:
        $minUsernameLength = CRM_CiviMobileAPI_Utils_Cms_Registration_Drupal::minUsernameLength();
        break;
      case CRM_CiviMobileAPI_Utils_CmsUser::CMS_WORDPRESS:
        $minUsernameLength = CRM_CiviMobileAPI_Utils_Cms_Registration_Wordpress::minUsernameLength();
        break;
      case CRM_CiviMobileAPI_Utils_CmsUser::CMS_JOOMLA:
        $minUsernameLength = CRM_CiviMobileAPI_Utils_Cms_Registration_Joomla::minUsernameLength();
        break;
      default:
        return self::DEFAULT_MIN_USERNAME_LENGTH;
    }

    return ($minUsernameLength !== false) ? $minUsernameLength : self::DEFAULT_MIN_USERNAME_LENGTH;
  }

  /**
   * @return string
   */
  public static function maxUsernameLength() {
    switch (CRM_CiviMobileAPI_Utils_CmsUser::getInstance()->getSystem()) {
      case CRM_CiviMobileAPI_Utils_CmsUser::CMS_DRUPAL7:
      case CRM_CiviMobileAPI_Utils_CmsUser::CMS_DRUPAL6:
        $maxUsernameLength = CRM_CiviMobileAPI_Utils_Cms_Registration_Drupal::maxUsernameLength();
        break;
      case CRM_CiviMobileAPI_Utils_CmsUser::CMS_WORDPRESS:
        $maxUsernameLength = CRM_CiviMobileAPI_Utils_Cms_Registration_Wordpress::maxUsernameLength();
        break;
      case CRM_CiviMobileAPI_Utils_CmsUser::CMS_JOOMLA:
        $maxUsernameLength = CRM_CiviMobileAPI_Utils_Cms_Registration_Joomla::maxUsernameLength();
        break;
      default:
        return self::DEFAULT_MAX_USERNAME_LENGTH;
    }

    return ($maxUsernameLength !== false) ? $maxUsernameLength : self::DEFAULT_MAX_USERNAME_LENGTH;
  }

  /**
   * @return bool
   */
  public static function minPasswordIntegers() {
    switch (CRM_CiviMobileAPI_Utils_CmsUser::getInstance()->getSystem()) {
      case CRM_CiviMobileAPI_Utils_CmsUser::CMS_DRUPAL7:
      case CRM_CiviMobileAPI_Utils_CmsUser::CMS_DRUPAL6:
        $minPasswordIntegers = CRM_CiviMobileAPI_Utils_Cms_Registration_Drupal::minPasswordIntegers();
        break;
      case CRM_CiviMobileAPI_Utils_CmsUser::CMS_WORDPRESS:
        $minPasswordIntegers = CRM_CiviMobileAPI_Utils_Cms_Registration_Wordpress::minPasswordIntegers();
        break;
      case CRM_CiviMobileAPI_Utils_CmsUser::CMS_JOOMLA:
        $minPasswordIntegers = CRM_CiviMobileAPI_Utils_Cms_Registration_Joomla::minPasswordIntegers();
        break;
      default:
        return self::DEFAULT_MIN_PASSWORD_INTEGERS;
    }

    return ($minPasswordIntegers !== false) ? $minPasswordIntegers : self::DEFAULT_MIN_PASSWORD_INTEGERS;
  }

  /**
   * @return bool
   */
  public static function minPasswordSymbols() {
    switch (CRM_CiviMobileAPI_Utils_CmsUser::getInstance()->getSystem()) {
      case CRM_CiviMobileAPI_Utils_CmsUser::CMS_DRUPAL7:
      case CRM_CiviMobileAPI_Utils_CmsUser::CMS_DRUPAL6:
      $minPasswordSymbols = CRM_CiviMobileAPI_Utils_Cms_Registration_Drupal::minPasswordSymbols();
        break;
      case CRM_CiviMobileAPI_Utils_CmsUser::CMS_WORDPRESS:
        $minPasswordSymbols = CRM_CiviMobileAPI_Utils_Cms_Registration_Wordpress::minPasswordSymbols();
        break;
      case CRM_CiviMobileAPI_Utils_CmsUser::CMS_JOOMLA:
        $minPasswordSymbols = CRM_CiviMobileAPI_Utils_Cms_Registration_Joomla::minPasswordSymbols();
        break;
      default:
        return self::DEFAULT_MIN_PASSWORD_SYMBOLS;
    }

    return ($minPasswordSymbols !== false) ? $minPasswordSymbols : self::DEFAULT_MIN_PASSWORD_SYMBOLS;
  }

  /**
   * @return bool
   */
  public static function minPasswordUpperCase() {
    switch (CRM_CiviMobileAPI_Utils_CmsUser::getInstance()->getSystem()) {
      case CRM_CiviMobileAPI_Utils_CmsUser::CMS_DRUPAL7:
      case CRM_CiviMobileAPI_Utils_CmsUser::CMS_DRUPAL6:
        $minPasswordUpperCase = CRM_CiviMobileAPI_Utils_Cms_Registration_Drupal::minPasswordUpperCase();
        break;
      case CRM_CiviMobileAPI_Utils_CmsUser::CMS_WORDPRESS:
        $minPasswordUpperCase = CRM_CiviMobileAPI_Utils_Cms_Registration_Wordpress::minPasswordUpperCase();
        break;
      case CRM_CiviMobileAPI_Utils_CmsUser::CMS_JOOMLA:
        $minPasswordUpperCase = CRM_CiviMobileAPI_Utils_Cms_Registration_Joomla::minPasswordUpperCase();
        break;
      default:
        return self::DEFAULT_MIN_PASSWORD_UPPER_CASE;
    }

    return ($minPasswordUpperCase !== false) ? $minPasswordUpperCase : self::DEFAULT_MIN_PASSWORD_UPPER_CASE;
  }

  /**
   * @return bool
   */
  public static function minPasswordLowerCase() {
    switch (CRM_CiviMobileAPI_Utils_CmsUser::getInstance()->getSystem()) {
      case CRM_CiviMobileAPI_Utils_CmsUser::CMS_DRUPAL7:
      case CRM_CiviMobileAPI_Utils_CmsUser::CMS_DRUPAL6:
      $minPasswordLowerCase = CRM_CiviMobileAPI_Utils_Cms_Registration_Drupal::minPasswordLowerCase();
        break;
      case CRM_CiviMobileAPI_Utils_CmsUser::CMS_WORDPRESS:
        $minPasswordLowerCase = CRM_CiviMobileAPI_Utils_Cms_Registration_Wordpress::minPasswordLowerCase();
        break;
      case CRM_CiviMobileAPI_Utils_CmsUser::CMS_JOOMLA:
        $minPasswordLowerCase = CRM_CiviMobileAPI_Utils_Cms_Registration_Joomla::minPasswordLowerCase();
        break;
      default:
        return self::DEFAULT_MIN_PASSWORD_LOWER_CASE;
    }

    return ($minPasswordLowerCase !== false) ? $minPasswordLowerCase : self::DEFAULT_MIN_PASSWORD_LOWER_CASE;
  }

  /**
   * @return bool
   */
  public static function maxFirstNameLength() {
    $contactFields = CRM_Contact_BAO_Contact::fields();
    if (!isset($contactFields['first_name']['maxlength'])) {
      return self::DEFAULT_MAX_FIRST_NAME_LENGTH;
    }

    return (int) $contactFields['first_name']['maxlength'];
  }

  /**
   * @return bool
   */
  public static function maxLastNameLength() {
    $contactFields = CRM_Contact_BAO_Contact::fields();
    if (!isset($contactFields['last_name']['maxlength'])) {
      return self::DEFAULT_MAX_LAST_NAME_LENGTH;
    }

    return (int) $contactFields['last_name']['maxlength'];
  }

}
