<?php

/**
 * Gets notification messages
 *
 * @param array $params
 *
 * @return array
 */
function civicrm_api3_push_notification_messages_get($params) {

  if (isset($params['options']['limit'])) {
    $params['limit'] = $params['options']['limit'];
    $params['offset'] = isset($params['options']['offset']) ? $params['options']['offset'] : 0;
  }

  if (!isset($params['options']['sort'])) {
    $params['sort'] = 'id';
  } else {
    $params['sort'] = $params['options']['sort'];
  }

  if (!isset($params['direction'])) {
    $params['direction'] = 'DESC';
  }

  $messages = CRM_CiviMobileAPI_BAO_PushNotificationMessages::getNotifications($params);

  return civicrm_api3_create_success($messages, $params);
}

/**
 * Adjust Metadata for get action
 *
 * The metadata is used for setting defaults, documentation & validation
 * @param array $params array or parameters determined by getfields
 */
function _civicrm_api3_push_notification_messages_get_spec(&$params) {
  $params['contact_id'] = [
    'title' => 'Contact ID',
    'description' => ts('Contact ID'),
    'api.required' => 1,
    'type' => CRM_Utils_Type::T_INT
  ];
  $params['direction'] = [
    'title' => 'Direction',
    'description' => ts('Direction'),
    'api.required' => 0,
    'type' => CRM_Utils_Type::T_STRING,
    'options' => ['ASC' => 'ASC', 'DESC' => 'DESC']
  ];
}

/**
 * Clears old Push notification messages
 *
 * @param array $params
 *
 * @return array
 */
function civicrm_api3_push_notification_messages_clear_old($params) {
  $countOfDays = (isset($params['count_of_day'])) ? (int) $params['count_of_day'] : CRM_CiviMobileAPI_BAO_PushNotificationMessages::LIFE_TIME_IN_DAYS;

  CRM_CiviMobileAPI_BAO_PushNotificationMessages::deleteOlderThan($countOfDays);

  return civicrm_api3_create_success(['message' => 'Push notification messages older than ' . $countOfDays .  ' days was deleted.']);
}

/**
 * Adjust Metadata
 *
 * The metadata is used for setting defaults, documentation & validation
 * @param array $params array or parameters determined by getfields
 */
function _civicrm_api3_push_notification_messages_clear_old_spec(&$params) {
  $params['count_of_day'] = [
    'title' => 'Count of days',
    'description' => ts('Deletes "Push notification messages" older than this param. Default 90.'),
    'api.required' => 0,
    'type' => CRM_Utils_Type::T_INT
  ];
}
