<?php

/**
 * Creates Participant with payment
 *
 * @param array $params
 *
 * @return array
 * @throws API_Exception
 */
function civicrm_api3_civi_mobile_participant_payment_create($params) {
  if (!CRM_CiviMobileAPI_Utils_Permission::isEnoughPermissionForCreateParticipantWithPayment()) {
    throw new api_Exception('Permission required.', 'permission_required');
  }

  $result = (new CRM_CiviMobileAPI_Api_CiviMobileParticipantPayment_Create($params))->getResult();

  return civicrm_api3_create_success($result);
}

/**
 * Specify Metadata for get action.
 *
 * @param array $params
 */
function _civicrm_api3_civi_mobile_participant_payment_create_spec(&$params) {
  $params['event_id'] = [
    'title' => 'Event id',
    'description' => ts('Event id'),
    'type' => CRM_Utils_Type::T_INT,
    'api.required' => 1,
  ];

  $params['contact_id'] = [
    'title' => 'contact_id',
    'description' => ts('Contact id'),
    'type' => CRM_Utils_Type::T_INT,
    'api.required' => 1,
  ];

  $params['role_id'] = [
    'title' => 'Participant role id',
    'description' => ts('Participant role id'),
    'type' => CRM_Utils_Type::T_INT,
    'api.required' => 0,
  ];

  $params['participant_status_id'] = [
    'title' => 'Participant status id',
    'description' => ts('Participant status id'),
    'type' => CRM_Utils_Type::T_INT,
    'api.required' => 1,
  ];

  $params['send_confirmation'] = [
    'title' => 'Send confirmation',
    'description' => ts('Send confirmation'),
    'type' => CRM_Utils_Type::T_BOOLEAN,
    'api.required' => 1,
  ];

  $params['price_set_selected_values'] = [
    'title' => 'Price set selected values',
    'description' => ts('Price set selected values'),
    'type' => CRM_Utils_Type::T_STRING,
    'api.required' => 0,
  ];
}
