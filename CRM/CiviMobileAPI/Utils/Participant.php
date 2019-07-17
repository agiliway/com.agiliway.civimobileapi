<?php

/**
 * Class provide Participant helper methods
 */
class CRM_CiviMobileAPI_Utils_Participant {

  /**
   * Gets Contact ids registered on Event
   *
   * @param $eventId
   *
   * @return array
   */
  public static function getContactIds($eventId) {
    $contactIds = [];
    if (empty($eventId)) {
      return $contactIds;
    }

    try {
      $result = civicrm_api3('Participant', 'get', [
        'sequential' => 1,
        'return' => ["contact_id"],
        'event_id' => $eventId,
        'options' => ['limit' => 0],
      ]);
    } catch (CiviCRM_API3_Exception $e) {
      return $contactIds;
    }

    if (empty($result))  {
      return $contactIds;
    }

    foreach ($result['values'] as $participant) {
      $contactIds[] = $participant['contact_id'];
    }

    return $contactIds;
  }

  /**
   * Gets Participant by Event id and Contact id
   *
   * @param $eventId
   *
   * @param $contactId
   *
   * @return array
   */
  public static function getByEventAndContactId($eventId, $contactId) {
    if (empty($eventId) || empty($contactId)) {
      return [];
    }

    $participants = civicrm_api3('Participant', 'get', [
      'sequential' => 1,
      'contact_id' => $contactId,
      'event_id' => $eventId,
      'options' => ['limit' => 0],
    ]);

    return (!empty($participants['values'])) ? $participants['values'] : [];
  }

  /**
   * Gets Participant's Contribution
   *
   * @param $participantId
   *
   * @return \CRM_Contribute_BAO_Contribution|null
   */
  public static function getParticipantContribution($participantId) {
    $participantContributionId = CRM_Core_DAO::getFieldValue('CRM_Event_DAO_ParticipantPayment', $participantId, 'contribution_id', 'participant_id');

    if (empty($participantContributionId)) {
      return NULL;
    }

    $contribution = new CRM_Contribute_BAO_Contribution();
    $contribution->id = $participantContributionId;
    $contribution->find(TRUE);

    if (empty($contribution)) {
      return NULL;
    }

    return $contribution;
  }

}
