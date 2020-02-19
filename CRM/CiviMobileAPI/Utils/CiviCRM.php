<?php

class CRM_CiviMobileAPI_Utils_CiviCRM {

  /**
   * Gets enabled CiviCRM components
   *
   * @return array
   */
  public static function getEnabledComponents() {
    try {
      $enableComponents = civicrm_api3('Setting', 'get', [
        'sequential' => 1,
        'return' => ["enable_components"],
      ]);
    } catch (CiviCRM_API3_Exception $e) {
      return [];
    }

    return (!empty($enableComponents['values'][0]['enable_components'])) ? $enableComponents['values'][0]['enable_components'] : [];
  }

}
