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
    'ext_version' => _civicrm_api3_civi_mobile_system_get_ext_version(),
  ];

  return civicrm_api3_create_success($result, $params);
}

/**
 * Gets version of civimobile extension
 *
 * @return string
 * @throws \CRM_Extension_Exception_ParseException
 */
function _civicrm_api3_civi_mobile_system_get_ext_version()
{
    $ext_info = CRM_Extension_Info::loadFromFile(CRM_CiviMobileAPI_ExtensionUtil::path() . DIRECTORY_SEPARATOR . CRM_Extension_Info::FILENAME);
    return isset($ext_info->version) ? $ext_info->version : '';
}
