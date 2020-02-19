<?php

/**
 * Gets information about extension
 *
 * @param array $params
 *   Array per getfields documentation.
 *
 * @return array API result array
 * @throws \CRM_Extension_Exception_ParseException
 */
function civicrm_api3_civi_mobile_system_get($params) {
  $result = [];
  $result[] = [
    'cms' => CRM_CiviMobileAPI_Utils_CmsUser::getInstance()->getSystem(),
    'crm_version' => CRM_Utils_System::version(),
    'ext_version' => CRM_CiviMobileAPI_Utils_VersionController::getInstance()->getCurrentFullVersion(),
    'site_name' => CRM_CiviMobileAPI_Utils_Extension::getSiteName(),
  ];

  return civicrm_api3_create_success($result, $params);
}
