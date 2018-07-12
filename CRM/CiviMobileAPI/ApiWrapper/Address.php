<?php

class CRM_CiviMobileAPI_ApiWrapper_Address implements API_Wrapper {

  public function fromApiInput($apiRequest) {
    if(is_mobile_request()) {
      if((isset($apiRequest['params']['entity_table']) && $apiRequest['params']['entity_table'] == 'civicrm_contact') ||
        (isset($apiRequest['params']['api.has_parent']) && $apiRequest['params']['api.has_parent'])) {
        $apiRequest['params']['check_permissions'] = 0;
      }
    }
    return $apiRequest;
  }

  public function toApiOutput($apiRequest, $result) {
    return $result;
  }
}
