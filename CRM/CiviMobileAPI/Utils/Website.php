<?php

/**
 * Class provide Website helper methods
 */
class CRM_CiviMobileAPI_Utils_Website {

  /**
   * Returns protocol 'https://' or 'http://'
   *
   * @return string
   */
  public static function getProtocol() {
    $protocol = 'http://';

    if (defined('CIVICRM_UF_BASEURL') && !empty(CIVICRM_UF_BASEURL)
      && !(stripos(CIVICRM_UF_BASEURL, 'https') === false)) {
      $protocol = 'https://';
    }

    return $protocol;
  }

}
