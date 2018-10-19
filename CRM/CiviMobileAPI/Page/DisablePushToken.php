<?php

use CRM_CiviMobileAPI_Utils_Request as Request;
use CRM_CiviMobileAPI_Utils_CmsUser as CmsUser;
use CRM_CiviMobileAPI_Utils_JsonResponse as JsonResponse;

/**
 * Provides token disabling functionality for CiviMobile application
 */
class CRM_CiviMobileAPI_Page_DisablePushToken extends CRM_Core_Page {

  /**
   * Number of attempts
   */
  const ATTEMPT = 3;

  /**
   * For how many minutes block the request
   */
  const BLOCK_MINUTES = 1;

  /**
   * Handles the request and prepares all contact information for response
   *
   * @return null|void
   */
  public function run() {
    civimobileapi_secret_validation();

    if (!CmsUser::getInstance()->validateCMS()) {
      JsonResponse::sendErrorResponse(ts('Sorry, but CiviMobile are not supporting your system yet.'));
    }

    if (!$this->validateAttempts()) {
      JsonResponse::sendErrorResponse(ts('You are blocked for a %1 min. Please try again later', [1 => self::BLOCK_MINUTES]));
    }

    $contactId = Request::getInstance()->post('contact_id', 'String');
    if (!$contactId) {
      JsonResponse::sendErrorResponse(ts('Required field'), 'contact_id');
    }

    $token = Request::getInstance()->post('token', 'String');
    if (!$token) {
      JsonResponse::sendErrorResponse(ts('Required field'), 'token');
    }

    $platform = Request::getInstance()->post('platform', 'String');
    if (!$platform) {
      JsonResponse::sendErrorResponse(ts('Required field'), 'platform');
    }

    $listOfSearchedParameters = [
      'contact_id' => $contactId,
      'token' => $token,
      'platform' => $platform,
    ];

    $pushNotificationRow = array_shift(CRM_CiviMobileAPI_BAO_PushNotification::getAll($listOfSearchedParameters));

    if (!isset($pushNotificationRow['id']) || empty($pushNotificationRow['id'])) {
      JsonResponse::sendErrorResponse(ts('No such id'));
    }
    $pushNotificationRow['is_active'] = 0;

    CRM_CiviMobileAPI_BAO_PushNotification::create($pushNotificationRow);

    $data['values'] = [
      'status' => 'ok',
    ];

    JsonResponse::sendSuccessResponse($data);
  }

  /**
   * Save the number of attempts and block the request
   *
   * @return bool
   */
  private function validateAttempts() {
    //TODO: save the number of attempts and block the request
    return TRUE;
  }
}