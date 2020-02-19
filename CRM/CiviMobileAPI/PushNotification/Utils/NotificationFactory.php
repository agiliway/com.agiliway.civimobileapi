<?php

/**
 * Class based on 'Factory' Pattern. Creates manager of notification for different entities
 */
class CRM_CiviMobileAPI_PushNotification_Utils_NotificationFactory {

  /**
   * Action type
   *
   * @var string
   */
  private $action;

  /**
   * Name of the alter entity
   *
   * @var string
   */
  private $objectName;

  /**
   * Id of the entity
   *
   * @var int
   */
  private $objectId;

  /**
   * Entity instance
   *
   * @var object
   */
  private $objectRef;

  /**
   * Title of transmitter hook
   *
   * @var string
   */
  private $hookContext;

  /**
   * CRM_CiviMobileAPI_PushNotification_Utils_NotificationFactory constructor.
   *
   * @param $action
   * @param $objectName
   * @param $objectId
   * @param $objectRef
   * @param $hookContext
   */
  public function __construct($action, $objectName, $objectId, $objectRef, $hookContext) {
    $this->action = $action;
    $this->objectName = $objectName;
    $this->objectId = $objectId;
    $this->objectRef = $objectRef;
    $this->hookContext = $hookContext;
  }

  /**
   * Converts post process action to appropriate for factory type
   *
   * @param object $form
   *
   * @return string
   */
  public static function convertPostProcessAction($form) {
    $action = $form->getAction();

    switch ($action) {
      case CRM_Core_Action::ADD:
        $action = "create";
        break;
      case CRM_Core_Action::UPDATE:
        $action = "edit";
        break;
      case CRM_Core_Action::DELETE:
        $action = "delete";
        break;
    }

    return $action;
  }

  /**
   * Converts post process form name to appropriate for factory type
   *
   * @param string $formName
   *
   * @return string
   */
  public static function convertPostProcessFormName($formName) {
    switch ($formName) {
      case 'CRM_Activity_Form_Activity':
        break;

      case 'CRM_Case_Form_Activity':
        $formName = 'ActivityInCase';
        break;
    }

    return $formName;
  }

  /**
   * Gets notification manager depends from entity name
   *
   * @return \CRM_CiviMobileAPI_PushNotification_Utils_BasePushNotificationManager
   * @throws \Exception
   */
  public function getPushNotificationManager() {
    $notificationManager = NULL;

    switch ($this->objectName) {
      case 'Case':
        $notificationManager = $this->getCaseNotification();
        break;

      case 'Relationship':
        $notificationManager = $this->getRelationshipNotification();
        break;

      case 'Participant':
        $notificationManager = $this->getParticipantNotification();
        break;

      case 'Activity':
        $notificationManager = $this->getActivityNotification();
        break;

      case 'ActivityInCase':
        $notificationManager = $this->getActivityInCaseNotification();
        break;
    }

    return $notificationManager;
  }

  /**
   * Gets case notification manager
   *
   * @return \CRM_CiviMobileAPI_PushNotification_Utils_Hook_Post_CasePushNotification|\CRM_CiviMobileAPI_PushNotification_Utils_Hook_Pre_CasePushNotification
   */
  private function getCaseNotification() {
    $notificationManager = NULL;
    if ($this->hookContext === "post") {

      $notificationManager = new CRM_CiviMobileAPI_PushNotification_Utils_Hook_Post_CasePushNotification($this->objectName, $this->action, $this->objectId);
      $notificationManager->setObjectRef($this->objectRef);

    } elseif ($this->hookContext === "pre" && $this->action === 'delete') {
       $notificationManager = new CRM_CiviMobileAPI_PushNotification_Utils_Hook_Pre_CasePushNotification($this->objectName, $this->action, $this->objectId);
    }

    return $notificationManager;
  }

  /**
   * Gets relationship notification manager
   *
   * @return \CRM_CiviMobileAPI_PushNotification_Utils_Hook_Post_CasePushNotification|\CRM_CiviMobileAPI_PushNotification_Utils_Hook_Post_RelationshipPushNotification|null
   * @throws \Exception
   */
  private function getRelationshipNotification() {
    $notificationManager = NULL;

    if ($this->hookContext === "post") {
      if (!empty($this->objectRef->case_id)) {
        $caseObjectRef = CRM_Case_BAO_Case::findById($this->objectRef->case_id);

        $notificationManager = new CRM_CiviMobileAPI_PushNotification_Utils_Hook_Post_CasePushNotification("Case", "edit", $this->objectRef->case_id);
        $notificationManager->setObjectRef($caseObjectRef);
      }
      else {
        $notificationManager = new CRM_CiviMobileAPI_PushNotification_Utils_Hook_Post_RelationshipPushNotification($this->objectName, $this->action, $this->objectId);
        $notificationManager->setObjectRef($this->objectRef);
      }
    }

    return $notificationManager;
  }

  /**
   * Gets participant notification manager
   *
   * @return \CRM_CiviMobileAPI_PushNotification_Utils_Hook_Post_ParticipantPushNotification|\CRM_CiviMobileAPI_PushNotification_Utils_Hook_Pre_ParticipantPushNotification|null
   */
  private function getParticipantNotification() {
    $notificationManager = NULL;

    if ($this->hookContext === "post") {
      $notificationManager = new CRM_CiviMobileAPI_PushNotification_Utils_Hook_Post_ParticipantPushNotification($this->objectName, $this->action, $this->objectId);
      $notificationManager->setObjectRef($this->objectRef);
    }
    elseif ($this->hookContext === "pre" && $this->action === 'delete') {
      $notificationManager = new CRM_CiviMobileAPI_PushNotification_Utils_Hook_Pre_ParticipantPushNotification($this->objectName, $this->action, $this->objectId);
    }

    return $notificationManager;
  }

  /**
   *  Gets activity notification manager
   *
   * @return \CRM_CiviMobileAPI_PushNotification_Utils_Hook_PostProcess_ActivityPushNotification|\CRM_CiviMobileAPI_PushNotification_Utils_Hook_Pre_ActivityPushNotification|null
   */
  private function getActivityNotification() {
    $notificationManager = NULL;

    if ($this->hookContext === "pre" && $this->action === 'delete') {
      $notificationManager = new CRM_CiviMobileAPI_PushNotification_Utils_Hook_Pre_ActivityPushNotification($this->objectName, $this->action, $this->objectId);
    } else {
      $objName = 'Activity';

      if (is_object($this->objectRef)) {
        $activityId = isset($this->objectRef->_activityId) ? $this->objectRef->_activityId : $this->objectRef->id;
        $notificationManager = new CRM_CiviMobileAPI_PushNotification_Utils_Hook_PostProcess_ActivityPushNotification($objName, $this->action, $activityId);
      }
    }

    return $notificationManager;
  }

  /**
   *  Gets activity in case notification manager
   *
   * @return \CRM_CiviMobileAPI_PushNotification_Utils_Hook_PostProcess_CasePushNotification
   */
  private function getActivityInCaseNotification() {
    $notificationManager = NULL;

    if ($this->hookContext === "post") {
      $objName = 'Case';
      $notificationManager = new CRM_CiviMobileAPI_PushNotification_Utils_Hook_PostProcess_CasePushNotification($objName, $this->action, array_shift($this->objectRef->_caseId));
    } elseif ($this->hookContext === "postProcess" && $this->action === 'delete') {
      $notificationManager = new CRM_CiviMobileAPI_PushNotification_Utils_Hook_PostProcess_CasePushNotification($this->objectName, $this->action, $this->objectId);
    }

    if ($this->hookContext === "postProcess") {
      $notificationManager = new CRM_CiviMobileAPI_PushNotification_Utils_Hook_PostProcess_CasePushNotification($this->objectName, $this->action, array_shift($this->objectRef->_caseId));
    }

    return $notificationManager;
  }

}
