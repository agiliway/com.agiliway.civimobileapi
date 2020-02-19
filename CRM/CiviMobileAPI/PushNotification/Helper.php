<?php

class CRM_CiviMobileAPI_PushNotification_Helper {

  /**
   * Url to Firebase Cloud Massaging
   */
  const FCM_URL = 'https://push.civimobile.org/rest.php';

  /**
   * Sends push notification
   *
   * @param array $contactsIDs
   * @param $title
   * @param $text
   * @param $data
   *
   * @return bool|mixed
   */
  public static function sendPushNotification(array $contactsIDs, $title, $text, $data) {
    $contactsTokens = self::getContactsToken($contactsIDs);

    if (empty($contactsTokens) || empty($contactsIDs)) {
      return FALSE;
    }
    $config = &CRM_Core_Config::singleton();
    $baseUrl = $config->userFrameworkBaseURL;

    $title = self::compileMessage($title);
    $text = self::compileMessage($text);

    $notificationBody = [
      'title' => $title,
      'text' => $text,
    ];
    $postFields = [
      'registration_ids' => $contactsTokens['Tokens'],
      'notification' => $notificationBody,
      'priority' => 'high',
      'data' => $data,
    ];
    $requestHeader = [
      'Content-Type:application/json',
      'Site-Name:' . $baseUrl,
      'Authorization:' . Civi::settings()->get('civimobile_server_key'),
    ];

    $nullObject = CRM_Utils_Hook::$_nullObject;
    CRM_Utils_Hook::singleton()
      ->commonInvoke(2, $notificationBody, $requestHeader, $nullObject, $nullObject, $nullObject, $nullObject, 'civimobile_send_push', '');

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, self::FCM_URL);
    curl_setopt($ch, CURLOPT_POST, TRUE);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $requestHeader);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($postFields));
    try {
      $result = curl_exec($ch);
    } catch (Exception $e) {
      $result = FALSE;
    }
    curl_close($ch);

    return $result;
  }

  /**
   * Collects all tokens for contacts if exists
   *
   * @param $listOfContactsID
   *
   * @return array
   */
  private static function getContactsToken($listOfContactsID) {
    $tokens = [];
    foreach ($listOfContactsID as $contactID) {
      $contactTokens = CRM_CiviMobileAPI_BAO_PushNotification::getAll(['contact_id' => $contactID]);

      if (empty($contactTokens)) {
        continue;
      }

      $tokens['Contacts'][] = $contactID;
      foreach ($contactTokens as $contactToken) {
        $tokens['Tokens'][] = $contactToken['token'];
      }
    }

    return $tokens;
  }

  /**
   * Collects all Relationships for Activity
   *
   * @param $caseID
   *
   * @return array
   * @throws \CiviCRM_API3_Exception
   */
  public static function getCaseRelationshipContacts($caseID) {
    $contacts = [];

    $relationship = civicrm_api3('Relationship', 'get', ['case_id' => $caseID]);
    foreach ($relationship['values'] as $contact) {
      if (intval($contact['is_active'])) {
        $contacts[] = $contact['contact_id_a'];
        $contacts[] = $contact['contact_id_b'];
      }
    }

    $contacts = array_unique($contacts);

    return $contacts;
  }

  /**
   * Collects all contacts for Activity
   *
   * @param $activityID
   * @param bool $withSourceContact
   *
   * @return array
   */
  public static function getActivityContacts($activityID, $withSourceContact = FALSE) {
    $contacts = [];
    $activityContacts = CRM_Activity_BAO_ActivityContact::buildOptions('record_type_id', 'validate');

    $targetID = CRM_Utils_Array::key('Activity Targets', $activityContacts);
    $assigneeID = CRM_Utils_Array::key('Activity Assignees', $activityContacts);

    $targetActivityContacts = CRM_Activity_BAO_ActivityContact::getNames($activityID, $targetID, TRUE);
    $contacts['target_contact'] = end($targetActivityContacts);
    $assigneeActivityContacts = CRM_Activity_BAO_ActivityContact::getNames($activityID, $assigneeID, TRUE);
    $contacts['assignee_contact'] = end($assigneeActivityContacts);
    $prepareContacts = array_merge($contacts['target_contact'], $contacts['assignee_contact']);

    if ($withSourceContact) {
      $sourceID = CRM_Utils_Array::key('Activity Source', $activityContacts);
      $sourceActivityContacts = CRM_Activity_BAO_ActivityContact::getNames($activityID, $sourceID, TRUE);
      $contacts['source_contact'] = end($sourceActivityContacts);
      $prepareContacts = array_merge($prepareContacts, $contacts['source_contact']);
    }

    $prepareContacts = array_unique($prepareContacts);

    return $prepareContacts;
  }

  /**
   * Gets Participant for Event registration
   *
   * @param $participantID
   *
   * @return array
   * @throws \CiviCRM_API3_Exception
   */
  public static function getEventContactByParticipantId($participantID) {
    $contacts = [];

    $participants = civicrm_api3('Participant', 'get', [
      'return' => ["contact_id"],
      'id' => $participantID,
    ]);
    foreach ($participants['values'] as $contact) {
      $contacts[] = $contact['contact_id'];
    }

    $contacts = array_unique($contacts);

    return $contacts;
  }

  /**
   * Deletes custom group
   *
   * @param $customGroup
   *
   * @throws \CiviCRM_API3_Exception
   */
  public static function deleteCustomGroup($customGroup) {
    $customGroupID = civicrm_api3('CustomGroup', 'get', [
      'return' => "id",
      'name' => $customGroup,
    ]);
    if (!empty($customGroupID["values"])) {
      $id = array_shift($customGroupID['values'])['id'];
      civicrm_api3('CustomGroup', 'delete', ['id' => $id]);
    }
  }

  /**
   * @param $message
   * @param $contactId
   *
   * @return string
   */
  public static function compileMessage($message, $contactId = NULL) {
    if (empty($contactId)) {
      $contactId = CRM_Core_Session::singleton()->getLoggedInContactID();
    }
    $params = ['id' => $contactId];
    $default = [];
    $contact = CRM_Contact_BAO_Contact::getValues($params, $default);
    $i = 1;
    $replace = [];

    foreach ((array) $contact as $k => $value) {
      if (strpos($message, '%' . $k) !== FALSE) {
        $message = str_replace('%' . $k, '%' . $i, $message);
        $replace[$i] = $value;
        $i++;
      }
    }

    return ts($message, $replace);
  }

}
