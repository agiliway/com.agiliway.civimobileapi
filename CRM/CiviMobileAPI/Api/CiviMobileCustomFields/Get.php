<?php

/**
 * Class handles CiviMobileCustomFields api
 */
class CRM_CiviMobileAPI_Api_CiviMobileCustomFields_Get extends CRM_CiviMobileAPI_Api_CiviMobileBase {

  /**
   * This sets if custom field don't have any value
   */
  const EMPTY_VALUE_SYMBOL = 'NULL_VALUE';

  /**
   * Entity map
   */
  private static $entityMap = [
    'Individual' => [
      'find_for' => ['Contact','Individual'],
    ],
    'Organization' => [
      'find_for' => ['Contact','Organization'],
    ],
    'Household' => [
      'find_for' => ['Contact','Household'],
    ],
  ];

  /**
   * Returns validated params
   *
   * @param $params
   *
   * @return array
   * @throws \api_Exception
   */
  protected function getValidParams($params) {
    if (!in_array($params['entity'], self::getAvailableEntities())) {
      throw new api_Exception('Invalid entity. Available values: (' . implode(', ', self::getAvailableEntities()) . ')', 'used_for_invalid_value');
    }

    return [
      'find_for' => self::$entityMap[$params['entity']]['find_for'],
      'entity_id' => $params['entity_id'],
    ];
  }

  /**
   * Returns results to api
   *
   * @return array
   */
  public function getResult() {
    $result = [];

    try {
      $customGroups = civicrm_api3('CustomGroup', 'get', [
        'sequential' => 1,
        'extends' => ['IN' => $this->validParams['find_for']],
        'is_active' => 1,
        'options' => ['limit' => 0],
      ]);
    } catch (CiviCRM_API3_Exception $e) {
      return [];
    }

    if (empty($customGroups['values'])) {
      return [];
    }

    foreach ($customGroups['values'] as $customGroup) {
      $result[] = $this->prepareCustomGroup($customGroup);
    }

    return $result;
  }

  /**
   * Returns prepared CustomGroup
   *
   * @param $customGroup
   *
   * @return array
   */
  private function prepareCustomGroup($customGroup) {
    $customGroupData = [
      'id' => $customGroup['id'],
      'name' => $customGroup['name'],
      'title' => $customGroup['title'],
      'style' => $customGroup['style'],
      'weight' => (int) $customGroup['weight'],
      'is_multiple' => $customGroup['is_multiple'],
      'custom_fields' => [],
    ];

    try {
      $customFields = civicrm_api3('CustomField', 'get', [
        'sequential' => 1,
        'custom_group_id' => $customGroup['id'],
        'options' => ['limit' => 0],
        'is_active' => 1,
      ]);
    } catch (CiviCRM_API3_Exception $e) {
      return $customGroupData;
    }

    if (empty($customFields['values'])) {
      return $customGroupData;
    }

    foreach ($customFields['values'] as $customField) {
      $customGroupData['custom_fields'][] = $this->prepareCustomField($customField, $customGroup);
    }

    return $customGroupData;
  }

  /**
   * Returns prepared CustomField
   *
   * @param $customField
   * @param $customGroup
   *
   * @return array
   */
  private function prepareCustomField($customField, $customGroup) {
    $customFieldId = CRM_CiviMobileAPI_Utils_CustomField::getId($customGroup['name'],  $customField['name']);
    $availableValues = [];

    if (!empty($customField['option_group_id'])) {
      $availableValues = CRM_CiviMobileAPI_Utils_OptionValue::getGroupValues($customField['option_group_id'], ['is_active' => 1]);
    }

    foreach ($availableValues as $key => $value) {
      $availableValues[$key]['weight'] = (int) $availableValues[$key]['weight'];
    }

    if ($customField['html_type'] == 'Radio' && $customField['data_type'] == "Boolean") {
      $availableValues = ['1','0'];
    }

    $prepareCustomField = [
      "id" => $customField['id'],
      "name" => $customField['name'],
      "default_value" => $customField['default_value'],
      "text_length" => (!empty($customField['text_length'])) ? (int) $customField['text_length'] : "NULL",
      "is_view" => $customField['is_view'],
      "label" => $customField['label'],
      "weight" => (int) $customField['weight'],
      "data_type" => $customField['data_type'],
      "html_type" => $customField['html_type'],
      "is_required" => $customField['is_required'],
      "current_value" => $this->getCurrentValue($customFieldId),
      "note_columns" => (!empty($customField['note_columns'])) ? (int) $customField['note_columns'] : "",
      "note_rows" => (!empty($customField['note_rows'])) ? (int) $customField['note_rows'] : "",
      "date_format" => (!empty($customField['date_format'])) ? $customField['date_format'] : "",
      "time_format" => (!empty($customField['time_format'])) ? $customField['time_format'] : "",
      "start_date_years" => (!empty($customField['start_date_years'])) ? $customField['start_date_years'] : "",
      "end_date_years" => (!empty($customField['end_date_years'])) ? $customField['end_date_years'] : "",
      "default_currency" => CRM_Core_Config::singleton()->defaultCurrency,
      "default_currency_symbol" => CRM_Core_Config::singleton()->defaultCurrencySymbol,
      "available_values" => $availableValues
    ];

    if ($prepareCustomField['data_type'] == 'Money'
      && ($prepareCustomField['html_type'] == 'Radio' || $prepareCustomField['html_type'] == 'Select') ) {
      $prepareCustomField['current_value'] = preg_replace("/.00$/", "", $prepareCustomField['current_value']);
    }

    return $prepareCustomField;
  }

  /**
   * Gets available entities for that api
   *
   * @return array
   */
  public static function getAvailableEntities() {
    return array_keys(self::$entityMap);
  }

  /**
   * Gets current values
   *
   * @param $customFieldId
   *
   * @return string
   */
  private function getCurrentValue($customFieldId) {
    try {
      $dbData = CRM_Core_BAO_CustomField::getTableColumnGroup($customFieldId);
    } catch (Exception $e) {
      return self::EMPTY_VALUE_SYMBOL;
    }

    $table = $dbData[0];
    $column = $dbData[1];
    $query = "SELECT {$table}.{$column} as current_value FROM {$table} WHERE {$table}.entity_id = {$this->validParams['entity_id']}";
    $result = CRM_Core_DAO::executeQuery($query);

    if ($result->fetch()) {
      return ($result->current_value === NULL) ? self::EMPTY_VALUE_SYMBOL : $result->current_value;
    }

    return self::EMPTY_VALUE_SYMBOL;
  }

}
