<?php

class CRM_CiviMobileAPI_ApiWrapper_Participant_Create implements API_Wrapper {

  /**
   * Interface for interpreting api input
   *
   * @param array $apiRequest
   *
   * @return array
   * @throws \API_Exception
   * @throws \CiviCRM_API3_Exception
   */
  public function fromApiInput($apiRequest) {
    if (empty($apiRequest['params']['event_id']) || empty($apiRequest['params']['contact_id'])) {
      return $apiRequest;
    }

    $eventId = $apiRequest['params']['event_id'];
    if (CRM_CiviMobileAPI_Utils_Event::isAllowSameEmail($eventId)) {
      return $apiRequest;
    }

    $currentContactId = $apiRequest['params']['contact_id'];
    $currentContactEmails = CRM_CiviMobileAPI_Utils_Contact::getEmails($currentContactId);

    foreach (CRM_CiviMobileAPI_Utils_Participant::getContactIds($eventId) as $registeredContactId) {
      $this->validateSameEmails($eventId, $currentContactId, $currentContactEmails, $registeredContactId);
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
    return $result;
  }

  /**
   * Validates if emails has already registered
   *
   * @param $eventId
   * @param $currentContactId
   * @param $currentContactEmails
   * @param $registeredContactId
   *
   * @throws \API_Exception
   */
  private function validateSameEmails($eventId, $currentContactId, $currentContactEmails, $registeredContactId) {
    $registeredEmails = CRM_CiviMobileAPI_Utils_Contact::getEmails($registeredContactId);
    foreach ($currentContactEmails as $email) {
      if (in_array($email, $registeredEmails)) {
        throw new \API_Exception(ts('This email has been already registered for an Event'));
      }
    }
  }

}
