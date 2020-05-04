<?php

class CRM_CiviMobileAPI_Page_PublicApi_Transform {

  /**
   * Adds 'url' to result
   *
   * @param $apiResult
   *
   * @return mixed
   */
  public static function addEventUrl($apiResult) {
    if (empty($apiResult)) {
      return $apiResult;
    }

    foreach ($apiResult as $key => $apiResultItem) {
      $apiResult[$key]['url'] = CRM_Utils_System::url('civicrm/event/info', 'id=' . $apiResultItem['id'], true);
    }

    return $apiResult;
  }

}
