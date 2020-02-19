<?php

class CRM_CiviMobileAPI_PushNotification_Utils_Hook_PostProcess_ParticipantPushNotification extends CRM_CiviMobileAPI_Hook_BasePushNotificationManager {

  /**
   * List of actions text
   *
   * @var array
   */
  private $actionText = [
    'create' => '%display_name has created participant.',
    'edit' => '%display_name has edited participant.',
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
    if ($this->id) {
      try {
        $eventTitle = civicrm_api3('Event', 'getvalue', ['return' => 'title', 'id' => $this->id]);
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
