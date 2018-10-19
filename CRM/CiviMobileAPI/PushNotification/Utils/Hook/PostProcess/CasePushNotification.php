<?php

class CRM_CiviMobileAPI_PushNotification_Utils_Hook_PostProcess_CasePushNotification extends CRM_CiviMobileAPI_PushNotification_Utils_BasePushNotificationManager {

  /**
   * List of actions text
   *
   * @var array
   */
  private $actionText = [
    "create" => 'Activity in the case created',
    "edit" => 'Activity details updated',
    "delete" => 'Activity removed',
  ];

  /**
   * @inheritdoc
   */
  protected function getContact() {
    return isset($this->id) ? CRM_CiviMobileAPI_PushNotification_Helper::getCaseRelationshipContacts($this->id) : '';
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
        $caseTitle = civicrm_api3('Case', 'getvalue', ['return' => 'subject', 'id' => $this->id]);
      } catch (Exception $e) {
        $caseTitle = NULL;
      }
      
      return $caseTitle;
    }
  }

}
