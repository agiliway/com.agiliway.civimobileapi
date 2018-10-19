<?php

class CRM_CiviMobileAPI_PushNotification_Utils_Hook_Post_CasePushNotification extends CRM_CiviMobileAPI_PushNotification_Utils_BasePushNotificationManager {

  /**
   * List of actions text
   *
   * @var array
   */
  private $actionText = [
    'create' => 'New case',
    'edit' => 'Case details updated',
  ];

  private $objectRef;

  /**
   * @param mixed $objectRef
   */
  public function setObjectRef($objectRef) {
    $this->objectRef = $objectRef;
  }

  /**
   * @inheritdoc
   */
  protected function getContact() {
    switch ($this->action) {
      case 'create':
        return $this->getContactFromCreateAction();
        break;

      case 'edit':
        return CRM_CiviMobileAPI_PushNotification_Helper::getCaseRelationshipContacts($this->id);
        break;

      default:
        return [];
        break;
    }
  }

  /**
   * @inheritdoc
   */
  protected function getTitle() {
    return isset($this->actionText[$this->action]) ? ts($this->actionText[$this->action]) : '';
  }

  /**
   * @inheritdoc
   */
  protected function getText() {
    if (CRM_Utils_Request::retrieve('activity_subject', 'String')) {
      return CRM_Utils_Request::retrieve('activity_subject', 'String');
    }
    if (CRM_Utils_Request::retrieve('subject', 'String')) {
      return CRM_Utils_Request::retrieve('subject', 'String');
    }
  }

  private function getCaseContact($id) {
    $result = civicrm_api3('CaseContact', 'get', [
      'return' => "contact_id",
      'case_id' => $id,
    ]);
    return key($result['values']);
  }

  private function getContactFromCreateAction() {
    $contacts = [];

    $contactID = CRM_CiviMobileAPI_Utils_Request::getInstance()
      ->get('cid', 'String');
    if (isset($contactID)) {
      $contacts[] = $contactID;
      return $contacts;
    }

    $paramsJson = CRM_CiviMobileAPI_Utils_Request::getInstance()
      ->get('json', 'String');
    $contactId = isset($paramsJson) ? json_decode($paramsJson)->contact_id : NULL;
    if (isset($contactId)) {
      $contacts[] = $contactId;
      return $contacts;
    }

    $contacts[] = isset($_POST['client_id']) ? $_POST['client_id'] : [];
    return $contacts;
  }

}
