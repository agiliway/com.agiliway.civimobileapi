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

    if (function_exists('is_plugin_active')) {
      $pathPlugin = 'civicrm-wp-rest/civicrm-wp-rest.php';
      if (is_plugin_active($pathPlugin)) {
        $restPath =  '/wp-json/civicrm/v3/rest';
      }
    }

    if (class_exists('CiviCRM_WP_REST\Controller\Rest')) {
      $restController = new CiviCRM_WP_REST\Controller\Rest();
      if (method_exists($restController, 'get_endpoint')) {
        $restPath = '/wp-json' . (string) (new CiviCRM_WP_REST\Controller\Rest)->get_endpoint();
      }
    }

    return $restPath;
  }

}
