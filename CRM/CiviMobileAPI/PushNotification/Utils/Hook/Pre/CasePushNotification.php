<?php

use CRM_CiviMobileAPI_PushNotification_Helper as PushNotification_Helper;

class CRM_CiviMobileAPI_PushNotification_Utils_Hook_Pre_CasePushNotification extends CRM_CiviMobileAPI_PushNotification_Utils_BasePushNotificationManager {

  /**
   * List of actions text
   *
   * @var array
   */
  private $actionText = ['delete' => '%display_name has deleted case.'];

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
    if ($this->action === 'delete' && $this->id) {
      try {
        $caseTitle = civicrm_api3('Case', 'getvalue', ['return' => 'subject', 'id' => $this->id]);
      }
      catch (Exception $e) {
        $caseTitle = NULL;
      }
    }

    return (!empty($caseTitle)) ?  $caseTitle : ts('Case');
  }

  /**
   * @inheritdoc
   */
  protected function getText() {
    return isset($this->actionText[$this->action]) ? ts($this->actionText[$this->action]) : $this->action;
  }

}
