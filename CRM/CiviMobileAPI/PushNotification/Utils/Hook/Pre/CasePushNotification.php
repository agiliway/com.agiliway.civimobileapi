<?php

use CRM_CiviMobileAPI_PushNotification_Helper as PushNotification_Helper;

class CRM_CiviMobileAPI_PushNotification_Utils_Hook_Pre_CasePushNotification extends CRM_CiviMobileAPI_PushNotification_Utils_BasePushNotificationManager {

  /**
   * @inheritdoc
   */
  protected function getContact() {
    return $this->action === 'delete' ? PushNotification_Helper::getCaseRelationshipContacts($this->id) : '';
  }
  
  /**
   * @inheritdoc
   */
  protected function getTitle() {
    return ts('Case removed');
  }
  
  /**
   * @inheritdoc
   */
  protected function getText() {
    if($this->action === 'delete' && $this->id) {
      try {
        $caseTitle = civicrm_api3('Case', 'getvalue', ['return' => 'subject', 'id' => $this->id]);
      } catch (Exception $e) {
        $caseTitle = NULL;
      }
      
      return $caseTitle;
    }
  }

}
