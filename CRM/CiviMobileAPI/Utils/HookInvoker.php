<?php

/**
 * This class invokes hooks through the civicrm core hook invoker functionality
 */
class CRM_CiviMobileAPI_Utils_HookInvoker {

  /**
   * This hook customize view of CiviMobileAPI popup with QR-code
   *
   * @param $params
   * @return mixed
   */
  public static function qrCodeBlockParams(&$params) {
    $null = NULL;
    if (version_compare(CRM_Utils_System::version(), '4.5', '<')) {
      return CRM_Utils_Hook::singleton()->invoke(1, $params, $null, $null, $null, $null, 'civimobileapi_qrCodeBlockParams');
    } else{
      return CRM_Utils_Hook::singleton()->invoke(1, $params, $null, $null, $null, $null, $null, 'civimobileapi_qrCodeBlockParams');
    }
  }

}
