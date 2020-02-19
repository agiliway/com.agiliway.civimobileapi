<?php

use CRM_CiviMobileAPI_Utils_Request as Request;
use CRM_CiviMobileAPI_Utils_JsonResponse as JsonResponse;

/**
 * Provides authentication functionality for CiviMobile application
 */
class CRM_CiviMobileAPI_Page_Auth extends CRM_Core_Page {

  /**
   * Email or Username sent in request
   *
   * @var string
   */
  public $emailOrUsername;

  /**
   * Password sent in request
   *
   * @var string
   */
  public $password;

  /**
   * Drupal Id which related to email and password
   *
   * @var int
   */
  public $drupalContactId;

  /**
   * CiviCrm contact assigns to drupal contact
   *
   * @var \CRM_Contact_BAO_Contact
   */
  public $civiContact;

  /**
   * CRM_CiviMobileAPI_Page_Auth constructor.
   */
  public function __construct() {
    civimobileapi_secret_validation();

    $this->emailOrUsername = $this->getEmailOrUsername();
    $this->password = $this->getPassword();
    $this->drupalContactId = CRM_CiviMobileAPI_Authentication_AuthenticationHelper::getDrupalUserIdByMailAndPassword($this->emailOrUsername, $this->password);

    if ($this->isBlocked()) {
      JsonResponse::sendErrorResponse('User is blocked', 'email', 'cms_user_is_blocked');
    }

    $this->civiContact = CRM_CiviMobileAPI_Authentication_AuthenticationHelper::getCiviContact($this->drupalContactId);

    if (CRM_CiviMobileAPI_Utils_Contact::isBlockedApp($this->civiContact->id) == 1) {
      JsonResponse::sendErrorResponse('App is blocked for this user.', 'email', 'application_access_is_blocked');
    }
    parent::__construct();
  }

  /**
   * Gets email from request
   *
   * @return string|null
   */
  private function getEmailOrUsername() {
    $emailOrUsername = Request::getInstance()->post('email', 'String');
    if (!$emailOrUsername) {
      JsonResponse::sendErrorResponse(ts('Required field'), 'email');
    }

    return $emailOrUsername;
  }

  /**
   * Gets password from request
   *
   * @return string|null
   */
  private function getPassword() {
    $password = Request::getInstance()->post('password', 'String');
    if (!$password) {
      JsonResponse::sendErrorResponse(ts('Required field'), 'password');
    }

    return $password;
  }

  /**
   * Checks have user blocked status
   *
   * @return bool
   */
  private function isBlocked() {
    $user = CRM_CiviMobileAPI_Utils_CmsUser::getInstance()->searchAccount($this->emailOrUsername);
    $currentCMS = CRM_CiviMobileAPI_Utils_CmsUser::getInstance()->getSystem();
    $isBlocked = FALSE;

    switch ($currentCMS) {
      case CRM_CiviMobileAPI_Utils_CmsUser::CMS_JOOMLA:
        if ($user->block == 1) {
          $isBlocked = TRUE;
        }
        break;
      case CRM_CiviMobileAPI_Utils_CmsUser::CMS_DRUPAL6:
      case CRM_CiviMobileAPI_Utils_CmsUser::CMS_DRUPAL7:
        if ($user->status == 0) {
          $isBlocked = TRUE;
        }
        break;
    }

    return $isBlocked;
  }

  /**
   * Checks If request is valid and launch preparing user data
   */
  public function run() {
    if (CRM_CiviMobileAPI_Authentication_AuthenticationHelper::isRequestValid()) {
      (new CRM_CiviMobileAPI_Authentication_Login($this))->run();
    }
  }

}
