<?php

/**
 * This API get called when run schedule job "Notify all participants that event is going to start"
 *
 * @param $params
 *
 * @return mixed
 * @throws \Exception
 */
function civicrm_api3_push_notification_event_reminder_send($params) {
  $data_time_min = CRM_Utils_Date::getToday(NULL, 'Y-m-d H:i:s');
  $data_time_min = date('Y-m-d H:i:s', strtotime($data_time_min . "+30 minutes"));
  $data_time_max = date('Y-m-d H:i:s', strtotime($data_time_min . "+1 hour"));

  $events = civicrm_api3('Event', 'get', [
    'return' => ["id", "start_date"],
    'start_date' => ['BETWEEN' => [$data_time_min, $data_time_max]],
    'is_active' => 1,
  ]);

  if (!empty($events['values'])) {
    foreach ($events['values'] as $event) {
      $eventsIDs[] = $event['id'];
      $eventsTime[$event['id']] = $event['start_date'];
    }

    $participants = civicrm_api3('Participant', 'get', ['return' => ["contact_id", "event_id"], 'event_id' => $eventsIDs]);
    foreach ($participants['values'] as $participant) {
      $text = ts('Event start at') . ' ' . $eventsTime[$participant['event_id']];
      $data = [
        'entity' => 'Event',
        'id' => $participant['event_id'],
        'body' => $text
      ];

      CRM_CiviMobileAPI_PushNotification_SaveMessageHelper::saveMessages(
        [$participant['contact_id']],
        $participant['event_id'],
        'Event',
        $participant['event_title'],
        $text,
        $data
      );
      CRM_CiviMobileAPI_PushNotification_Helper::sendPushNotification(
        [$participant['contact_id']],
        $participant['event_title'],
        $text,
        $data
      );
    }
  }

  return [
    'values' => '',
    'is_error' => '0'
  ];
}
