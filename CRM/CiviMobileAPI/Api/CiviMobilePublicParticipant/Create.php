<?php

class CRM_CiviMobileAPI_Api_CiviMobilePublicParticipant_Create extends CRM_CiviMobileAPI_Api_CiviMobileBase {

  /**
   * Returns results to api
   *
   * @return array
   * @throws CiviCRM_API3_Exception
   */
  public function getResult() {
    $publicKeyFieldId = "custom_" . CRM_CiviMobileAPI_Utils_CustomField::getId(
      CRM_CiviMobileAPI_Install_Entity_CustomGroup::PUBLIC_INFO,
      CRM_CiviMobileAPI_Install_Entity_CustomField::PUBLIC_KEY
      );
    $result = civicrm_api3('Participant', 'create', [
      'event_id' => $this->validParams["event_id"],
      'contact_id' => $this->validParams["contact_id"],
    ]);

    $publicKey = CRM_CiviMobileAPI_Utils_Participant::generatePublicKey($result['id']);
    $result = civicrm_api3('Participant', 'create', [
      'id' => $result['id'],
      $publicKeyFieldId => $publicKey
    ]);

    foreach ($result["values"] as $key => $participant) {
      $result["values"][$key]['participant_public_key'] = $publicKey;
    }

    return $result["values"];
  }

  /**
   * Returns validated params
   *
   * @param $params
   * @return array
   * @throws api_Exception
   */
  protected function getValidParams($params) {
    $event = new CRM_Event_BAO_Event();
    $event->id = $params['event_id'];
    $event->is_public = 1;
    $eventExistence = $event->find(TRUE);
    if (empty($eventExistence)) {
      throw new api_Exception(ts('Event(id=' . $params['event_id'] . ') does not exist or is not public.'), 'public_event_does_not_exist');
    }

    $result = [
      'event_id' => $params["event_id"],
      'contact_id' => $this->getContactId($params),
    ];

    return $result;
  }

  /**
   * Gets ContactId by Email.
   * If not exist contact with given email, it creates new Contact with
   *
   * @param $params
   * @return integer
   * @throws api_Exception
   */
  private function getContactId($params) {
    try {
      $contactByEmail = civicrm_api3('Email', 'getsingle', [
        'sequential' => 1,
        'is_primary' => 1,
        'email' => $params["contact_email"],
      ]);
    } catch (CiviCRM_API3_Exception $e) {
      return $this->createContact($params);
    }

    return (int) $contactByEmail["contact_id"];
  }

  /**
   * Creates new Contact by params
   *
   * @param $params
   * @return integer
   * @throws api_Exception
   */
  private function createContact($params) {
    try {
      $contact = civicrm_api3('Contact', 'create', [
        'contact_type' => "Individual",
        'first_name' => $params["first_name"],
        'last_name' => $params["last_name"],
      ]);
    } catch (CiviCRM_API3_Exception $e) {
      throw new api_Exception(ts('Can not create Contact. Error: ') . $e->getMessage(), 'can_not_create_contact');
    }

    try {
      civicrm_api3('Email', 'create', [
        'contact_id' => $contact["id"],
        'email' => $params["contact_email"],
        'is_primary' => 1,
      ]);
    } catch (CiviCRM_API3_Exception $e) {
      throw new api_Exception(ts('Can not create Email to Contact. Error: ') . $e->getMessage(), 'can_not_create_email_to_contact');
    }

    return (int) $contact["id"];
  }

}
