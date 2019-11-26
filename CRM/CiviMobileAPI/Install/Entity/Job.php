<?php

class CRM_CiviMobileAPI_Install_Entity_Job extends CRM_CiviMobileAPI_Install_Entity_EntityBase {

  /**
   * Entity name
   *
   * @var string
   */
  protected $entityName = 'Job';

  /**
   * Params for checking Entity existence
   *
   * @var array
   */
  protected $entitySearchParamNameList = ['api_action', 'api_entity', 'domain_id'];

  /**
   * Sets entity Param list
   */
  protected function setEntityParamList() {
    $this->entityParamList = [
      [
        'name' => 'Civimobile clean old push notification messages',
        'description' => 'Clean old push notification messages',
        'api_entity' => 'PushNotificationMessages',
        'api_action' => 'clear_old',
        'run_frequency' => 'Daily',
        'domain_id' => CRM_Core_Config::domainID(),
        'is_active' => '0',
      ]
    ];
  }

  /**
   * Gets entity id
   * (override because api 'Job->getsingle' does not work)
   *
   * @param $entityParam
   *
   * @return bool|int
   */
  protected function getId($entityParam) {
    $searchParam = [];
    foreach ($this->entitySearchParamNameList as $nameParam) {
      $searchParam[$nameParam] = $entityParam[$nameParam];
    }

    $searchParam['options'] = ['limit' => 1];

    foreach ($this->additionalEntitySearchParamNameList as $additionalParam) {
      if (!empty($entityParam[$additionalParam])) {
        $searchParam[$additionalParam] = $entityParam[$additionalParam];
      }
    }

    try {
      $result = civicrm_api3($this->entityName, 'get', $searchParam);
    } catch (\CiviCRM_API3_Exception $e) {
      return FALSE;
    }

    if (empty($result['values'])) {
      return FALSE;
    }

    foreach ($result['values'] as $job) {
      return $job['id'];
    }

    return FALSE;
  }

}
