<?php

/**
 * Gets events, cases and activities for calendar
 *
 * @param array $params array or parameters
 *
 * @return array
 */
function civicrm_api3_civi_mobile_calendar_get($params) {
  $calendarManager = new CRM_CiviMobileAPI_Utils_Calendar($params);
  $events = $calendarManager->getEvents();

  return civicrm_api3_create_success($events, $params);
}

function _civicrm_api3_civi_mobile_calendar_get_formatResult($params, $events) {
  //TODO:return only nedeed fields
  return $events;
}

/**
 * Adjust Metadata for get action
 *
 * The metadata is used for setting defaults, documentation & validation
 * @param array $params array or parameters determined by getfields
 */
function _civicrm_api3_civi_mobile_calendar_get_spec(&$params) {
  $params['contact_id'] = [
    'title' => 'Contact ID',
    'description' => ts('Contact ID'),
    'api.required' => 1,
    'type' => CRM_Utils_Type::T_INT,
  ];
  $params['start'] = [
    'title' => 'Start date',
    'description' => ts('Start date for searching (Y-m-d H:i:s)'),
    'api.required' => 1,
    'type' => CRM_Utils_Type::T_STRING,
  ];
  $params['end'] = [
    'title' => 'End  date',
    'description' => ts('End date for searching (Y-m-d H:i:s)'),
    'api.required' => 1,
    'type' => CRM_Utils_Type::T_STRING,
  ];
  $params['type'] = [
    'title' => 'Type',
    'description' => ts('Event type'),
    'api.default' => ['all'],
    'type' => CRM_Utils_Type::T_STRING,
    'options' => [
      CRM_CiviMobileAPI_Calendar_Handler::TYPE_ALL => CRM_CiviMobileAPI_Calendar_Handler::TYPE_ALL,
      CRM_CiviMobileAPI_Calendar_Handler::TYPE_EVENTS => CRM_CiviMobileAPI_Calendar_Handler::TYPE_EVENTS,
      CRM_CiviMobileAPI_Calendar_Handler::TYPE_CASES => CRM_CiviMobileAPI_Calendar_Handler::TYPE_CASES,
      CRM_CiviMobileAPI_Calendar_Handler::TYPE_ACTIVITIES => CRM_CiviMobileAPI_Calendar_Handler::TYPE_ACTIVITIES,
    ]
  ];
}
