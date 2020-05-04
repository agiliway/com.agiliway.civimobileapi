<?php

class CRM_CiviMobileAPI_Utils_CiviCRM {

  /**
   * Gets enabled CiviCRM components
   *
   * @return array
   */
  public static function getEnabledComponents() {
    $enableComponents = [];
    try {
      $result = civicrm_api3('Setting', 'get', [
        'sequential' => 1,
        'return' => ["enable_components"],
      ]);
    } catch (CiviCRM_API3_Exception $e) {
      return [];
    }

    if (isset($result['values'][0]['enable_components'])) {
      foreach ($result['values'][0]['enable_components'] as $component) {
        $enableComponents[] = $component;
      }
    }

    return $enableComponents;
  }

}
