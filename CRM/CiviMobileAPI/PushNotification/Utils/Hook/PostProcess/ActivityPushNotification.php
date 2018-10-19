<?php

class CRM_CiviMobileAPI_PushNotification_Utils_Hook_PostProcess_ActivityPushNotification extends CRM_CiviMobileAPI_PushNotification_Utils_BasePushNotificationManager {

  /**
   * List of actions text
   *
   * @var array
   */
  private $actionText = [
    "create" => 'New activity',
    "edit" => 'Activity details updated',
  ];

  /**
   * @inheritdoc
   */
  protected function getContact() {
    switch ($this->action) {
      case "create":
        return CRM_CiviMobileAPI_PushNotification_Helper::getActivityContacts($this->id);
      break;

      case "edit":
        return CRM_CiviMobileAPI_PushNotification_Helper::getActivityContacts($this->id, TRUE);
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
    return CRM_Utils_Request::retrieve('subject', 'String');
  }

}
