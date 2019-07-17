<?php

/**
 * Class provide Event Tickets helper methods
 */
class CRM_CiviMobileAPI_Utils_EventTicket {

  /**
   * Gets all Contact's tickets
   *
   * @param $eventId
   * @param $contactId
   *
   * @return array
   */
  public static function getAll($eventId, $contactId) {
    if (empty($eventId) || empty($contactId)) {
      return [];
    }

    $myTickets = [];
    $event = CRM_CiviMobileAPI_Utils_Event::getById($eventId);
    $participants = CRM_CiviMobileAPI_Utils_Participant::getByEventAndContactId($eventId, $contactId);

    try {
      $eventInfo = civicrm_api3('Event', 'getsingle', [
        'return' => ["is_monetary", "currency"],
        'id' => $eventId,
      ]);
    } catch (CiviCRM_API3_Exception $e) {
      $eventInfo = [];
    }

    foreach ($participants as $participant) {
      $participantContactDisplayName = CRM_Core_DAO::getFieldValue('CRM_Contact_DAO_Contact', $participant['contact_id'], 'display_name');
      $qrCodeInfo = CRM_CiviMobileAPI_Utils_ParticipantQrCode::getQrCodeInfo($participant['id']);

      if ($eventInfo['is_monetary'] == 1) {
        $currency = CRM_Utils_Money::format($participant['participant_fee_amount'], $participant['participant_fee_currency']);
      }

      $myTickets[] = [
        'participant_contact_display_name' => $participantContactDisplayName,
        'participant_status_name' => !empty($participant['participant_status']) ? ts($participant['participant_status']) : '',
        'participant_role_name' => !empty($participant['participant_role']) ? ts($participant['participant_role']) : '',
        'participant_fee_amount' => !empty($participant['participant_fee_amount']) ? $participant['participant_fee_amount'] : '0',
        'participant_fee_amount_currency' => !empty($currency) ? $currency : '',
        'event_name' => $event['event_title'],
        'event_start_date' => !empty($event['event_start_date']) ? $event['event_start_date'] : '',
        'event_end_date' => !empty($event['event_end_date']) ? $event['event_end_date'] : '',
        'qr_code_link' => !empty($qrCodeInfo['qr_code_image']) ? $qrCodeInfo['qr_code_image'] : ''
      ];
    }

    return $myTickets;
  }

}
