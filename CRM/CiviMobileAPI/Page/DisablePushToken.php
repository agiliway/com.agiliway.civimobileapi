<?php

use CRM_CiviMobileAPI_Utils_Request as Request;
use CRM_CiviMobileAPI_Utils_JsonResponse as JsonResponse;

/**
 * Provides token disabling functionality for CiviMobile application
 */
class CRM_CiviMobileAPI_Page_DisablePushToken extends CRM_Core_Page {

  /**
   * Contact id sent in request
   *
   * @var int
   */
  public $contactId;

  /**
   * User Push Token sent in request
   *
   * @var string
   */
  public $token;

  /**
   * Platform in request
   *
   * @var string
   */
  public $platform;

  /**
   * CRM_CiviMobileAPI_Page_DisablePushToken constructor.
   */
  public function __construct() {
    civimobileapi_secret_validation();

    $this->contactId = $this->getContactId();
    $this->token = $this->getToken();
    $this->platform = $this->getPlatform();
    parent::__construct();
  }

  /**
   * Gets contact from request
   *
   * @return string|null
   */
  private function getContactId() {
    $contactId = Request::getInstance()->post('contact_id', 'String');
    if (!$contactId) {
      JsonResponse::sendErrorResponse(ts('Required field'), 'contact_id');
    }

    return $contactId;
  }

  /**
   * Gets token from request
   *
   * @return string|null
   */
  private function getToken() {
    $token = Request::getInstance()->post('token', 'String');
    if (!$token) {
      JsonResponse::sendErrorResponse(ts('Required field'), 'token');
    }

    return $token;
  }

  /**
   * Gets platform from request
   *
   * @return string|null
   */
  private function getPlatform() {
    $platform = Request::getInstance()->post('platform', 'String');
    if (!$platform) {
      JsonResponse::sendErrorResponse(ts('Required field'), 'platform');
    }

    return $platform;
  }

  /**
   * Checks If request is valid and launch disabling push token
   */
  public function run() {
    if (CRM_CiviMobileAPI_Authentication_AuthenticationHelper::isRequestValid()) {
      (new CRM_CiviMobileAPI_Authentication_DisablePushToken($this))->run();
    }
  }

}
