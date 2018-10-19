<?php

use CRM_CiviMobileAPI_PushNotification_Helper as PushNotification_Helper;

class CRM_CiviMobileAPI_PushNotification_Utils_Hook_Pre_ActivityPushNotification extends CRM_CiviMobileAPI_PushNotification_Utils_BasePushNotificationManager {

  /**
   * @inheritdoc
   */
  protected function getContact() {
    if ($this->action === 'delete') {
      $caseID = $this->getCaseId();

      return empty($caseID['values']) ? PushNotification_Helper::getActivityContacts($this->id, TRUE) : '';
    }
  }

  /**
   * Gets id of current case id
   *
   * @return array
   */
  private function getCaseId() {
    return civicrm_api3('Case', 'get', [
      'return' => 'id',
      'activity_id' => $this->id,
    ]);
  }

  /**
   * @inheritdoc
   */
  protected function getTitle() {
    return ts('Activity removed');
  }

  /**
   * @inheritdoc
   */
  protected function getText() {
    if ($this->action === 'delete' && $this->id) {
      try {
        $activityName = civicrm_api3('Activity', 'getvalue', ['return' => 'subject', 'id' => $this->id]);
      } catch (Exception $e) {
        $activityName = NULL;
      }
      
      return $activityName;
    }
  }

}
