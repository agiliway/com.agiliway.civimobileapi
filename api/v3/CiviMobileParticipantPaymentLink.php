<?php

/**
 * Gets participant payment link for proceed mobile app payment
 * @param $params
 * @return array
 * @throws api_Exception
 */
function civicrm_api3_civi_mobile_participant_payment_link_get($params) {
  $result[] = (new CRM_CiviMobileAPI_Api_CiviMobileParticipantPaymentLink_Get($params))->getResult();
  return civicrm_api3_create_success($result);
}

/**
 * @param $params
 */
function _civicrm_api3_civi_mobile_participant_payment_link_get_spec(&$params) {
  $params['event_id'] = [
    'title' => 'Event id',
    'description' => ts('Event id'),
    'type' => CRM_Utils_Type::T_INT,
    'api.required' => 1,
  ];
  $params['contact_id'] = [
    'title' => 'Contact id',
    'description' => ts('Contact id'),
    'type' => CRM_Utils_Type::T_INT,
  ];
  $params['price_set'] = [
    'title' => 'Price Set',
    'description' => ts('Price Set'),
    'type' => CRM_Utils_Type::T_STRING,
    'api.required' => 1,
  ];
  $params['first_name'] = [
    'title' => 'First Name',
    'description' => ts('First Name'),
    'type' => CRM_Utils_Type::T_STRING,
  ];
  $params['last_name'] = [
    'title' => 'Last Name',
    'description' => ts('Last Name'),
    'type' => CRM_Utils_Type::T_STRING,
  ];
  $params['email'] = [
    'title' => 'Email',
    'description' => ts('Email'),
    'type' => CRM_Utils_Type::T_EMAIL,
  ];
}
