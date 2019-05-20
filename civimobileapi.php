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
 * @link http://wiki.civicrm.org/confluence/display/CR
 *   MDOC/hook_civicrm_upgrade
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
 * Implements hook_civicrm_apiWrappers().
 */
function civimobileapi_civicrm_apiWrappers(&$wrappers, $apiRequest) {
  if (is_mobile_request()) {
    if ($apiRequest['entity'] == 'Contact' && ($apiRequest['action'] == 'getsingle' || $apiRequest['action'] == 'get')) {
      $wrappers[] = new CRM_CiviMobileAPI_ApiWrapper_Contact();
    }
    elseif ($apiRequest['entity'] == 'Address' && $apiRequest['action'] == 'get') {
      $wrappers[] = new CRM_CiviMobileAPI_ApiWrapper_Address();
    }
    elseif ($apiRequest['entity'] == 'Activity') {
      if ($apiRequest['action'] == 'getsingle') {
        $wrappers[] = new CRM_CiviMobileAPI_ApiWrapper_Activity_GetSingle();
      }

      if ($apiRequest['action'] == 'get') {
        $wrappers[] = new CRM_CiviMobileAPI_ApiWrapper_Activity_Get();
      }

      $wrappers[] = new CRM_CiviMobileAPI_ApiWrapper_Activity_Notification();
    }
    elseif ($apiRequest['entity'] == 'Case' && $apiRequest['action'] == 'getsingle') {
      $wrappers[] = new CRM_CiviMobileAPI_ApiWrapper_Case();
    }
    elseif ($apiRequest['entity'] == 'Event' && ($apiRequest['action'] == 'getsingle' || $apiRequest['action'] == 'get')) {
      $wrappers[] = new CRM_CiviMobileAPI_ApiWrapper_Event();
    }
    elseif ($apiRequest['entity'] == 'Job' && $apiRequest['action'] == 'version_check') {
      $wrappers[] = new CRM_CiviMobileAPI_ApiWrapper_Job_VersionCheck();
    }
    elseif ($apiRequest['entity'] == 'Note' && $apiRequest['action'] == 'get') {
      $wrappers[] = new CRM_CiviMobileAPI_ApiWrapper_Note();
    }
    elseif ($apiRequest['entity'] == 'Contribution' && ($apiRequest['action'] == 'getsingle' || $apiRequest['action'] == 'get')) {
      $wrappers[] = new CRM_CiviMobileAPI_ApiWrapper_Contribution();
    }
    elseif ($apiRequest['entity'] == 'Membership') {
      if ($apiRequest['action'] == 'create') {
        $wrappers[] = new CRM_CiviMobileAPI_ApiWrapper_Membership_Create();
      }

      if ($apiRequest['action'] == 'getsingle' || $apiRequest['action'] == 'get') {
        $wrappers[] = new CRM_CiviMobileAPI_ApiWrapper_Membership_Get();
      }
    }
    elseif ($apiRequest['entity'] == 'Relationship' && $apiRequest['action'] == 'get') {
      $wrappers[] = new CRM_CiviMobileAPI_ApiWrapper_Relationship_Get();
    }
    elseif ($apiRequest['entity'] == 'Participant') {
      if ($apiRequest['action'] == 'create') {
        $wrappers[] = new CRM_CiviMobileAPI_ApiWrapper_Participant_Create();
      }
      elseif ($apiRequest['action'] == 'get') {
        $wrappers[] = new CRM_CiviMobileAPI_ApiWrapper_Participant_Get();
      }
    }
  }
}

/**
 * API hook to disable permission validation
 */
function civimobileapi_civicrm_alterAPIPermissions($entity, $action, &$params, &$permissions) {
  if (is_mobile_request()) {
    civimobileapi_secret_validation();
    if (($entity == 'calendar' and $action == 'get') ||
      ($entity == 'my_event' and $action == 'get') ||
      ($entity == 'civi_mobile_system' and $action == 'get') ||
      ($entity == 'civi_mobile_calendar' and $action == 'get') ||
      ($entity == 'relationship' and $action == 'update') ||
      ($entity == 'civi_mobile_case_role') ||
      ($entity == 'civi_mobile_allowed_relationship_types') ||
      ($entity == 'civi_mobile_allowed_extended_relationship_types') ||
      ($entity == 'push_notification' and $action == 'create') ||
      ($entity == 'contact_type' and $action == 'get') ||
      ($entity == 'location_type' and $action == 'get') ||
      ($entity == 'civi_mobile_permission' and $action == 'get') ||
      ($entity == 'option_value' and $action == 'get') ||
      ($entity == 'phone' and $action == 'create') ||
      ($entity == 'email' and $action == 'create') ||
      ($entity == 'contact' and $action == 'delete') ||
      ($entity == 'civi_mobile_contact' and $action == 'create') ||
      ($entity == 'phone' and $action == 'create') ||
      ($entity == 'address' and $action == 'create') ||
      ($entity == 'website' and $action == 'create') ||
      ($entity == 'civi_mobile_active_relationship' and $action == 'get') ||
      ($entity == 'civi_mobile_allowed_activity_types' and $action == 'get') ||
      ($entity == 'civi_mobile_contribution_statistic') ||
      ($entity == 'state_province' and $action == 'get')
    ) {
      $params['check_permissions'] = FALSE;
    }
  }
}

/**
 * Integrates Pop-up window to notify that mobile application is available for
 * this website
 */
function civimobileapi_civicrm_pageRun(&$page) {
  if (empty($_GET['snippet'])) {
    if (CRM_Core_Permission::check('administer CiviCRM')){
      $param = [
        'apple_link' => 'https://itunes.apple.com/us/app/civimobile/id1404824793?mt=8',
        'google_link' => 'https://play.google.com/store/apps/details?id=com.agiliway.civimobile',
        'bg_color' => '#2786C2',
        'logo' => CRM_CiviMobileAPI_ExtensionUtil::url('/img/logo.png'),
      ];

      CRM_Core_Smarty::singleton()->assign($param);
      CRM_Core_Region::instance('page-body')->add([
        'template' => CRM_CiviMobileAPI_ExtensionUtil::path() . '/templates/CRM/CiviMobileAPI/popup.tpl',
      ]);
    }
  }
}

/**
 * Adds hook civimobile_secret_validation, which you can use to add own secret
 * validation
 */
function civimobileapi_secret_validation() {
  $nullObject = CRM_Utils_Hook::$_nullObject;
  $validated = TRUE;
  CRM_Utils_Hook::singleton()
    ->commonInvoke(1, $validated, $nullObject, $nullObject, $nullObject, $nullObject, $nullObject, 'civimobile_secret_validation', '');
  if (!$validated) {
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

function civimobileapi_civicrm_post($op, $objectName, $objectId, &$objectRef) {
  /**
   * This hook run only when create Case or make relationship to Case. And send
   * notification if contact or relation contact haves token.
   */
  $notificationFactory = new CRM_CiviMobileAPI_PushNotification_Utils_NotificationFactory($op, $objectName, $objectId, $objectRef, "post");
  $notificationManager = $notificationFactory->getPushNotificationManager();
  if (isset($notificationManager)) {
    $notificationManager->sendNotification();
  }
}

function civimobileapi_civicrm_postProcess($formName, &$form) {
  /**
   * This hook run only when create or update Activity from WEB,
   * if it has made by API notification will send
   * in 'CRM_CiviMobileAPI_ApiWrapper_Activity_Notification'.
   */
  $action = CRM_CiviMobileAPI_PushNotification_Utils_NotificationFactory::convertPostProcessAction($form);
  $formName = CRM_CiviMobileAPI_PushNotification_Utils_NotificationFactory::convertPostProcessFormName($formName);

  $notificationFactory = new CRM_CiviMobileAPI_PushNotification_Utils_NotificationFactory($action, $formName, NULL, $form, "postProcess");
  $notificationManager = $notificationFactory->getPushNotificationManager();

  if (isset($notificationManager)) {
    $notificationManager->sendNotification();
  }

}

function civimobileapi_civicrm_pre($op, $objectName, $id, &$params) {
  /**
   * Send notification in delete process
   */
  $notificationFactory = new CRM_CiviMobileAPI_PushNotification_Utils_NotificationFactory($op, $objectName, $id, $params, "pre");
  $notificationManager = $notificationFactory->getPushNotificationManager();
  if (isset($notificationManager)) {
    $notificationManager->sendNotification();
  }

}

/**
 * @param $tabsetName
 * @param $tabs
 * @param $context
 */
function civimobileapi_civicrm_tabset($tabsetName, &$tabs, $context) {
  if ($tabsetName == 'civicrm/contact/view' && !empty($context['contact_id'])) {
    if (CRM_Contact_BAO_Contact::getContactType($context['contact_id']) == 'Individual' &&
       (CRM_Core_Permission::check('administer CiviCRM') || CRM_Core_Session::singleton()->getLoggedInContactID() == $context['contact_id'])
    ) {
      $tabs[] = [
        'id' => 'civimobile',
        'url' => CRM_Utils_System::url('civicrm/civimobile/dashboard', 'reset=1&cid=' . $context['contact_id']),
        'title' => ts('CiviMobile'),
        'weight' => 99,
      ];
    }
  }
}

/**
 * @param $entity
 * @param $clauses
 *
 * @throws \CRM_Core_Exception
 */
function civimobileapi_civicrm_selectWhereClause($entity, &$clauses) {
  if ($entity == 'Note') {
    if ($json = CRM_Utils_Request::retrieve('json', 'String')) {
      $params = json_decode($json, TRUE);

      if (!empty($params['entity_table']) && $params['entity_table'] == 'civicrm_note') {
        unset($clauses['id']);
      }
    }
  }
}

if (!function_exists('is_writable_r')) {

  /**
   * @param string $dir directory path.
   *
   * @return bool
   */
  function is_writable_r($dir) {
    if (is_dir($dir)) {
      if (is_writable($dir)) {
        $objects = scandir($dir);

        foreach ($objects as $object) {
          if ($object != "." && $object != "..") {
            if (!is_writable_r($dir."/".$object)) {
              return FALSE;
            }
            else {
              continue;
            }
          }
        }

        return TRUE;
      } else {
        return FALSE;
      }
    } else if (file_exists($dir)) {
      return is_writable($dir);
    }

    return false;
  }
}

/**
 * Implements hook_civicrm_navigationMenu().
 *
 * @param $menu
 */
function civimobileapi_civicrm_navigationMenu(&$menu) {
  $directDebitMenuItem = [
    'name' => ts('CiviMobile Settings'),
    'url' => 'civicrm/civimobile/settings',
    'permission' => 'administer CiviCRM',
    'operator' => NULL,
    'separator' => NULL,
  ];
  _civimobileapi_civix_insert_navigation_menu($menu, 'Administer/', $directDebitMenuItem);
}
