<?php

abstract class CRM_CiviMobileAPI_Install_Entity_EntityBase  implements CRM_CiviMobileAPI_Install_Entity_InstallInterface {

  /**
   * Entity name
   *
   * @var string
   */
  protected $entityName;

  /**
   * List of Entity params
   *
   * @var array
   */
  protected $entityParamList = [];

  /**
   * Params for checking Entity existence
   *
   * @var array
   */
  protected $entitySearchParamNameList = [];

  /**
   * Additional params for checking Entity existence
   *
   * @var array
   */
  protected $additionalEntitySearchParamNameList = [];

  /**
   * LcfEntityBase constructor.
   */
  public function __construct() {
    $this->setEntityParamList();
  }

  /**
   * Installs list of Entity
   */
  public function install() {
    foreach ($this->entityParamList as $entityParam) {
      if (!$this->isExist($entityParam)) {
        $this->create($entityParam);
      }
    }
  }

  /**
   * Creates new Entity
   *
   * @param $entityParam
   */
  private function create($entityParam) {
    try {
      civicrm_api3($this->entityName, 'create', $entityParam);
    } catch (\CiviCRM_API3_Exception $e) {}
  }

  /**
   * Checks exist Entity
   *
   * @param $entityParam
   *
   * @return bool|int
   */
  private function isExist($entityParam) {
    return !empty(($this->getId($entityParam)));
  }

  /**
   * Gets entity id
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
      $result = civicrm_api3($this->entityName, 'getsingle', $searchParam);
    } catch (\CiviCRM_API3_Exception $e) {
      return FALSE;
    }

    return (!empty(($result['id']))) ? (int) $result['id'] : FALSE;
  }

  /**
   * Disables all
   */
  public function disableAll() {
    foreach ($this->entityParamList as $entityParam) {
      $entityId = $this->getId($entityParam);
      if (!empty($entityId)) {
        $this->disable($entityId);
      }
    }
  }

  /**
   * Disables by id
   *
   * @param $entityId
   */
  protected function disable($entityId) {
    try {
      civicrm_api3($this->entityName, 'create', [
        'id' => $entityId,
        'is_active' => 0,
      ] );
    } catch (\CiviCRM_API3_Exception $e) {}
  }

  /**
   * Enables all
   */
  public function enableAll() {
    foreach ($this->entityParamList as $entityParam) {
      $entityId = $this->getId($entityParam);
      if (!empty($entityId)) {
        $this->enable($entityId);
      }
    }
  }

  /**
   * Enables by id
   *
   * @param $entityId
   */
  protected function enable($entityId) {
    try {
      civicrm_api3($this->entityName, 'create', [
        'id' => $entityId,
        'is_active' => 1,
      ] );
    } catch (\CiviCRM_API3_Exception $e) {}
  }

  /**
   * Sets entity Param list
   */
  abstract protected function setEntityParamList();

}
