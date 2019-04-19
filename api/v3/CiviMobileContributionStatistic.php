<?php

/**
 * Gets statistic for Contact's Contribution
 *
 * @param $params
 *
 * @return array
 */
function civicrm_api3_civi_mobile_contribution_statistic_get($params) {
  $queryParams = ['contact_id' => $params['contact_id']];
  $currentYearQueryParams = [
    'contact_id' => $params['contact_id'],
    'receive_date' => [
      'BETWEEN' => [
        CRM_Utils_Date::getToday(['month'=> 1, 'day' => 1, 'year' => date("Y")], 'Y-m-d H:i:s'),
        CRM_Utils_Date::getToday(['month'=> 1, 'day' => 1, 'year' => (date("Y") + 1)], 'Y-m-d H:i:s'),
      ]
    ],
  ];

  $selector = new CRM_Contribute_Selector_Search(CRM_Contact_BAO_Query::convertFormValues($queryParams));
  $currentYearSelector = new CRM_Contribute_Selector_Search(CRM_Contact_BAO_Query::convertFormValues($currentYearQueryParams));

  $statistic = [
    'all_time' => CRM_CiviMobileAPI_Utils_Contribution::transformStatistic($selector->getSummary()),
    'current_year' => CRM_CiviMobileAPI_Utils_Contribution::transformStatistic($currentYearSelector->getSummary())
  ];

  return civicrm_api3_create_success([$params['contact_id'] => $statistic], $params);
}

/**
 * Adjust Metadata for get action
 *
 * The metadata is used for setting defaults, documentation & validation
 * @param array $params array or parameters determined by getfields
 */
function _civicrm_api3_civi_mobile_contribution_statistic_get_spec(&$params) {
  $params['contact_id'] = [
    'title' => 'Contact ID',
    'description' => ts('Contact ID'),
    'api.required' => 1,
    'type' => CRM_Utils_Type::T_INT,
  ];
}
