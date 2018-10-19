<?php

class CRM_CiviMobileAPI_PushNotification_Utils_Hook_Post_ParticipantPushNotification extends CRM_CiviMobileAPI_PushNotification_Utils_BasePushNotificationManager {

  /**
   * Instance of object reference
   *
   * @var object
   */
  private $objectRef;

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
    if ($this->action === 'edit') {
      $contacts[] = $this->objectRef->contact_id;
      return $contacts;
    }
  }
  
  /**
   * @inheritdoc
   */
  protected function getTitle() {
    return ts('Update registration to the event');
  }
  
  /**
   * @inheritdoc
   */
  protected function getText() {
    if($this->objectRef->event_id) {
      try {
        $eventTitle = civicrm_api3('Event', 'getvalue', ['return' => "title", 'id' => $this->objectRef->event_id]);
      } catch (Exception $e) {
        $eventTitle = NULL;
      }
      
      return $eventTitle;
    }
  }

}
