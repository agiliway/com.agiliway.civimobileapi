<?php

use CRM_CiviMobileAPI_PushNotification_Helper as PushNotification_Helper;

class CRM_CiviMobileAPI_PushNotification_Utils_Hook_Pre_ParticipantPushNotification extends CRM_CiviMobileAPI_PushNotification_Utils_BasePushNotificationManager {

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
    return ts('Registration to the event removed');
  }
  
  
  /**
   * @inheritdoc
   */
  protected function getText() {
    if($this->action === 'delete' && $this->id) {
      try {
        $eventTitle = civicrm_api3('Participant', 'getvalue', ['return' => 'event_title', 'id' => $this->id]);
      } catch (Exception $e) {
        $eventTitle = NULL;
      }
    
      return $eventTitle;
    }
  }

}
