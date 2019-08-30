<?php

/**
 * Base class for CiviMobile API
 */
abstract class CRM_CiviMobileAPI_Api_CiviMobileBase {

  /**
   * Validated params
   */
  protected $validParams;

  /**
   * CRM_CiviMobileAPI_Api_CiviMobileBase constructor.
   *
   * @param $params
   */
  public function __construct($params) {
    $this->validParams = $this->getValidParams($params);
  }

  /**
   * Returns results to api
   *
   * @return array
   */
  public function getResult() {
    return [];
  }

  /**
   * Returns validated params
   *
   * @param $params
   *
   * @return array
   */
  protected function getValidParams($params) {
    return [];
  }

}
