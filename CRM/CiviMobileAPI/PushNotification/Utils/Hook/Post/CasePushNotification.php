<?php

class CRM_CiviMobileAPI_PushNotification_Utils_Hook_Post_CasePushNotification extends CRM_CiviMobileAPI_PushNotification_Utils_BasePushNotificationManager {

  /**
   * List of actions text
   *
   * @var array
   */
  private $actionText = [
    'create' => '%display_name has created case.',
    'edit' => '%display_name has edited case.',
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

      case 'edit':
        return CRM_CiviMobileAPI_PushNotification_Helper::getCaseRelationshipContacts($this->id);

      default:
        return [];
    }
  }

  /**
   * @inheritdoc
   */
  protected function getTitle() {
    if (CRM_Utils_Request::retrieve('activity_subject', 'String')) {
      return CRM_Utils_Request::retrieve('activity_subject', 'String');
    }

    if (CRM_Utils_Request::retrieve('subject', 'String')) {
      return CRM_Utils_Request::retrieve('subject', 'String');
    }

    if (!empty($this->objectRef->subject)) {
      return $this->objectRef->subject;
    }

    return ts('Case');
  }

  /**
   * @inheritdoc
   */
  protected function getText() {
    return isset($this->actionText[$this->action]) ? ts($this->actionText[$this->action]) : $this->action;
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

    $contacts = isset($_POST['client_id']) ? [$_POST['client_id']] : [];

    return $contacts;
  }

}
