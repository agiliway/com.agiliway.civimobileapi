<?php

/**
 * @deprecated will be deleted in version 7.0.0
 */
class CRM_CiviMobileAPI_ApiWrapper_Job_VersionCheck implements API_Wrapper {

  /**
   * Interface for interpreting api input.
   *
   * @param array $apiRequest
   *
   * @return array
   */
  public function fromApiInput($apiRequest) {
    return $apiRequest;
  }

  /**
   * Interface for interpreting api output.
   *
   * @param array $apiRequest
   * @param array $result
   *
   * @return array
   * @throws \CRM_Extension_Exception
   * @throws \Exception
   */
  public function toApiOutput($apiRequest, $result) {
    if (Civi::settings()->get('civimobile_auto_update')) {
      if (CRM_CiviMobileAPI_Utils_VersionController::getInstance()->isCurrentVersionLowerThanRepositoryVersion()) {
        CRM_CiviMobileAPI_Utils_Extension::update();
      }
    }

    return $result;
  }

}
