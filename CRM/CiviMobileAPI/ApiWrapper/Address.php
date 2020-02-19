<?php

/**
 * @deprecated will be deleted in version 7.0.0
 */
class CRM_CiviMobileAPI_ApiWrapper_Address implements API_Wrapper {

  /**
   * Interface for interpreting api input
   *
   * @param array $apiRequest
   *
   * @return array
   */
  public function fromApiInput($apiRequest) {
    if (is_mobile_request()) {
      if ((isset($apiRequest['params']['entity_table']) && $apiRequest['params']['entity_table'] == 'civicrm_contact') ||
        (isset($apiRequest['params']['api.has_parent']) && $apiRequest['params']['api.has_parent'])) {
        $apiRequest['params']['check_permissions'] = 0;
      }
    }

    return $apiRequest;
  }

  /**
   * Interface for interpreting api output
   *
   * @param $apiRequest
   * @param $result
   *
   * @return array
   */
  public function toApiOutput($apiRequest, $result) {
    return $result;
  }

}
