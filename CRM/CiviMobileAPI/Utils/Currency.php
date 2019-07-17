<?php

/**
 * Class provide Currency helper methods
 */
class CRM_CiviMobileAPI_Utils_Currency {

  /**
   * Gets currency symbol by name
   *
   * @param $currencyName
   *
   * @return string
   */
  public static function getSymbolByName($currencyName) {
    if (empty($currencyName)) {
      return '';
    }

    $symbol = CRM_Core_DAO::getFieldValue('CRM_Financial_DAO_Currency', $currencyName, 'symbol', 'name');

    if (!empty($symbol)) {
      return $symbol;
    }

    return '';
  }

}
