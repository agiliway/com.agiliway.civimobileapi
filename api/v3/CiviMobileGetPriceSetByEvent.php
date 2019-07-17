<?php

/**
 * Gets price set values by event
 *
 * @param array $params
 *
 * @return array
 * @throws API_Exception
 * @throws \Civi\API\Exception\UnauthorizedException
 */
function civicrm_api3_civi_mobile_get_price_set_by_event_get($params) {
  _civicrm_api3_price_set_check_permission($params);
  $eventId = (int) $params['event_id'];

  try {
    $eventInfo = civicrm_api3('Event', 'getsingle', [
      'return' => ["is_monetary", "currency"],
      'id' => $eventId
    ]);
  } catch (CiviCRM_API3_Exception $e) {
    $eventInfo = [];
  }

  $result = [];
  $isMonetary = false;
  if (!empty($eventInfo)) {
    $isMonetary = !empty($eventInfo['is_monetary']) ? $eventInfo['is_monetary'] : false;
    $currencyName = $eventInfo['currency'];
  }

  if ($isMonetary != 1) {
    return civicrm_api3_create_success($result);
  }

  $priceSetId = CRM_Price_BAO_PriceSet::getFor(CRM_Event_BAO_Event::getTableName(), $eventId);
  $priceSetFields = CRM_CiviMobileAPI_Utils_PriceSet::getFields($priceSetId);

  if (empty($priceSetFields) && empty($priceSetFields['values'])) {
    return civicrm_api3_create_success($result);
  }

  foreach ($priceSetFields['values'] as $priceSetField) {
    $priceSetFieldValue = getPriceSetFieldValue($priceSetField['id']);
    foreach ($priceSetFieldValue['values'] as &$value) {
      if (!empty($currencyName)) {
        $value['amount_currency'] = CRM_Utils_Money::format($value['amount'], $currencyName);
        $value['currency_name'] = $currencyName;
      }
    }

    if ($priceSetFieldValue) {
      $result[] = [
        'id' => $priceSetField['id'],
        'label' => $priceSetField['label'],
        'type' => $priceSetField['html_type'],
        'is_required' => $priceSetField['is_required'],
        'items' => $priceSetFieldValue['values'],
      ];
    }
  }

  return civicrm_api3_create_success($result);
}

/**
 * @param $priceSetFieldId
 * @return array|bool
 */
function getPriceSetFieldValue($priceSetFieldId) {
  try {
    $priceFieldValue = civicrm_api3('PriceFieldValue', 'get', [
      'sequential' => 1,
      'return' => ['id', 'name', 'price_field_id', 'amount', 'label'],
      'price_field_id' => $priceSetFieldId,
      'is_active' => 1
    ]);
  } catch (CiviCRM_API3_Exception $e) {
    return false;
  }

  return $priceFieldValue;
}

/**
 * @param $params
 * @throws \Civi\API\Exception\UnauthorizedException
 */
function _civicrm_api3_price_set_check_permission($params) {
  if (!CRM_Core_Permission::check('access CiviCRM')) {
    throw new \Civi\API\Exception\UnauthorizedException('Permission denied.');
  }
}

/**
 * Specify Metadata for get action.
 *
 * @param array $params
 */
function _civicrm_api3_civi_mobile_get_price_set_by_event_get_spec(&$params) {
  $params['event_id'] = [
    'title' => 'Event id',
    'description' => ts('Event id'),
    'type' => CRM_Utils_Type::T_INT,
    'api.required' => 1,
  ];
}
