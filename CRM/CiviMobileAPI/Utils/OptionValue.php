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
   * @return array|bool
   */
  public static function getGroupValues($optionGroupId) {
    if (empty($optionGroupId)) {
      return [];
    }

    try {
      $optionValue = civicrm_api3('OptionValue', 'get', [
        'sequential' => 1,
        'option_group_id' => $optionGroupId,
      ]);
    } catch (CiviCRM_API3_Exception $e) {
      return [];
    }

    return !empty($optionValue['values']) ? $optionValue['values'] : [];
  }

}
