<?php

class CRM_CiviMobileAPI_PushNotification_Utils_Hook_PostProcess_CasePushNotification extends CRM_CiviMobileAPI_PushNotification_Utils_BasePushNotificationManager {

  /**
   * List of actions text
   *
   * @var array
   */
  private $actionText = [
    'create' => '%display_name has created activity.',
    'edit' => '%display_name has edited activity.',
    'delete' => '%display_name has deleted activity.',
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
    if ($this->id) {
      try {
        $caseTitle = civicrm_api3('Case', 'getvalue', [
          'return' => 'subject',
          'id' => $this->id,
        ]);
      } catch (Exception $e) {
        $caseTitle = NULL;
      }
    }

    return (!empty($caseTitle)) ?  $caseTitle : ts('Activity');
  }

  /**
   * @inheritdoc
   */
  protected function getText() {
    return isset($this->actionText[$this->action]) ? ts($this->actionText[$this->action]) : $this->action;
  }

}
