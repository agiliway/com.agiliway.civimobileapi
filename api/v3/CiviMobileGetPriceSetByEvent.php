<?php

/**
 * Gets price set values by event
 *
 * @param array $params
 *
 * @return array
 */
function civicrm_api3_civi_mobile_get_price_set_by_event_get($params) {
  $result = (new CRM_CiviMobileAPI_Api_CiviMobileGetPriceSetByEvent_Get($params))->getResult();
  return civicrm_api3_create_success($result);
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
