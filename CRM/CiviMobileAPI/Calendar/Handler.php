<?php

class CRM_CiviMobileAPI_Calendar_Handler {

  const TYPE_ALL = 'all';
  const TYPE_EVENTS = 'event';
  const TYPE_CASES = 'case';
  const TYPE_ACTIVITIES = 'activity';

  /**
   * Contact id
   *
   * @var int
   */
  private $contactId;

  /**
   * Validated params
   *
   * @var array
   */
  private $params;

  /**
   * Fields which returned from DAO
   *
   * @var array
   */
  private $fields = ['title' => 'title', 'start' => 'start', 'end' => 'end', 'url' => 'url', 'type' => 'type'];

  /**
   * CRM_CiviMobileAPI_Calendar_Handler constructor.
   *
   * @param $contactId
   * @param $params
   */
  public function __construct($contactId, $params) {
    $this->contactId = $contactId;
    $this->params = $this->validateParams($params);
  }

  /**
   * Validates params
   *
   * @param $params
   *
   * @return mixed
   */
  private function validateParams($params) {
    if (empty($params['startDate'])) {
      $params['startDate'] = gmdate("Y-m-d H:i:s", time());
    }
    if (empty($params['endDate'])) {
      $params['endDate'] = gmdate("Y-m-d H:i:s", time() + 86400);
    }
    if (empty($params['hidePastEvents'])) {
      $params['hidePastEvents'] = FALSE;
    }
    if (empty($params['eventColor'])) {
      $params['eventColor'] = '#35D0AE';
    }
    if (empty($params['caseColor'])) {
      $params['caseColor'] = '#ff0000';
    }
    if (empty($params['activityColor'])) {
      $params['activityColor'] = '#F7CF5D';
    }
    if (!isset($params['type'])) {
      $params['type'][] = self::TYPE_ALL;
    }
    elseif (!is_array($params['type'])) {
      $params['type'] = [(empty($params['type'])) ? self::TYPE_ALL : $params['type']];
    }
    if (!empty($params['fields'])) {
      $this->fields = $params['fields'];
    }

    return $params;
  }

  /**
   * Gets all type events
   *
   * @return mixed
   * @throws \Exception
   */
  public function getAll() {
    $events = [];
    if (in_array(self::TYPE_EVENTS, $this->params['type']) || in_array(self::TYPE_ALL, $this->params['type'])) {
      $events = array_merge($events, $this->getEvents());
    }
    if (in_array(self::TYPE_CASES, $this->params['type']) || in_array(self::TYPE_ALL, $this->params['type'])) {
      $events = array_merge($events, $this->getCases());
    }
    if (in_array(self::TYPE_ACTIVITIES, $this->params['type']) || in_array(self::TYPE_ALL, $this->params['type'])) {
      $events = array_merge($events, $this->getActivities());
    }

    return $events;
  }

  /**
   * Gets events
   *
   * @return array
   */
  public function getEvents() {
    $result = [];
    $query = '
      SELECT DISTINCT
        civicrm_event.id,
        civicrm_event.title,
        event_type_value.label AS event_type_label,
        civicrm_event.event_type_id AS event_type_id,
        CONVERT_TZ(civicrm_event.start_date, @@session.time_zone, "+00:00") AS start,
        CONVERT_TZ(civicrm_event.end_date, @@session.time_zone, "+00:00") AS end
      FROM civicrm_event
      LEFT JOIN civicrm_participant ON civicrm_participant.event_id = civicrm_event.id
      LEFT JOIN `civicrm_option_group` AS event_type_group ON event_type_group.name = \'event_type\'
      LEFT JOIN `civicrm_option_value` AS event_type_value ON (event_type_value.option_group_id = event_type_group.id 
        AND civicrm_event.event_type_id = event_type_value.value )
      WHERE civicrm_event.is_active = 1 
        AND civicrm_event.is_template = 0
        AND ( civicrm_event.created_id = %1 OR civicrm_participant.contact_id = %1)
        AND (
          civicrm_event.start_date BETWEEN %2 AND %3 
          OR civicrm_event.end_date BETWEEN %2 AND %3 
          OR "%2" BETWEEN civicrm_event.start_date AND civicrm_event.end_date
        )
    ';

    if ($this->params['hidePastEvents'] == "1") {
      $query .= ' AND civicrm_event.start_date > NOW()';
    }

    $eventCategories = CRM_CiviMobileAPI_Settings_Calendar::getEventTypes();
    if (!empty($eventCategories)) {
      $query .= ' AND civicrm_event.event_type_id IN (' . implode(', ', $eventCategories) . ') ';
    }

    $dao = CRM_Core_DAO::executeQuery($query, [
      1 => [ $this->contactId, 'Integer' ],
      2 => [ $this->params['startDate'], 'String' ],
      3 => [ $this->params['endDate'], 'String' ]
    ]);

    while ($dao->fetch()) {
      if ($dao->title) {
        $dao->url = htmlspecialchars_decode(CRM_Utils_System::url('civicrm/event/info', 'reset=1&id=' . $dao->id));
      }

      $eventData = [];
      $eventData['id'] = $dao->id;
      $eventData['event_type_label'] = $dao->event_type_label;
      $eventData['event_type_id'] = $dao->event_type_id;
      foreach ($this->fields as $k) {
        $eventData[$k] = $dao->$k;
      }

      $eventData['constraint'] = true;
      $eventData['color'] = $this->params['eventColor'];
      $eventData['type'] = self::TYPE_EVENTS;
      $result[] = $eventData;
    }

    $dao->free();

    return $result;
  }

  /**
   * Gets Cases
   *
   * @return array
   * @throws \Exception
   */
  public function getCases() {
    $result = [];
    $query = '
      SELECT 
        civicrm_case.id AS id,
        civicrm_case_activity.activity_id AS activity_id,
        civicrm_case.subject AS case_title,
        civicrm_activity.subject AS activity_title,
        civicrm_case.case_type_id AS case_type_id,
        civicrm_case_type.title AS case_type_label,
        CONCAT(COALESCE(civicrm_activity.subject,civicrm_case.subject,"")," (",civicrm_option_value.name,")") AS title,
        civicrm_activity.activity_date_time AS start,
        DATE_ADD(civicrm_activity.activity_date_time, INTERVAL COALESCE (civicrm_activity.duration, 30) MINUTE) AS end
      
      FROM civicrm_case      
      JOIN civicrm_case_contact ON civicrm_case_contact.case_id = civicrm_case.id
      JOIN civicrm_case_activity ON civicrm_case_activity.case_id = civicrm_case.id
      JOIN civicrm_activity ON civicrm_activity.id = civicrm_case_activity.activity_id
      JOIN civicrm_option_value ON civicrm_activity.activity_type_id = civicrm_option_value.value
      JOIN civicrm_option_group ON civicrm_option_group.id = civicrm_option_value.option_group_id 
        AND civicrm_option_group.name = "activity_type" AND civicrm_option_value.component_id IS NOT NULL
      JOIN civicrm_case_type ON civicrm_case_type.id = civicrm_case.case_type_id

      WHERE civicrm_case_contact.contact_id = %1
      AND civicrm_case.is_deleted=0 AND civicrm_activity.is_deleted=0
      AND (
        (
          civicrm_activity.activity_date_time >= %2 
          AND COALESCE (DATE_ADD(civicrm_activity.activity_date_time, INTERVAL COALESCE (civicrm_activity.duration, 30) MINUTE), civicrm_activity.activity_date_time) <= %3 
        )
        OR 
        (
          %2 BETWEEN civicrm_activity.activity_date_time 
          AND COALESCE (DATE_ADD(civicrm_activity.activity_date_time, INTERVAL COALESCE (civicrm_activity.duration, 30) MINUTE),civicrm_activity.activity_date_time)
        )
      )
    ';

    if ($this->params['hidePastEvents'] == "1") {
      $query .= ' AND civicrm_activity.activity_date_time > NOW()';
    }

    $caseCategories = CRM_CiviMobileAPI_Settings_Calendar::getCaseTypes();
    if (!empty($caseCategories)) {
      $query .= ' AND civicrm_case.case_type_id IN (' . implode(', ', $caseCategories) . ') ';
    }

    $dao = CRM_Core_DAO::executeQuery($query, [
      1 => [ $this->contactId, 'Integer' ],
      2 => [ $this->params['startDate'], 'String' ],
      3 => [ $this->params['endDate'], 'String' ]
    ]);
    $i = 1;

    while ($dao->fetch()) {
      if ($dao->title) {
        $startDate = new DateTime($dao->start);
        $startDate->modify('+' . $i . ' second');

        $dao->start = $startDate->format('Y-m-d H:i:s');
        $dao->url = htmlspecialchars_decode(CRM_Utils_System::url('civicrm/case/activity/view', 'cid=' . $this->contactId . '&aid=' . $dao->activity_id));
      }

      $eventData = [];
      $eventData['id'] = $dao->activity_id;
      $eventData['case_title'] = $dao->case_title !== $dao->activity_title ? $dao->case_title : '';
      $eventData['case_id'] = $dao->id;
      $eventData['case_type_label'] = $dao->case_type_label ;
      $eventData['case_type_id'] = $dao->case_type_id ;

      foreach ($this->fields as $k) {
        $eventData[$k] = $dao->$k;
      }

      $eventData['constraint'] = true;
      $eventData['color'] = $this->params['caseColor'];
      $eventData['type'] = self::TYPE_CASES;
      $result[] = $eventData;

      $i++;
    }
    $dao->free();

    return $result;
  }

  /**
   * Gets Activities
   *
   * @return array
   */
  public function getActivities() {
    $result = [];
    $query = '
      SELECT DISTINCT
        civicrm_activity.id AS id,
        civicrm_activity.subject AS title,
        civicrm_activity.activity_type_id AS activity_type_id,
        activity_type_value.label AS activity_type_label,
        civicrm_activity.activity_date_time AS start,
        DATE_ADD(civicrm_activity.activity_date_time, INTERVAL COALESCE (civicrm_activity.duration, 30) MINUTE) AS end
      
      FROM civicrm_activity
      JOIN civicrm_activity_contact ON civicrm_activity_contact.activity_id = civicrm_activity.id
      LEFT JOIN civicrm_case_activity ON civicrm_case_activity.activity_id = civicrm_activity.id
      
      LEFT JOIN `civicrm_option_group` AS activity_type_group ON activity_type_group.name = "activity_type"
      LEFT JOIN `civicrm_option_value` AS activity_type_value 
        ON (activity_type_value.option_group_id = activity_type_group.id AND civicrm_activity.activity_type_id = activity_type_value.value )
      
      WHERE civicrm_activity_contact.contact_id = %1 
      AND (civicrm_activity.activity_date_time > %2 
      AND civicrm_activity.activity_date_time < %3) AND civicrm_case_activity.activity_id IS NULL
        AND civicrm_activity.is_deleted = 0      
        AND activity_type_id IN (
          SELECT civicrm_option_value.value FROM civicrm_option_value
          JOIN civicrm_option_group ON civicrm_option_group.id = civicrm_option_value.option_group_id
          WHERE civicrm_option_group.name = "activity_type" 
            AND civicrm_option_value.component_id IS NULL
        )
    ';

    if ($this->params['hidePastEvents'] == "1") {
      $query .= ' AND civicrm_activity.activity_date_time > NOW()';
    }

    $activityCategories = CRM_CiviMobileAPI_Settings_Calendar::getActivityTypes();
    if (!empty($activityCategories)) {
      $query .= ' AND civicrm_activity.activity_type_id IN (' . implode(', ', $activityCategories) . ') ';
    }

    $dao = CRM_Core_DAO::executeQuery($query, [
      1 => [ $this->contactId, 'Integer' ],
      2 => [ $this->params['startDate'], 'String' ],
      3 => [ $this->params['endDate'], 'String' ]
    ]);

    while ($dao->fetch()) {
      if ($dao->title) {
        $dao->url = htmlspecialchars_decode(CRM_Utils_System::url('civicrm/activity', 'action=view&reset=1&cid=' . $this->contactId . '&id=' . $dao->id));
      }

      $eventData = [];
      $eventData['id'] = $dao->id;
      $eventData['activity_type_label'] = $dao->activity_type_label;
      $eventData['activity_type_id'] = $dao->activity_type_id;

      foreach ($this->fields as $k) {
        $eventData[$k] = $dao->$k;
      }

      $eventData['constraint'] = true;
      $eventData['color'] = $this->params['activityColor'];
      $eventData['type'] = self::TYPE_ACTIVITIES;
      $result[] = $eventData;
    }

    $dao->free();

    return $result;
  }

}
