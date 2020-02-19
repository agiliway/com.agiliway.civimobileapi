<?php

/**
 * @deprecated will be deleted in version 7.0.0
 */
class CRM_CiviMobileAPI_ApiWrapper_Participant_Create implements API_Wrapper {

  /**
   * Interface for interpreting api input
   *
   * @param array $apiRequest
   *
   * @return array
   * @throws API_Exception
   * @throws CiviCRM_API3_Exception
   */
  public function fromApiInput($apiRequest) {
    if (empty($apiRequest['params']['event_id']) || empty($apiRequest['params']['contact_id'])) {
      return $apiRequest;
    }

    $participant = new CRM_Event_BAO_Participant();
    $participant->contact_id = $apiRequest['params']['contact_id'];
    $participant->event_id = $apiRequest['params']['event_id'];
    $participantExist = $participant->find(TRUE);

    if (!empty($participantExist)) {
      throw new api_Exception(ts('This contact has already been assigned to this event.'), 'contact_already_registered');
    }

    if (empty($apiRequest['params']['fee_currency'])) {
      try {
        $feeCurrency = civicrm_api3('Event', 'getvalue', ['return' => "currency", 'id' => $apiRequest['params']['event_id']]);
      } catch (CiviCRM_API3_Exception $e) {
        $feeCurrency = false;
      }

      if (!empty($feeCurrency)) {
        $apiRequest['params']['fee_currency'] = $feeCurrency;
      }
    }

    if (empty($apiRequest['params']['status_id'])) {
      throw new \API_Exception(ts('Empty participant status field(status_id). Please fill it.'));
    }

    return $apiRequest;
  }

  /**
   * Interface for interpreting api output
   *
   * @param $apiRequest
   * @param $result
   *
   * @return array
   */
  public function toApiOutput($apiRequest, $result) {
    if (!empty($apiRequest['params']['send_confirmation']) && $apiRequest['params']['send_confirmation'] == 1) {
      if (!empty($result['values'])) {
        $currentContactId = CRM_CiviMobileAPI_Utils_Contact::getCurrentContactId();
        foreach ($result['values'] as $participant) {
          if ($participant['contact_id'] == $currentContactId) {
            CRM_CiviMobileAPI_Utils_Emails_EventConfirmationReceipt::send($participant['id'],'event_online_receipt');
          } else {
            CRM_CiviMobileAPI_Utils_Emails_EventConfirmationReceipt::send($participant['id'], 'event_offline_receipt');
          }
        }
      }
    }

    return $result;
  }

}
