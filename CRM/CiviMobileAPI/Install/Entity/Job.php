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
      ],
      [
        'name' => 'Notify all participants that event is going to start',
        'description' => 'Notify all participants that event is going to start',
        'api_entity' => 'PushNotificationEventReminder',
        'api_action' => 'send',
        'run_frequency' => 'Hourly',
        'domain_id' => CRM_Core_Config::domainID(),
        'is_active' => '0',
      ]
    ];
  }

}
