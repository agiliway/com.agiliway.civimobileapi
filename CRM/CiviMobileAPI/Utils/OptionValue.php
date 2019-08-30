<?php

/**
 * Class provide OptionValue helper methods
 */
class CRM_CiviMobileAPI_Utils_OptionValue {

  /**
   * Get id OptionValue for custom groupId
   *
   * @param $optionGroupName
   * @param $optionValueName
   *
   * @return array|bool
   */
  public static function getId($optionGroupName, $optionValueName) {
    try {
      $optionValue = civicrm_api3('OptionValue', 'getsingle', [
        'sequential' => 1,
        'option_group_id' => $optionGroupName,
        'name' => $optionValueName
      ]);
    } catch (CiviCRM_API3_Exception $e) {
      return false;
    }

    return !empty($optionValue['id']) ? $optionValue['id'] : false;
  }

  /**
   * Gets OptionValues by OptionGroupId
   *
   * @param $optionGroupId
   * @param array $extraParams
   *
   * @return array|bool
   */
  public static function getGroupValues($optionGroupId, $extraParams = []) {
    if (empty($optionGroupId)) {
      return [];
    }

    $params =  [
      'sequential' => 1,
      'option_group_id' => $optionGroupId,
    ];

    try {
      $optionValue = civicrm_api3('OptionValue', 'get', array_merge($params, $extraParams));
    } catch (CiviCRM_API3_Exception $e) {
      return [];
    }

    return !empty($optionValue['values']) ? $optionValue['values'] : [];
  }

}
