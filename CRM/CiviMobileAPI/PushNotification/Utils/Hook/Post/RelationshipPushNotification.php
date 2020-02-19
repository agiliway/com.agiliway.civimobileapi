<?php

class CRM_CiviMobileAPI_PushNotification_Utils_Hook_Post_RelationshipPushNotification extends CRM_CiviMobileAPI_PushNotification_Utils_BasePushNotificationManager {

  /**
   * Instance of object reference
   *
   * @var object
   */
  private $objectRef;

  /**
   * Id of the case
   *
   * @var int
   */
  private $caseID;

  /**
   * List of actions text
   *
   * @var array
   */
  private $actionText = [
    'create' => '%display_name has created relationship.',
    'edit' => '%display_name has edited relationship.',
    'delete' => '%display_name has deleted relationship.'
  ];

  /**
   * Sets object reference
   *
   * @param object $objectRef
   */
  public function setObjectRef(&$objectRef) {
    $this->objectRef = $objectRef;
  }

  /**
   * @inheritdoc
   */
  protected function getContact() {
    $contacts = [];
    $this->setCaseId();

    if ($this->caseID) {
      $contacts = CRM_CiviMobileAPI_PushNotification_Helper::getCaseRelationshipContacts($this->caseID);
    }

    if (isset($this->objectRef->contact_id_a) && isset($this->objectRef->contact_id_b)) {
      $contacts[] = $this->objectRef->contact_id_a;
      $contacts[] = $this->objectRef->contact_id_b;
    }

    $contacts = array_unique($contacts);
    $contactId = CRM_Core_Session::singleton()->getLoggedInContactID();
    if ($key = array_search($contactId, $contacts)) {
      unset($contacts[$key]);
    }

    return $contacts;
  }

  /**
   *  Sets case id
   */
  private function setCaseId() {
    switch ($this->action) {
      case 'create':
        $this->caseID = isset($this->objectRef->case_id) ? $this->objectRef->case_id : NULL;
        break;

      case 'edit':
        if (isset($this->objectRef->case_id)) {
          $this->caseID = $this->objectRef->case_id;
        }
        else {
          $this->caseID = CRM_CiviMobileAPI_Utils_Request::getInstance()
            ->post('case_id', 'String');
        }
        break;
    }

  }

  /**
   * @inheritdoc
   */
  protected function getTitle() {
    if ($this->caseID) {
      try {
        $caseTitle = civicrm_api3('Case', 'getvalue', ['return' => 'subject', 'id' => $this->caseID]);
      } catch (Exception $e) {
        $caseTitle = NULL;
      }
    }

    return (!empty($caseTitle)) ?  $caseTitle : ts('Relationship');
  }

  /**
   * @inheritdoc
   */
  protected function getText() {
    return isset($this->actionText[$this->action]) ? ts($this->actionText[$this->action]) : $this->action;
  }

}
