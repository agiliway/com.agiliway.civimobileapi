<?php

class CRM_CiviMobileAPI_ApiWrapper_Event implements API_Wrapper {

  public function fromApiInput($apiRequest) {
    if(is_mobile_request()) {
      $apiRequest['params']['check_permissions'] = 0;
    }
    return $apiRequest;
  }

  /**
   * Adds url field
   *
   * @param $apiRequest
   * @param $result
   *
   * @return array
   */
  public function toApiOutput($apiRequest, $result) {
  	if($apiRequest['action'] == 'getsingle'){
  		$result['url'] = CRM_Utils_System::url('civicrm/event/info', 'id=' . $result['id'], true);
  	}
    return $result;
  }
}
