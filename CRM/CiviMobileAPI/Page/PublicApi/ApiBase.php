<?php

use CRM_CiviMobileAPI_Utils_JsonResponse as JsonResponse;

abstract class CRM_CiviMobileAPI_Page_PublicApi_ApiBase extends CRM_Core_Page {

  /**
   * Api settings
   *
   * @var array
   */
  public $apiSettings = [];

  /**
   * Json data from request
   *
   * @var array
   */
  public $requestJsonData = [];

  /**
   * Action name from request
   *
   * @var array
   */
  public $requestAction = '';

  /**
   * Entity name from request
   *
   * @var array
   */
  public $requestEntity = '';

  public function __construct() {
    civimobileapi_secret_validation();
    if (!CRM_CiviMobileAPI_Authentication_AuthenticationHelper::isRequestValid()) {
      JsonResponse::sendErrorResponse(ts('Not valid request'));
    }

    $this->requestJsonData = $this->findRequestJsonData();
    $this->requestEntity = $this->findRequestEntityName();
    $this->requestAction = $this->findRequestActionName();

    parent::__construct();
  }

  /**
   * Checks If request is valid and launch preparing user data
   */
  public function run() {
    $result = [];
    $apiSettings = $this->findApiSettings();
    $actionSettings = $this->findActionSettings($apiSettings);
    $this->checkActionPermissions($actionSettings);
    $this->requestJsonData = $this->cleanJsonData($actionSettings, $this->requestJsonData);
    $this->requestJsonData = $this->runMiddleware($actionSettings, $this->requestJsonData);

    try {
      $result = civicrm_api3($this->requestEntity, $this->requestAction,
        array_merge($this->requestJsonData, ["sequential" => 1])
      );
    } catch (CiviCRM_API3_Exception $e) {
      JsonResponse::sendErrorResponse(ts('Api error.') . ' Error message: ' . $e->getMessage() ,'entityData', $e->getErrorCode());
    }

    if (!empty($result['values'])) {
      foreach ($result['values'] as $key => $item) {
        $result['values'][$key] = $this->cleanResponse($item, $actionSettings);
      }
    }

    $result['values'] = $this->runTransforms($result['values'], $actionSettings);

    JsonResponse::sendSuccessResponse($result);
  }

  /**
   * Finds entity name from request
   *
   * @return string
   */
  function findRequestEntityName() {
    if (empty($_POST['entityName'])) {
      JsonResponse::sendErrorResponse(ts("The 'entityName' field is required field."), 'entityName');
    }

    return (string) $_POST['entityName'];
  }

  /**
   * Finds Json data from request
   *
   * @return mixed
   */
  private function findRequestJsonData() {
    if (!isset($_POST['entityData'])) {
      JsonResponse::sendErrorResponse(ts("The 'entityData' field is required field."), 'entityData');
    }

    $jsonData = json_decode($_POST['entityData'], true);
    if (json_last_error() != JSON_ERROR_NONE) {
      JsonResponse::sendErrorResponse(ts("The 'entityData' has not valid json."), 'entityData');
    }

    return $jsonData;
  }

  /**
   * Finds name of action from request
   *
   * @return string
   */
  private function findRequestActionName() {
    if (empty($_POST['actionName'])) {
      JsonResponse::sendErrorResponse(ts("The 'actionName' field is required field."), 'actionName');
    }

    return (string) $_POST['actionName'];
  }

  /**
   * Finds api settings by name
   */
  private function findApiSettings() {
    $apiSettings = [];
    foreach ($this->apiSettings as $settings) {
      if ($settings['entityName'] === $this->requestEntity) {
        $apiSettings = $settings;
      }
    }

    if (empty($apiSettings)) {
      JsonResponse::sendErrorResponse(ts("Value '%1' is not allow entity name.", [1 => $this->requestEntity]), 'entityName');
    }

    return $apiSettings;
  }

  /**
   * Finds action settings by name
   *
   * @param $apiSettings
   *
   * @return array
   */
  private function findActionSettings($apiSettings) {
    $actionSettings = [];
    foreach ($apiSettings['availableActions'] as $settings) {
      if ($settings['actionName'] === $this->requestAction) {
        $actionSettings = $settings;
      }
    }

    if (empty($actionSettings)) {
      JsonResponse::sendErrorResponse(ts("Value '%1' is not allow action name.", [1 => $this->requestAction]), 'actionName');
    }

    return $actionSettings;
  }

  /**
   * Checks action's permissions
   *
   * @param $actionSettings
   */
  private function checkActionPermissions($actionSettings) {
    if (!empty($actionSettings['actionPermissions'])) {
      foreach ($actionSettings['actionPermissions'] as $permission) {
        if (!CRM_Core_Permission::check($permission)) {
          JsonResponse::sendErrorResponse(ts("'1%' Permission denied.", [1 => $permission]));
        }
      }
    }
  }

  /**
   * Runs middleware
   *
   * @param $actionSettings
   * @param $requestJsonData
   *
   * @return array
   */
  private function runMiddleware($actionSettings, $requestJsonData) {
    if (empty($actionSettings['middleware'])) {
      return $requestJsonData;
    }

    foreach ($actionSettings['middleware'] as $middleware) {
      try {
        $requestJsonData = forward_static_call($middleware['class'] . "::" . $middleware['method'], $requestJsonData);
      } catch (Exception $e) {
        JsonResponse::sendErrorResponse(ts($e->getMessage()));
      }
    }

    return $requestJsonData;
  }

  /**
   * Removes not allow fields
   *
   * @param $actionSettings
   * @param $requestJsonData
   *
   * @return array
   */
  private function cleanJsonData($actionSettings, $requestJsonData) {
    if (empty($actionSettings['availableParams'])) {
      return $requestJsonData;
    }

    $cleanParams = [];

    foreach ($actionSettings['availableParams'] as $paramName) {
      if (isset($requestJsonData[$paramName])) {
        $cleanParams[$paramName] = $requestJsonData[$paramName];
      }
    }

    return $cleanParams;
  }

  /**
   * Removes not allow fields
   *
   * @param $item
   * @param $actionSettings
   * @return array
   */
  private function cleanResponse($item, $actionSettings) {
    if (empty($actionSettings['availableReturnFields'])) {
      return [];
    }

    $cleanItem = [];

    foreach ($actionSettings['availableReturnFields'] as $fieldName) {
      if (isset($item[$fieldName])) {
        $cleanItem[$fieldName] = $item[$fieldName];
      }
    }

    return $cleanItem;
  }

  /**
   * Runs transforms
   *
   * @param $apiResult
   * @param $actionSettings
   * @return mixed
   */
  private function runTransforms($apiResult, $actionSettings) {
    if (empty($actionSettings['transforms'])) {
      return $apiResult;
    }

    foreach ($actionSettings['transforms'] as $middleware) {
      try {
        $apiResult = forward_static_call($middleware['class'] . "::" . $middleware['method'], $apiResult);
      } catch (Exception $e) {
        JsonResponse::sendErrorResponse(ts($e->getMessage()));
      }
    }

    return $apiResult;
  }

}
