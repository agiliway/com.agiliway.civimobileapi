<?php

/**
 * Class retrieve 'rest path' for CiviCRM API
 */
class CRM_CiviMobileAPI_Utils_RestPath {

  /**
   * Gets 'rest path' for CiviCRM API
   */
  public function get() {
    $currentCMS = CRM_CiviMobileAPI_Utils_CmsUser::getInstance()->getSystem();
    $restPath = $this->getStandardRestPath();

    if ($currentCMS == CRM_CiviMobileAPI_Utils_CmsUser::CMS_WORDPRESS ) {
      $restPath = $this->getWordpressRestPath();
    }

    if ($currentCMS == CRM_CiviMobileAPI_Utils_CmsUser::CMS_JOOMLA ) {
      $restPath = $this->getJoomlaRestPath();
    }

    return $restPath;
  }

  /**
   * Gets standard 'rest path' for CiviCRM API
   *
   * @return mixed
   */
  private function getStandardRestPath() {
    return Civi::paths()->getUrl("[civicrm.root]/extern/rest.php");
  }

  /**
   * Gets Wordpress 'rest path' for CiviCRM API
   *
   * @return string
   */
  private function getWordpressRestPath() {
    $restPath = $this->getStandardRestPath();
    $endpoint = $this->getWordpressApiEndpoint();

    if (!empty($endpoint) && function_exists('get_rest_url')) {
      $restPath = str_replace(home_url(), '', get_rest_url()) . $endpoint;
    }

    return $restPath;
  }

  /**
   * Is 'civicrm-wp-rest' plugin active
   *
   * @return bool
   */
  public function isWpRestPluginActive() {
    if (CRM_CiviMobileAPI_Utils_CmsUser::getInstance()->getSystem() !== CRM_CiviMobileAPI_Utils_CmsUser::CMS_WORDPRESS ) {
      return false;
    }

    if (function_exists('is_plugin_active')) {
      $pathPlugin = 'civicrm-wp-rest/civicrm-wp-rest.php';
      if (is_plugin_active($pathPlugin)) {
        return true;
      }
    }

    if (class_exists('CiviCRM_WP_REST\Controller\Rest')) {
      $restController = new CiviCRM_WP_REST\Controller\Rest();
      if (method_exists($restController, 'get_endpoint')) {
        return true;
      }
    }

    return false;
  }

  /**
   * Gets Joomla 'rest path' for CiviCRM API
   *
   * @return string
   */
  private function getJoomlaRestPath() {
    return '/administrator' . Civi::paths()->getUrl("[civicrm.root]/extern/rest.php");
  }

  /**
   * Returns absolute rest URL
   *
   * @return string
   */
  public function getAbsoluteUrl() {
    $currentCMS = CRM_CiviMobileAPI_Utils_CmsUser::getInstance()->getSystem();
    $restPath = $this->getStandardAbsoluteUrl();

    if ($currentCMS == CRM_CiviMobileAPI_Utils_CmsUser::CMS_WORDPRESS ) {
      $restPath = $this->getWordpressAbsoluteUrl();
    }

    if ($currentCMS == CRM_CiviMobileAPI_Utils_CmsUser::CMS_JOOMLA ) {
      $restPath = $this->getJoomlaAbsoluteUrl();
    }

    return $restPath;
  }

  /**
   * Returns standard absolute rest URL
   *
   * @return string
   */
  private function getStandardAbsoluteUrl() {
    $config = CRM_Core_Config::singleton();
    return $config->userFrameworkResourceURL . 'extern/rest.php';
  }

  /**
   * Returns Wordpress absolute rest URL
   *
   * @return string
   */
  private function getWordpressAbsoluteUrl() {
    $restUrl = $this->getStandardAbsoluteUrl();
    $endpoint = $this->getWordpressApiEndpoint();

    if (!empty($endpoint)) {
      if (function_exists('get_rest_url')) {
        $restUrl = get_rest_url() . $endpoint;
      }
    }

    return $restUrl;
  }

  /**
   * Returns Joomla absolute rest URL
   *
   * @return string
   */
  private function getJoomlaAbsoluteUrl() {
    return JUri::root() . substr($this->getJoomlaRestPath(), 1);
  }

  /**
   * Returns endpoint for Wordpress
   *
   * @return string
   */
  private function getWordpressApiEndpoint() {
    $endpoint = '';

    if ($this->isWpRestPluginActive()) {
      $endpoint =  'civicrm/v3/rest';
    }

    if (class_exists('CiviCRM_WP_REST\Controller\Rest')) {
      $restController = new CiviCRM_WP_REST\Controller\Rest();
      if (method_exists($restController, 'get_endpoint')) {
        $endpoint = substr((string) $restController->get_endpoint(), 1);
      }
    }

    return $endpoint;
  }

}
