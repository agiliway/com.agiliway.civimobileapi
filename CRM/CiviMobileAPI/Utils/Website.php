<?php

/**
 * Class provide Website helper methods
 */
class CRM_CiviMobileAPI_Utils_Website {

  /**
   * Gets domain name
   *
   * @return string
   */
  public static function getDomainName() {
    return $_SERVER['SERVER_NAME'];
  }

}
