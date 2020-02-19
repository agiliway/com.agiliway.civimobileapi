<?php

class CRM_CiviMobileAPI_Api_CiviMobileGetPriceSetByEvent_Get extends CRM_CiviMobileAPI_Api_CiviMobileBase {

  /**
   * Returns validated params
   *
   * @param $params
   *
   * @return array
   * @throws \api_Exception
   */
  protected function getValidParams($params) {
    return [
      'event_id' => $params['event_id']
    ];
  }

  /**
   * Returns results to api
   *
   * @return array
   */
  public function getResult() {
    $eventId = (int) $this->validParams['event_id'];

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
      return $result;
    }

    $priceSetId = CRM_Price_BAO_PriceSet::getFor(CRM_Event_BAO_Event::getTableName(), $eventId);
    $priceSetFields = CRM_CiviMobileAPI_Utils_PriceSet::getFields($priceSetId);

    if (empty($priceSetFields) && empty($priceSetFields['values'])) {
      return $result;
    }

    foreach ($priceSetFields['values'] as $priceSetField) {
      if (!CRM_CiviMobileAPI_Utils_PriceSetField::isActualPriceFieldNow($priceSetField)) {
        continue;
      }
      $priceSetFieldValue = CRM_CiviMobileAPI_Utils_PriceSetField::getPriceSetFieldValue($priceSetField['id']);
      foreach ($priceSetFieldValue['values'] as &$value) {
        if (!empty($currencyName)) {
          $value['amount_currency'] = CRM_Utils_Money::format($value['amount'], $currencyName);
          $value['currency_name'] = $currencyName;
        }
      }

      if ($priceSetFieldValue) {
        $result[] = [
          'id' => $priceSetField['id'],
          'price_set_id' => $priceSetId,
          'label' => $priceSetField['label'],
          'type' => $priceSetField['html_type'],
          'is_required' => $priceSetField['is_required'],
          'items' => $priceSetFieldValue['values'],
        ];
      }
    }

    return $result;
  }

}
