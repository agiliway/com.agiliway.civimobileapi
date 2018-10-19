<?php

class CRM_CiviMobileAPI_PushNotification_Utils_Hook_PostProcess_ParticipantPushNotification extends CRM_CiviMobileAPI_Hook_BasePushNotificationManager {

  /**
   * List of actions text
   *
   * @var array
   */
  private $actionText = [
    CRM_Core_Action::ADD => 'New registration to the event',
    CRM_Core_Action::UPDATE => 'Update registration to the event',
  ];

  /**
   * Instance of form object
   *
   * @var object
   */
  private $form;

  /**
   * Sets form
   *
   * @param object $form
   */
  public function setForm($form) {
    $this->form = $form;
  }

  /**
   * @inheritdoc
   */
  public function getContact() {
    $contacts[] = $this->form->_contactId;
    return $contacts;
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
    if($this->id) {
      try {
        $eventTitle = civicrm_api3('Event', 'getvalue', ['return' => 'title', 'id' => $this->id]);
      } catch (Exception $e) {
        $eventTitle = NULL;
      }
      
      return $eventTitle;
    }
  }

}
