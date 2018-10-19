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

    if (isset($objectRef->contact_id_a) && isset($objectRef->contact_id_b)) {
      $contacts[] = $this->objectRef->contact_id_a;
      $contacts[] = $this->objectRef->contact_id_b;
    }

    return array_unique($contacts);
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
    switch ($this->action) {
      case 'create':
        return isset($this->objectRef->case_id) ? ts('Role in the case added') : '';
      break;

      case 'edit':
        if (isset($this->objectRef->case_id)) {
          return ts('Role in the case changed');
        }
        else {
          return $this->caseID ? ts('Role in the case removed') : '';
        }
      break;

      default:
        return '';
      break;
    }
  }

  /**
   * @inheritdoc
   */
  protected function getText() {
//    $this->setCaseId();
    if($this->caseID) {
      try {
        $caseTitle = civicrm_api3('Case', 'getvalue', ['return' => 'subject', 'id' => $this->caseID]);
      } catch (Exception $e) {
        $caseTitle = NULL;
      }
      
      return $caseTitle;
    }
  }
  
}
