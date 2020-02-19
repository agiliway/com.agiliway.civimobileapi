<?php

class CRM_CiviMobileAPI_PushNotification_Utils_Hook_Post_ParticipantPushNotification extends CRM_CiviMobileAPI_PushNotification_Utils_BasePushNotificationManager {

  /**
   * Instance of object reference
   *
   * @var object
   */
  private $objectRef;

  /**
   * List of actions text
   *
   * @var array
   */
  private $actionText = [
    'create' => '%display_name has created participant.',
    'edit' => '%display_name has edited participant.',
    'delete' => '%display_name has deleted participant.'
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
    if (isset($this->objectRef->contact_id)) {
      $contacts[] = $this->objectRef->contact_id;
      return $contacts;
    }

    return NULL;
  }

  /**
   * @inheritdoc
   */
  protected function getTitle() {
    if ($this->objectRef->event_id) {
      try {
        $eventTitle = civicrm_api3('Event', 'getvalue', ['return' => "title", 'id' => $this->objectRef->event_id]);
      } catch (Exception $e) {
        $eventTitle = NULL;
      }
    }

    return (!empty($eventTitle)) ? $eventTitle : ts('Participant');
  }

  /**
   * @inheritdoc
   */
  protected function getText() {
    return isset($this->actionText[$this->action]) ? ts($this->actionText[$this->action]) : $this->action;
  }

}
