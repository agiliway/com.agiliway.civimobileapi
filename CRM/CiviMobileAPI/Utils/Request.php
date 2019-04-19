<?php
/**
 * Gives you the ability to work with request params
 * The Class is a wrapper, since in the feature make sens to validate params
 */

class CRM_CiviMobileAPI_Utils_Request {

  /**
   * Singleton pattern
   */
  private static $instance;

  /**
   * JSON body
   * @var array
   */
  private $jsonBody;

  /**
   * CRM_CiviMobileAPI_Utils_Request constructor.
   */
  private function __construct() {
    $this->getJsonBody();
  }

  private function __clone() {}

  /**
   * Gets instance of CRM_CiviMobileAPI_Utils_Request
   *
   * @return \CRM_CiviMobileAPI_Utils_Request
   */
  public static function getInstance() {
    if (self::$instance === NULL) {
      self::$instance = new self;
    }
    return self::$instance;
  }

  /**
   * Retrieves GET param by name
   *
   * @param $name
   * @param $type
   *
   * @return mixed
   */
  public function get($name, $type) {
    try {
      $null = NULL;
      $param = CRM_Utils_Request::retrieve($name, $type, $null, FALSE, NULL, 'GET');
    } catch (Exception $e) {
      $param = NULL;
    }
    return $param;
  }

  /**
   * Retrieves POST param by name
   *
   * @param $name
   * @param $type
   *
   * @return mixed
   */
  public function post($name, $type) {
    try {
      $null = NULL;
      $param = CRM_Utils_Request::retrieve($name, $type, $null, FALSE, NULL, 'POST');
    } catch (Exception $e) {
      $param = NULL;
    }
    return $param;
  }

  /**
   * Retrieves param by name
   *
   * @param $name
   * @param $type
   *
   * @return mixed
   */
  public function find($name, $type) {
    $param = $this->json($name);
    if (!$param) {
      $param = $this->post($name, $type);
    }

    return $param;
  }

  /**
   * Get param from JSON
   *
   * @param $name
   *
   * @return mixed|string
   */
  public function json($name) {
    $param = !empty($this->jsonBody[$name]) ? $this->jsonBody[$name] : '';

    return $param;
  }

  /**
   * Parse BODY and convert to the array
   */
  private function getJsonBody() {
    $inputJSON = file_get_contents('php://input');
    $this->jsonBody = json_decode($inputJSON, TRUE);
  }

}
