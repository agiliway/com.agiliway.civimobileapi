<?php

use CRM_CiviMobileAPI_Utils_JsonResponse as JsonResponse;

class CRM_CiviMobileAPI_Authentication_Login {

  /**
   * Email sent from user
   *
   * @var string
   */
  public $emailOrUsername;

  /**
   * Password sent from user
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
   * CiviCrm contact assign drupal contact
   *
   * @var \CRM_Contact_BAO_Contact
   */
  public $civiContact;


  /**
   * Reference to null object
   *
   * @var null
   */
  private $nullObject;

  /**
   * Response which was sent to user
   *
   * @var array
   */
  private $responseData;

  public function __construct($request) {
    $this->emailOrUsername = $request->emailOrUsername;
    $this->password = $request->password;
    $this->civiContact = $request->civiContact;
    $this->drupalContactId = $request->drupalContactId;
    $this->nullObject = CRM_Utils_Hook::$_nullObject;
  }

  /**
   *  Launch sending response process
   */
  public function run() {
    $this->invokePreResponseHook();
    $this->sendResponse();
    $this->invokePostResponseHook();
  }

  /**
   * Invokes pre response hooks
   */
  private function invokePreResponseHook() {
    CRM_Utils_Hook::singleton()
      ->commonInvoke(1, $this->drupalContactId, $this->nullObject, $this->nullObject, $this->nullObject, $this->nullObject, $this->nullObject, 'civimobile_auth_pre', '');
  }

  /**
   *  Sends response to user
   */
  private function sendResponse() {
    $restPath = new CRM_CiviMobileAPI_Utils_RestPath();
    $this->responseData['values'] = [
      'api_key' => $this->getUserApiKey(),
      'key' => $this->getSiteKey(),
      'id' => $this->civiContact->id,
      'display_name' => $this->civiContact->display_name,
      'cms' => CRM_CiviMobileAPI_Utils_CmsUser::getInstance()->getSystem(),
      'rest_path' => $restPath->get(),
      'absolute_rest_url' => $restPath->getAbsoluteUrl(),
      'site_name' => CRM_CiviMobileAPI_Utils_Extension::getSiteName(),
    ];

    JsonResponse::sendSuccessResponse($this->responseData);
  }

  /**
   * Gets user API Key
   *
   * @return string
   */
  private function getUserApiKey() {
    $apiKey = $this->civiContact->api_key ? $this->civiContact->api_key : $this->setApiKey($this->civiContact->id);
    if (!$apiKey) {
      JsonResponse::sendErrorResponse(ts('Something went wrong, we can not create the API KEY'));
    }

    return $apiKey;
  }

  /**
   * Generates and saves new api key for user
   *
   * @param $uid
   *
   * @return bool|string
   */
  public static function setApiKey($uid) {
    try {
      $bytes = openssl_random_pseudo_bytes(10);
      $api_key = bin2hex($bytes);
      civicrm_api3('Contact', 'create', [
        'id' => $uid,
        'api_key' => $api_key,
      ]);
    } catch (Exception $e) {
      $api_key = FALSE;
    }
    return $api_key;
  }

  /**
   * Gets CiviCRM Site Key
   *
   * @return string
   */
  private function getSiteKey() {
    return CIVICRM_SITE_KEY;
  }

  /**
   * Invokes post response hook
   */
  private function invokePostResponseHook() {
    CRM_Utils_Hook::singleton()
      ->commonInvoke(4, $this->responseData, $this->emailOrUsername, $this->password, $this->civiContact->id, $nullObject, $nullObject, 'civimobile_auth_success', '');
  }

}
