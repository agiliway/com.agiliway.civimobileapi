<?php

abstract class CRM_CiviMobileAPI_PushNotification_Utils_BasePushNotificationManager {

  /**
   * Object Name
   *
   * @var string
   */
  protected $objectName;

  /**
   * Action type
   *
   * @var mixed
   */
  protected $action;

  /**
   * Entity Id
   *
   * @var int
   */
  protected $id;

  public function __construct($objectName, $action, $id) {
    $this->objectName = $objectName;
    $this->action = $action;
    $this->id = $id;
  }

  /**
   *  Send Push Notification
   */
  public function sendNotification() {
    $contact = $this->getContact();
    $text = $this->getText();
    $title = $this->getTitle();
    $isContactExist = isset($contact) && !empty($contact) && !empty($title);

    if ($isContactExist) {
      CRM_CiviMobileAPI_PushNotification_Helper::sendPushNotification($contact, $title, $text);
    }
  }

  /**
   * Gets contact (single or plural) which related to entity
   *
   * @return int|array
   */
  protected abstract function getContact();

  /**
   * Gets text for push notification
   *
   * @return string
   */
  protected abstract function getText();

  /**
   * Gets name of entity
   *
   * @return string
   */
  protected abstract function getTitle();

}
