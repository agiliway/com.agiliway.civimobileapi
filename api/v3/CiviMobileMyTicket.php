<?php

/**
 * Gets my Tickets
 *
 * @param array $params
 *
 * @return array API result array
 */
function civicrm_api3_civi_mobile_my_ticket_get($params) {
  if (!CRM_CiviMobileAPI_Utils_Permission::isEnoughPermissionForViewMyTickets()) {
    throw new \API_Exception(ts('Permission is required.'));
  }

  $event = new CRM_Event_BAO_Event();
  $event->id = $params['event_id'];
  $eventExistence = $event->find(TRUE);
  if (empty($eventExistence)) {
    throw new api_Exception('Event(id=' . $params['event_id'] . ') does not exist.', 'event_does_not_exist');
  }

  $contact = new CRM_Contact_BAO_Contact();
  $contact->id = $params['contact_id'];
  $contactExistence = $contact->find(TRUE);
  if (empty($contactExistence)) {
    throw new api_Exception('Contact(id=' . $params['contact_id'] . ') does not exist.', 'contact_does_not_exist');
  }

  $myTickets = CRM_CiviMobileAPI_Utils_EventTicket::getAll($params['event_id'], $params['contact_id']);

  return civicrm_api3_create_success($myTickets);
}

/**
 * Specify Metadata for get action.
 *
 * @param array $params
 */
function _civicrm_api3_civi_mobile_my_ticket_get_spec(&$params) {
  $params['contact_id'] = [
    'title' => 'Contact ID ',
    'description' => ts('Contact id'),
    'api.required' => 1,
    'type' => CRM_Utils_Type::T_INT,
  ];
  $params['event_id'] = [
    'title' => 'Event Id',
    'description' => ts('Event Id'),
    'api.required' => 1,
    'type' => CRM_Utils_Type::T_INT,
  ];
}
