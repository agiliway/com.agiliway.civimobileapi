<?php

/**
 * @deprecated will be deleted in version 7.0.0
 */
class CRM_CiviMobileAPI_ApiWrapper_Membership_Create implements API_Wrapper {

  /**
   * Interface for interpreting api input.
   *
   * @param array $apiRequest
   *
   * @return array
   *
   * @throws \CiviCRM_API3_Exception
   */
  public function fromApiInput($apiRequest) {
    if (!empty($apiRequest['params']['id']) && !empty($apiRequest['params']['renewal'])) {
      CRM_CiviMobileAPI_Utils_Membership::renewal($apiRequest['params']);
    }

    return $apiRequest;
  }

  /**
   * Interface for interpreting api output.
   *
   * @param array $apiRequest
   * @param array $result
   *
   * @return array
   */
  public function toApiOutput($apiRequest, $result) {
    return $result;
  }

}
