<?php

class CRM_CiviMobileAPI_Utils_Calendar {

  /**
   * Requested params
   *
   * @var array
   */
  private $params;

  /**
   * CRM_CiviMobileAPI_Utils_Calendar constructor.
   *
   * @param $params
   */
  public function __construct($params) {
    $this->params = $params;
  }

  /**
   * Gets calendar events
   *
   * @return array
   * @throws \CiviCRM_API3_Exception
   */
  public function getEvents() {
    $calendarParams = [
      'hidePastEvents' => $this->params['hidePastEvents'],
      'startDate' => $this->params["start"],
      'endDate' => $this->params["end"],
      'type' => $this->params['type']
    ];

    $events = [];
    if ($this->getPermission()) {
      $eventsHandler = new CRM_CiviMobileAPI_Calendar_Handler($this->params['contact_id'], $calendarParams);
      $events = $eventsHandler->getAll();
    }

    return $events;
  }

  /**
   * Gets permission if user allowed to receive events
   *
   * @return bool
   * @throws \CiviCRM_API3_Exception
   */
  private function getPermission() {
    $result = civicrm_api3('CiviMobilePermission', 'get', [
      'sequential' => 1,
    ]);

    if (!isset($result['values'][0]['event']['view']['all']) || empty($result['values'][0]['event']['view']['all'])) {
      return FALSE;
    }

    return  $result['values'][0]['event']['view']['all'] ? TRUE : FALSE;
  }

}
