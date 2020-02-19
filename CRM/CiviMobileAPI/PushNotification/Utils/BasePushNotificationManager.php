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

  /**
   * CRM_CiviMobileAPI_PushNotification_Utils_BasePushNotificationManager constructor.
   *
   * @param $objectName
   * @param $action
   * @param $id
   */
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
    $data = [
      'entity' => $this->objectName,
      'id' => $this->id,
      'body' => $text
    ];

    $isContactExist = isset($contact) && !empty($contact) && !empty($title);

    CRM_Utils_Hook::singleton()
      ->commonInvoke(6, $isContactExist, $listOfContactsID, $this->id, $this->objectName, $text, $title, 'civimobile_send_notification', '');

    if ($isContactExist) {
      CRM_CiviMobileAPI_PushNotification_SaveMessageHelper::saveMessages(
        $contact, $this->id, $this->objectName, $title, $text, $data);
      CRM_CiviMobileAPI_PushNotification_Helper::sendPushNotification(
        $contact, $title, $text, $data);
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
   * Gets title of entity
   *
   * @return string
   */
  protected abstract function getTitle();

}
