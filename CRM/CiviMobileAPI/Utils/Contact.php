<?php

/**
 * Class provide extension version helper methods
 */
class CRM_CiviMobileAPI_Utils_Contact {

  /**
   * @param int $contactId
   *
   * @throws \CiviCRM_API3_Exception
   */
  public static function logoutFromMobile($contactId) {
    civicrm_api3('Contact', 'create', [
      'id' => $contactId,
      'api_key' => '',
    ]);

    $pushNotification = new CRM_CiviMobileAPI_BAO_PushNotification();
    $pushNotification->contact_id = $contactId;
    $pushNotification->find(TRUE);

    if (isset($pushNotification->id) && !empty($pushNotification->id)) {
      CRM_CiviMobileAPI_BAO_PushNotification::del($pushNotification->id);
    }

    CRM_Core_Session::setStatus(ts('Your Api key has removed and all device disconnected from account.'));

    CRM_Utils_System::redirect($_SERVER['HTTP_REFERER']);
  }

  /**
   * Gets Contact's list of emails
   *
   * @param int $contactId
   *
   * @return array
   */
  public static function getEmails($contactId) {
    $emailList = [];

    if (empty($contactId)) {
      return $emailList;
    }

    try {
      $result = civicrm_api3('Email', 'get', [
        'sequential' => 1,
        'return' => ['email'],
        'contact_id' => $contactId,
        'options' => ['limit' => 0],
      ]);
    } catch (CiviCRM_API3_Exception $e) {
      return $emailList;
    }

    if (empty($result))  {
      return $emailList;
    }

    foreach ($result['values'] as $email) {
      $emailList[] = $email['email'];
    }

    return $emailList;
  }

  /**
   * Gets display_name by Contact id
   *
   * @param int $contactId
   *
   * @return string
   */
  public static function getDisplayName($contactId) {
    if (empty($contactId)) {
      return '';
    }

    try {
      $displayName = civicrm_api3('Contact', 'getvalue', [
        'return' => 'display_name',
        'contact_id' => $contactId
      ]);
    } catch (CiviCRM_API3_Exception $e) {
      return '';
    }

    return $displayName;
  }

}