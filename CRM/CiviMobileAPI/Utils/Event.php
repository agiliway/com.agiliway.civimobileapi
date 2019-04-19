<?php

/**
 * Class provide Event helper methods
 */
class CRM_CiviMobileAPI_Utils_Event {

  /**
   * Checks if "Same email" option is enabled
   * This option allows creating Participant
   * with Contacts which has same emails
   *
   * @param $eventId
   *
   * @return bool
   * @throws \CiviCRM_API3_Exception
   */
  public static function isAllowSameEmail($eventId) {
    $allowSameParticipantEmails = civicrm_api3('Event', 'getvalue', [
      'return' => "allow_same_participant_emails",
      'id' => $eventId,
    ]);

    return !empty($allowSameParticipantEmails) && $allowSameParticipantEmails == 1;
  }

}
