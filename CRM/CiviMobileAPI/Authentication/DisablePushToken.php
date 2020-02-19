<?php

use CRM_CiviMobileAPI_Utils_JsonResponse as JsonResponse;

/**
 * @deprecated will be deleted in version 6.0.0
 */
class CRM_CiviMobileAPI_Authentication_DisablePushToken {

  /**
   * Contact id sent in request
   *
   * @var int
   */
  public $contactId;

  /**
   * Token sent in request
   *
   * @var string
   */
  public $token;

  /**
   * Platform in from request
   *
   * @var string
   */
  public $platform;

  /**
   * CRM_CiviMobileAPI_Authentication_DisablePushToken constructor.
   *
   * @param $request
   */
  public function __construct($request) {
    $this->contactId = $request->contactid;
    $this->token = $request->token;
    $this->platform = $request->platform;
  }

  /**
   * Launch process of disabling of push token
   */
  public function run() {
    $this->disablePushNotification();
    $this->sendResponse();
  }

  /**
   *  Disables push notifications
   */
  private function disablePushNotification() {
    $pushNotificationRow = $this->getPushNotificationRow();

    $pushNotificationRow['is_active'] = 0;

    CRM_CiviMobileAPI_BAO_PushNotification::create($pushNotificationRow);
  }

  /**
   * Gets notifications row
   *
   * @return array|null
   */
  private function getPushNotificationRow() {
    $listOfSearchedParameters = [
      'contact_id' => $this->contactId,
      'token' => $this->token,
      'platform' => $this->platform,
    ];

    $pushNotificationRow = array_shift(CRM_CiviMobileAPI_BAO_PushNotification::getAll($listOfSearchedParameters));

    if (!isset($pushNotificationRow['id']) || empty($pushNotificationRow['id'])) {
      JsonResponse::sendErrorResponse(ts('No such id'));
    }

    return $pushNotificationRow;
  }

  /**
   * Sends response
   */
  private function sendResponse() {
    $data['values'] = [
      'status' => 'ok',
    ];

    JsonResponse::sendSuccessResponse($data);
  }

}
