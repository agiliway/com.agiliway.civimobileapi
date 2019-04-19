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

}
