<?php

use CRM_CiviMobileAPI_Utils_CmsUser as CmsUser;
use CRM_CiviMobileAPI_Utils_JsonResponse as JsonResponse;

class CRM_CiviMobileAPI_Authentication_AuthenticationHelper {

  /**
   * Number of attempts
   */
  const ATTEMPT = 3;

  /**
   * For how many minutes block the request
   */
  const BLOCK_MINUTES = 1;

  /**
   * Gets Civi User Contact assigns to Drupal account
   *
   * @param $drupalUserId
   *
   * @return \CRM_Contact_BAO_Contact
   *
   */
  public static function getCiviContact($drupalUserId) {
    $contact = static::findContact($drupalUserId);
    if (!$contact) {
      JsonResponse::sendErrorResponse(ts('There are no such contact in CiviCRM'));
    }

    return $contact;
  }

  /**
   * Finds Contact in CiviCRM
   *
   * @param $drupalUserId
   *
   * @return \CRM_Contact_BAO_Contact
   *
   */
  private static function findContact($drupalUserId) {
    $contact = new CRM_Contact_BAO_Contact();
    $contact->get('id', static::findContactRelation($drupalUserId));

    return $contact;
  }

  /**
   * Finds CiviCRM Contact id within relation
   *
   * @param $uid
   *
   * @return CRM_Contact_BAO_Contact
   */
  private static function findContactRelation($uid) {
    try {
      $ufMatch = civicrm_api3('UFMatch', 'get', [
        'uf_id' => $uid,
        'sequential' => 1,
      ]);
      $contactId = $ufMatch ['values'][0]['contact_id'];
    } catch (Exception $e) {
      $contactId = FALSE;
    }

    return $contactId;
  }

  /**
   * Checks if Request is valid
   *
   * @return bool
   */
  public static function isRequestValid() {
    return (static::validateCms() && static::validateAttempts());
  }

  /**
   * Checks if CMS is valid
   *
   * @return bool
   */
  private static function validateCms() {
    if (CmsUser::getInstance()->validateCMS()) {
      return TRUE;
    }
    else {
      JsonResponse::sendErrorResponse(ts('Sorry, but CiviMobile are not supporting your system yet.'));
      return FALSE;
    }
  }

  /**
   * Saves the number of attempts and block the request
   *
   * @return bool
   */
  private static function validateAttempts() {
    if (TRUE) {
      return TRUE;
    }
    else {
      JsonResponse::sendErrorResponse(ts('You are blocked for a %1 min. Please try again later', [1 => self::BLOCK_MINUTES]));
      return FALSE;
    }
  }

  /**
   * Gets drupal user id by email and password
   *
   * @param $email
   * @param $password
   *
   * @return int|null
   */
  public static function getDrupalUserIdByMailAndPassword($email, $password) {
    $cmsUserId = CmsUser::getInstance()->validateAccount($email, $password);

    if ($cmsUserId === FALSE) {
      JsonResponse::sendErrorResponse(ts('Wrong email or password'));
    }
    
    return $cmsUserId;
  }

  /**
   * Gets drupal user id by email or user name
   *
   * @return int|null
   */
  public static function getDrupalUserIdByUsernameOrEmail($emailOrUsername) {
    $userAccount = CmsUser::getInstance()->searchAccount($emailOrUsername);

    if (!isset($userAccount) && empty($userAccount)) {
      JsonResponse::sendErrorResponse(ts('Wrong email/login'), 'email_or_username');
    }

    return $userAccount->uid;
  }

}
