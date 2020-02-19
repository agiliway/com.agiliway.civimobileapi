<?php

use CRM_CiviMobileAPI_PushNotification_Helper as PushNotification_Helper;

class CRM_CiviMobileAPI_PushNotification_Utils_Hook_Pre_ParticipantPushNotification extends CRM_CiviMobileAPI_PushNotification_Utils_BasePushNotificationManager {

  /**
   * List of actions text
   *
   * @var array
   */
  private $actionText = ['delete' => '%display_name has deleted participant.'];

  /**
   * @inheritdoc
   */
  protected function getContact() {
    return $this->action === 'delete' ? PushNotification_Helper::getEventContactByParticipantId($this->id) : '';
  }

  /**
   * @inheritdoc
   */
  protected function getTitle() {
    if ($this->action === 'delete' && $this->id) {
      try {
        $eventTitle = civicrm_api3('Participant', 'getvalue', ['return' => 'event_title', 'id' => $this->id]);
      }
      catch (Exception $e) {
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
