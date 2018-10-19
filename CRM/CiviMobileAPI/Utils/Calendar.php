<?php

class CRM_CiviMobileAPI_Utils_Calendar {

  /**
   * Requested params
   *
   * @var array
   */
  private $params;

  public function __construct($params) {
    $this->params = $params;
  }

  /**
   * Gets calendar events
   *
   * @return array
   */
  public function getEvents() {
    $calendarParams = [
      'hidePastEvents' => $this->params['hidePastEvents'],
      'startDate' => $this->params["start"],
      'endDate' => $this->params["end"],
      'type' => $this->params['type']
    ];

    $events = [];
    if ($this->getPermission()){
      $eventsHandler = new CRM_CiviMobileAPI_Calendar_Handler($this->params['contact_id'], $calendarParams);
      $events = $eventsHandler->getAll();
      $events = _civicrm_api3_civi_mobile_calendar_get_formatResult($this->params, $events);
    }

    return $events;
  }

  /**
   * Gets permission if user allowed to receive events
   *
   * @return bool
   */
  private function getPermission() {
    $accessCiviEvent = CRM_Core_Permission::check("access CiviEvent");
    $accessMyCase = CRM_Core_Permission::check("access my cases and activities");
    $accessAllCase = CRM_Core_Permission::check("access all cases and activities");

    return  $accessCiviEvent && ($accessMyCase || $accessAllCase)? TRUE : FALSE;
  }


}