<?php

/**
 * Class provide PriceSet helper methods
 */
class CRM_CiviMobileAPI_Utils_PriceSet {

  /**
   * Gets price set fields by price set id
   *
   * @param $priceSetId
   * @return array|bool
   */
  public static function getFields($priceSetId) {
    try {
      $priceSetFields = civicrm_api3('PriceField', 'get', [
        'sequential' => 1,
        'price_set_id' => $priceSetId,
        'is_active' => 1
      ]);
    } catch (CiviCRM_API3_Exception $e) {
      return false;
    }

    return $priceSetFields;
  }

}
