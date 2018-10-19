<?php

class CRM_CiviMobileAPI_PushNotification_Helper {

  /**
   * Server Key Token
   */
  const SERVER_KEY = '';

  /**
   * Url to Firebase Cloud Massaging
   */
  const FCM_URL = 'https://fcm.googleapis.com/fcm/send';

  /**
   * Sends push notification
   *
   * @param $contactsID
   * @param $text
   *
   * @return bool|mixed
   */
  public static function sendPushNotification($contactsID, $title, $text) {
    //TODO: finish the push token functionality
    return true;
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
   * @return array
   *
   */
  public static function deleteCustomGroup($customGroup) {
    $customGroupID = civicrm_api3('CustomGroup', 'get', ['return' => "id", 'name' => $customGroup]);
    if(!empty($customGroupID["values"])){
      $id = array_shift($customGroupID['values'])['id'];
      civicrm_api3('CustomGroup', 'delete', ['id' => $id]);
    }
  }


}
