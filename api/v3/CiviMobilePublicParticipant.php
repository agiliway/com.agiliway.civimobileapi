<?php

/**
 * Creates Participant
 *
 * @param array $params
 *
 * @return array
 */
function civicrm_api3_civi_mobile_public_participant_create($params) {
  $result = (new CRM_CiviMobileAPI_Api_CiviMobilePublicParticipant_Create($params))->getResult();

  return civicrm_api3_create_success($result);
}

/**
 * Adjust Metadata for get action
 *
 * The metadata is used for setting defaults, documentation & validation
 *
 * @param array $params array or parameters determined by getfields
 */
function _civicrm_api3_civi_mobile_public_participant_create_spec(&$params) {
  $params['contact_email'] = [
    'title' => 'Contact email',
    'description' => ts('Contact email.'),
    'type' => CRM_Utils_Type::T_STRING,
    'api.required' => 1,
  ];
  $params['event_id'] = [
    'title' => 'Event id',
    'description' => ts('Event id'),
    'type' => CRM_Utils_Type::T_INT,
    'api.required' => 1,
  ];
  $params['first_name'] = [
    'title' => 'First name',
    'description' => ts('First name'),
    'type' => CRM_Utils_Type::T_STRING,
    'api.required' => 1,
  ];
  $params['last_name'] = [
    'title' => 'Last name',
    'description' => ts('Last name'),
    'type' => CRM_Utils_Type::T_STRING,
    'api.required' => 1,
  ];
}

/**
 * Get public ticket
 *
 * @param array $params
 *
 * @return array
 */
function civicrm_api3_civi_mobile_public_participant_get_ticket($params) {
  $publicKeyFieldId = "custom_" . CRM_CiviMobileAPI_Utils_CustomField::getId(
      CRM_CiviMobileAPI_Install_Entity_CustomGroup::PUBLIC_INFO,
      CRM_CiviMobileAPI_Install_Entity_CustomField::PUBLIC_KEY
    );

  try{
    $participant = civicrm_api3('Participant', 'getsingle', [$publicKeyFieldId => $params['public_key']]);
  } catch (CiviCRM_API3_Exception $e) {
    throw new api_Exception(ts('Ticket not found.'), 'ticket_not_found');
  }

  try{
    $event = civicrm_api3('Event', 'getsingle', ['id' => $participant['event_id']]);
  } catch (CiviCRM_API3_Exception $e) {
    throw new api_Exception(ts('Event which is attached to ticket does not exist.'), 'event_does_not_exist');
  }

  return civicrm_api3_create_success([CRM_CiviMobileAPI_Utils_EventTicket::prepareTicket($participant, $event)]);
}

/**
 * Adjust Metadata for get_ticket action
 *
 * The metadata is used for setting defaults, documentation & validation
 *
 * @param array $params array or parameters determined by getfields
 */
function _civicrm_api3_civi_mobile_public_participant_get_ticket_spec(&$params) {
  $params['public_key'] = [
    'title' => 'Public key',
    'description' => ts('Public key.'),
    'type' => CRM_Utils_Type::T_STRING,
    'api.required' => 1,
  ];
}
