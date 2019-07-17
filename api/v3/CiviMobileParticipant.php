<?php

/**
 * Updates Participant status to 'Registered' or 'Attended'
 *
 * @param array $params
 *
 * @return array
 * @throws API_Exception
 */
function civicrm_api3_civi_mobile_participant_create($params) {
  if (!CRM_CiviMobileAPI_Utils_Permission::isEnoughPermissionForChangingParticipantStatuses()) {
    throw new api_Exception('Permission required.', 'permission_required');
  }
  $participantId = (int) $params['participant_id'];
  $participant = new CRM_Event_DAO_Participant();
  $participant->id = $participantId;

  if (!$participant->find(TRUE)) {
    throw new api_Exception('Participant(id = '. $participantId .') doesn\'t exist.' , 'participant_doesnt_exist');
  }

  $participantEventId = $participant->event_id;
  $qrCodeInfo = CRM_CiviMobileAPI_Utils_ParticipantQrCode::getQrCodeInfo($participantId);

  if ($participantEventId != $params['event_id']) {
    throw new api_Exception('Event id does not exist for this participant id.' , 'uncorrect_event_id');
  }

  $allowStatuses = [1, 2];
  if (!in_array($params['status_id'], $allowStatuses)) {
    throw new api_Exception('Field "status_id" is not allowed status. Allow statuses: ' . implode(', ', $allowStatuses) . ' (Registered, Attended).', 'not_allowed_status');
  }

  if (!in_array($participant->status_id, $allowStatuses)) {
    throw new api_Exception('Participant does not have status Registered.', 'participant_status_is_not_registered');
  }

  if (!empty($qrCodeInfo['qr_code_hash']) && $qrCodeInfo['qr_code_hash'] != $params['qr_token']) {
    throw new api_Exception('QR token does not exist for this participant id. Please fill correct token.' , 'uncorrect_token');
  }

  $participantStatusId = (int) $params['status_id'];
  $participant = new CRM_Event_DAO_Participant();
  $participant->id = $participantId;
  $participant->find(TRUE);
  
  if ($participant->status_id == $participantStatusId) {
    throw new api_Exception('Participant(id = '. $participantId .') already has this status.' , 'error_same_status');
  }

  $participant = new CRM_Event_DAO_Participant();
  $participant->id = $participantId;
  $participant->status_id = $participantStatusId;
  $participant->save();

  $result = ['message' => ts("Participant status successfully updated.")];

  return civicrm_api3_create_success($result);
}

/**
 * Specify Metadata for get action.
 *
 * @param array $params
 */
function _civicrm_api3_civi_mobile_participant_create_spec(&$params) {
  $params['participant_id'] = [
    'title' => 'Participant id',
    'description' => ts('Participant id'),
    'type' => CRM_Utils_Type::T_INT,
    'api.required' => 1,
  ];
  $params['status_id'] = [
    'title' => 'Participant status id',
    'description' => ts('Participant status id. Allow statuses: "Registered" or "Attended"'),
    'type' => CRM_Utils_Type::T_INT,
    'api.required' => 1,
  ];
  $params['event_id'] = [
    'title' => 'Event id',
    'description' => ts('Event id'),
    'type' => CRM_Utils_Type::T_INT,
    'api.required' => 1,
  ];
  $params['qr_token'] = [
    'title' => 'QR token',
    'description' => ts('Qr token'),
    'type' => CRM_Utils_Type::T_STRING,
  ];
}
