<?php

class CRM_CiviMobileAPI_Api_CiviMobileCalendar_Get extends CRM_CiviMobileAPI_Api_CiviMobileBase {

  /**
   * Returns results to api
   *
   * @return array
   * @throws Exception
   */
  public function getResult() {
    $calendarParams = [
      'hidePastEvents' => CRM_CiviMobileAPI_Settings_Calendar::getHidePastEvents(),
      'startDate' => $this->validParams["start"],
      'endDate' => $this->validParams["end"],
      'type' => $this->validParams['type']
    ];

    $events = [];
    if ($this->getPermission()) {
      $eventsHandler = new CRM_CiviMobileAPI_Calendar_Handler($this->validParams['contact_id'], $calendarParams);
      $events = $eventsHandler->getAll();
    }

    return $events;
  }

  /**
   * Returns validated params
   * @param $params
   *
   * @return mixed
   */
  protected function getValidParams($params) {
    return $params;
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
