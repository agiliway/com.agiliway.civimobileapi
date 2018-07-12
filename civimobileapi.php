<?php

require_once 'civimobileapi.civix.php';
use CRM_CiviMobileAPI_ExtensionUtil as E;

/**
 * Implements hook_civicrm_config().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_config
 */
function civimobileapi_civicrm_config(&$config) {
  _civimobileapi_civix_civicrm_config($config);
}

/**
 * Implements hook_civicrm_xmlMenu().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_xmlMenu
 */
function civimobileapi_civicrm_xmlMenu(&$files) {
  _civimobileapi_civix_civicrm_xmlMenu($files);
}

/**
 * Implements hook_civicrm_install().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_install
 */
function civimobileapi_civicrm_install() {
  _civimobileapi_civix_civicrm_install();
}

/**
 * Implements hook_civicrm_postInstall().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_postInstall
 */
function civimobileapi_civicrm_postInstall() {
  _civimobileapi_civix_civicrm_postInstall();
}

/**
 * Implements hook_civicrm_uninstall().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_uninstall
 */
function civimobileapi_civicrm_uninstall() {
  _civimobileapi_civix_civicrm_uninstall();
}

/**
 * Implements hook_civicrm_enable().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_enable
 */
function civimobileapi_civicrm_enable() {
  _civimobileapi_civix_civicrm_enable();
}

/**
 * Implements hook_civicrm_disable().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_disable
 */
function civimobileapi_civicrm_disable() {
  _civimobileapi_civix_civicrm_disable();
}

/**
 * Implements hook_civicrm_upgrade().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_upgrade
 */
function civimobileapi_civicrm_upgrade($op, CRM_Queue_Queue $queue = NULL) {
  return _civimobileapi_civix_civicrm_upgrade($op, $queue);
}

/**
 * Implements hook_civicrm_managed().
 *
 * Generate a list of entities to create/deactivate/delete when this module
 * is installed, disabled, uninstalled.
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_managed
 */
function civimobileapi_civicrm_managed(&$entities) {
  _civimobileapi_civix_civicrm_managed($entities);
}

/**
 * Implements hook_civicrm_caseTypes().
 *
 * Generate a list of case-types.
 *
 * Note: This hook only runs in CiviCRM 4.4+.
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_caseTypes
 */
function civimobileapi_civicrm_caseTypes(&$caseTypes) {
  _civimobileapi_civix_civicrm_caseTypes($caseTypes);
}

/**
 * Implements hook_civicrm_angularModules().
 *
 * Generate a list of Angular modules.
 *
 * Note: This hook only runs in CiviCRM 4.5+. It may
 * use features only available in v4.6+.
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_angularModules
 */
function civimobileapi_civicrm_angularModules(&$angularModules) {
  _civimobileapi_civix_civicrm_angularModules($angularModules);
}

/**
 * Implements hook_civicrm_alterSettingsFolders().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_alterSettingsFolders
 */
function civimobileapi_civicrm_alterSettingsFolders(&$metaDataFolders = NULL) {
  _civimobileapi_civix_civicrm_alterSettingsFolders($metaDataFolders);
}

/**
 * Implements hook_civicrm_apiWrappers
 */
function civimobileapi_civicrm_apiWrappers(&$wrappers, $apiRequest) {
  if ($apiRequest['entity'] == 'Contact' && ($apiRequest['action'] == 'getsingle' || $apiRequest['action'] == 'get')) {
    $wrappers[] = new CRM_CiviMobileAPI_ApiWrapper_Contact();
  } elseif ($apiRequest['entity'] == 'Address' && $apiRequest['action'] == 'get') {
    $wrappers[] = new CRM_CiviMobileAPI_ApiWrapper_Address();
  } elseif ($apiRequest['entity'] == 'Activity' && $apiRequest['action'] == 'getsingle') {
    $wrappers[] = new CRM_CiviMobileAPI_ApiWrapper_Activity();
  } elseif ($apiRequest['entity'] == 'Case' && $apiRequest['action'] == 'getsingle') {
    $wrappers[] = new CRM_CiviMobileAPI_ApiWrapper_Case();
  } elseif ($apiRequest['entity'] == 'Event' && ($apiRequest['action'] == 'getsingle' || $apiRequest['action'] == 'get')) {
    $wrappers[] = new CRM_CiviMobileAPI_ApiWrapper_Event();
  } 
}

/**
 * API hook to disable permishion validation 
 */
function civimobileapi_civicrm_alterAPIPermissions($entity, $action, &$params, &$permissions) {
  if (is_mobile_request()) {
    civimobileapi_secret_validation();
    if (($entity == 'calendar' and $action == 'get') ||
      ($entity == 'my_event' and $action == 'get') ||
      ($entity == 'civi_mobile_system' and $action == 'get') ||
      ($entity == 'civi_mobile_calendar' and $action == 'get') ||
      ($entity == 'push_notification' and $action == 'create') ||
      ($entity == 'contact_type' and $action == 'get') || 
      ($entity == 'location_type' and $action == 'get')
    ) {
      $params['check_permissions'] = false;
    }
  }
}

/**
 * Integrates Pop-up window to notify that mobile application is available for this website
 */
function civimobileapi_civicrm_pageRun(&$page) {
  if (empty($_GET['snippet'])){
    $apple_link = 'https://itunes.apple.com/us/app/civimobile/id1404824793?mt=8';
    $google_link = 'https://play.google.com/store/apps/details?id=com.agiliway.civimobile';
    $bg_color = '#2786C2';
    CRM_Utils_Hook::singleton()->commonInvoke(3, $apple_link, $google_link, $bg_color, $nullObject, $nullObject, $nullObject, 'civimobile_app_link', '');
    require_once CRM_CiviMobileAPI_ExtensionUtil::path('templates/CRM/CiviMobileAPI/popup.tpl');  
  }
}

/**
 * Adds hook civimobile_secret_validation, which you can use to add own secret validation
 */
function civimobileapi_secret_validation() {
  $nullObject = CRM_Utils_Hook::$_nullObject;
  $validated = true;
  CRM_Utils_Hook::singleton()->commonInvoke(1, $validated, $nullObject, $nullObject, $nullObject, $nullObject, $nullObject, 'civimobile_secret_validation', '');
  if(!$validated) {
    http_response_code(404);
    exit;
  }
}

/**
 * Checks if this is request from mobile application
 */
function is_mobile_request() {
  $null = NULL;
  $civimobile = CRM_Utils_Request::retrieve('civimobile', 'Int', $null, FALSE, FALSE, 'GET');
  return $civimobile;
}
