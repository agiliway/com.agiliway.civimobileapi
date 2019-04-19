<?php

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
      $currentVersion = CRM_CiviMobileAPI_Utils_Version::getCurrentVersion();
      $latestVersion = CRM_CiviMobileAPI_Utils_Version::getLatestVersion();

      if ($latestVersion > $currentVersion) {
        CRM_CiviMobileAPI_Utils_Version::update($latestVersion);
      }
    }

    return $result;
  }

}
