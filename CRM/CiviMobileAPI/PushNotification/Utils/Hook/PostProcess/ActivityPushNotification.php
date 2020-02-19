<?php

class CRM_CiviMobileAPI_PushNotification_Utils_Hook_PostProcess_ActivityPushNotification extends CRM_CiviMobileAPI_PushNotification_Utils_BasePushNotificationManager {

  /**
   * List of actions text
   *
   * @var array
   */
  private $actionText = [
    'create' => '%display_name has created activity.',
    'edit' => '%display_name has edited activity.',
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
    if ($this->id) {
      try {
        $activityTitle = CRM_Core_DAO::getFieldValue('CRM_Activity_BAO_Activity', $this->id, 'subject');
      } catch (Exception $e) {
        $activityTitle = NULL;
      }
    }

    return (!empty($activityTitle)) ?  $activityTitle : ts('Activity');
  }

  /**
   * @inheritdoc
   */
  protected function getText() {
    return isset($this->actionText[$this->action]) ? ts($this->actionText[$this->action]) : $this->action;
  }

}
