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
   * @deprecated please use getAll function, which combine all items in one array and has filter by type
   * @return mixed
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
        CONVERT_TZ(civicrm_event.start_date , @@session.time_zone, "+00:00") AS start,
        CONVERT_TZ(civicrm_event.end_date , @@session.time_zone, "+00:00") AS end
      FROM civicrm_event
      LEFT JOIN civicrm_participant ON civicrm_participant.event_id = civicrm_event.id
      WHERE civicrm_event.is_active = 1 
        AND civicrm_event.is_template = 0
        AND ( civicrm_event.created_id = ' . $this->contactId . ' OR civicrm_participant.contact_id = ' . $this->contactId . ')
        AND (
          civicrm_event.start_date BETWEEN "' . $this->params['startDate'] . '" AND "' . $this->params['endDate'] . '" 
          OR civicrm_event.end_date BETWEEN "' . $this->params['startDate'] . '" AND "' . $this->params['endDate'] . '" 
          OR "' . $this->params['startDate'] . '" BETWEEN civicrm_event.start_date AND civicrm_event.end_date
        )
    ';

    if ($this->params['hidePastEvents'] == "1") {
      $query .= ' AND civicrm_event.start_date > NOW()';
    }

    $dao = CRM_Core_DAO::executeQuery($query);

    while ($dao->fetch()) {
      if ($dao->title) {
        $dao->url = htmlspecialchars_decode(CRM_Utils_System::url('civicrm/event/info', 'reset=1&id=' . $dao->id));
      }

      $eventData = [];
      $eventData['id'] = $dao->id;
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
   */
  public function getCases() {
    $result = [];
    $query = '
      SELECT 
        civicrm_case.id AS id,
        civicrm_case_activity.activity_id AS activity_id,
        civicrm_case.subject as case_title,
        civicrm_activity.subject as activity_title,
        CONCAT(COALESCE(civicrm_activity.subject,civicrm_case.subject,"")," (",civicrm_option_value.name,")") AS title,
        civicrm_activity.activity_date_time AS start,
        DATE_ADD(civicrm_activity.activity_date_time, INTERVAL COALESCE (civicrm_activity.duration, 30) MINUTE) AS end
      
      FROM civicrm_case
      
      JOIN civicrm_case_contact ON civicrm_case_contact.case_id = civicrm_case.id
      JOIN civicrm_case_activity ON civicrm_case_activity.case_id = civicrm_case.id
      JOIN civicrm_activity ON civicrm_activity.id = civicrm_case_activity.activity_id
      JOIN civicrm_option_value ON civicrm_activity.activity_type_id = civicrm_option_value.value
      JOIN civicrm_option_group ON civicrm_option_group.id = civicrm_option_value.option_group_id AND civicrm_option_group.name = "activity_type" AND civicrm_option_value.component_id IS NOT NULL
      
      WHERE civicrm_case_contact.contact_id = ' . $this->contactId . '
      AND civicrm_case.is_deleted=0 AND civicrm_activity.is_deleted=0
      AND ( (civicrm_activity.activity_date_time >= "' . $this->params['startDate'] . '"
      AND COALESCE (DATE_ADD(civicrm_activity.activity_date_time, INTERVAL COALESCE (civicrm_activity.duration, 30) MINUTE),civicrm_activity.activity_date_time) <= "' . $this->params['endDate'] . '" )
      OR ("' . $this->params['startDate'] . '" BETWEEN civicrm_activity.activity_date_time AND COALESCE (DATE_ADD(civicrm_activity.activity_date_time, INTERVAL COALESCE (civicrm_activity.duration, 30) MINUTE),civicrm_activity.activity_date_time)))
    ';

    if ($this->params['hidePastEvents'] == "1") {
      $query .= ' AND civicrm_activity.activity_date_time > NOW()';
    }

    $dao = CRM_Core_DAO::executeQuery($query);

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
        civicrm_activity.activity_date_time AS start,
        DATE_ADD(civicrm_activity.activity_date_time, INTERVAL COALESCE (civicrm_activity.duration, 30) MINUTE) AS end
      
      FROM civicrm_activity
      JOIN civicrm_activity_contact ON civicrm_activity_contact.activity_id = civicrm_activity.id
      LEFT JOIN civicrm_case_activity ON civicrm_case_activity.activity_id = civicrm_activity.id
      
      WHERE civicrm_activity_contact.contact_id = "' . $this->contactId . '" 
      AND (civicrm_activity.activity_date_time > "' . $this->params['startDate'] . '" 
      AND civicrm_activity.activity_date_time < "' . $this->params['endDate'] . '") AND civicrm_case_activity.activity_id IS NULL
        AND civicrm_activity.is_deleted=0      
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

    $dao = CRM_Core_DAO::executeQuery($query);

    while ($dao->fetch()) {
      if ($dao->title) {
        $dao->url = htmlspecialchars_decode(CRM_Utils_System::url('civicrm/activity', 'action=view&reset=1&cid=' . $this->contactId . '&id=' . $dao->id));
      }

      $eventData = [];
      $eventData['id'] = $dao->id;

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
