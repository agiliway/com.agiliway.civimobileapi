<?php

require_once 'civimobileapi.civix.php';
require_once 'lib/PHPQRCode.php';
\PHPQRCode\Autoloader::register();

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

    if (is_mobile_request()) {
      if ($apiRequest['action'] == 'getsingle' || $apiRequest['action'] == 'get') {
        $wrappers[] = new CRM_CiviMobileAPI_ApiWrapper_Membership_Get();
      }
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
  elseif ($apiRequest['entity'] == 'GroupContact') {
    if ($apiRequest['action'] == 'get') {
      $wrappers[] = new CRM_CiviMobileAPI_ApiWrapper_GroupContact_Get();
    }
    elseif ($apiRequest['action'] == 'create') {
      $wrappers[] = new CRM_CiviMobileAPI_ApiWrapper_GroupContact_Create();
    }
  }
  elseif ($apiRequest['entity'] == 'EntityTag') {
    if ($apiRequest['action'] == 'get') {
      $wrappers[] = new CRM_CiviMobileAPI_ApiWrapper_EntityTag_Get();
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
      ($entity == 'civi_mobile_participant' and $action == 'create') ||
      ($entity == 'civi_mobile_participant_payment' and $action == 'create') ||
      ($entity == 'participant_status_type' and $action == 'get') ||
      ($entity == 'civi_mobile_get_price_set_by_event' and $action == 'get') ||
      ($entity == 'my_event' and $action == 'get') ||
      ($entity == 'civi_mobile_system' and $action == 'get') ||
      ($entity == 'civi_mobile_calendar' and $action == 'get') ||
      ($entity == 'civi_mobile_my_ticket' and $action == 'get') ||
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
      ($entity == 'state_province' and $action == 'get') ||
      ($entity == 'civi_mobile_available_contact_group' and $action == 'get') ||
      ($entity == 'civi_mobile_tag_structure' and $action == 'get') ||
      ($entity == 'civi_mobile_custom_fields' and $action == 'get')
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
  if(empty($_COOKIE["civimobile_popup_close"])) {
    if (empty($_GET['snippet'])) {
      if (Civi::settings()->get('civimobile_is_allow_public_website_url_qrcode') == 1 || CRM_Core_Permission::check('administer CiviCRM')) {

        $params = [
          'apple_link' => 'https://itunes.apple.com/us/app/civimobile/id1404824793?mt=8',
          'google_link' => 'https://play.google.com/store/apps/details?id=com.agiliway.civimobile',
          'civimobile_logo' => CRM_CiviMobileAPI_ExtensionUtil::url('/img/civimobile_logo.svg'),
          'app_store_img' => CRM_CiviMobileAPI_ExtensionUtil::url('/img/app-store.png'),
          'google_play_img' => CRM_CiviMobileAPI_ExtensionUtil::url('/img/google-play.png'),
          'civimobile_phone_img' => CRM_CiviMobileAPI_ExtensionUtil::url('/img/civimobile-phone.png'),
          'font_directory' => CRM_CiviMobileAPI_ExtensionUtil::url('/font'),
          'qr_code_link' => CRM_CiviMobileAPI_Install_Entity_ApplicationQrCode::getPath(),
          'small_popup_background_color' => '#e8ecf0',
          'advanced_popup_background_color' => '#e8ecf0',
          'button_background_color' => '#5589b7',
          'button_text_color' => 'white',
          'description_text' => 'Congratulations, your CiviCRM supports <b>CiviMobile</b> application now. You can download the mobile application at AppStore or Google PlayMarket.',
          'description_text_color' => '#3b3b3b',
          'is_showed_popup' => TRUE,
        ];

        CRM_CiviMobileAPI_Utils_HookInvoker::qrCodeBlockParams($params);

        if ($params['is_showed_popup']) {
          CRM_Core_Smarty::singleton()->assign($params);
          CRM_Core_Region::instance('page-body')->add([
            'template' => CRM_CiviMobileAPI_ExtensionUtil::path() . '/templates/CRM/CiviMobileAPI/popup.tpl',
          ]);
        }
      }
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
  if ($objectName == 'Participant' && $op == 'create') {
    CRM_CiviMobileAPI_Utils_QRcode::generateQRcode($objectId);
  }

  if ($objectName == 'Individual' && $op == 'edit') {
    try {
      $contact = CRM_Contact_BAO_Contact::findById($objectId);
      $apiKey = $contact->api_key;
    } catch (\CiviCRM_API3_Exception $e) {
      $apiKey = NULL;
    }
    if (!empty($apiKey) && CRM_CiviMobileAPI_Utils_Contact::isBlockedApp($objectId) == 1) {
      CRM_CiviMobileAPI_Utils_Contact::logoutFromMobile($objectId);
    }
  }

  if ($objectName == 'Event' && $op == 'create') {
    $qrcodeCheckinEvent = CRM_Utils_Request::retrieve('default_qrcode_checkin_event', 'String');
    $eventId = $objectId;

    if ($qrcodeCheckinEvent) {
      CRM_CiviMobileAPI_Utils_EventQrCode::setQrCodeToEvent($eventId);
    }
  }

  /**
   * This hook run only when create Case or make relationship to Case. And send
   * notification if contact or relation contact haves token.
   */
  $notificationFactory = new CRM_CiviMobileAPI_PushNotification_Utils_NotificationFactory($op, $objectName, $objectId, $objectRef, "post");
  $notificationManager = $notificationFactory->getPushNotificationManager();
  if (isset($notificationManager)) {
    $notificationManager->sendNotification();
  }

  CRM_CiviMobileAPI_Hook_Post_Register::run($op, $objectName, $objectId, $objectRef);
}

function civimobileapi_civicrm_postProcess($formName, &$form) {
  /**
   * This hook run only when create or update Activity from WEB,
   * if it has made by API notification will send
   * in 'CRM_CiviMobileAPI_ApiWrapper_Activity_Notification'.
   */
  $action = CRM_CiviMobileAPI_PushNotification_Utils_NotificationFactory::convertPostProcessAction($form);
  $formName = CRM_CiviMobileAPI_PushNotification_Utils_NotificationFactory::convertPostProcessFormName($formName);

  $objectId = null;
  if ($formName == 'ActivityInCase' && $action == 'delete') {
    $objectId = (isset($form->_caseId[0])) ? $form->_caseId[0] : null;
  }

  $notificationFactory = new CRM_CiviMobileAPI_PushNotification_Utils_NotificationFactory($action, $formName, $objectId, $form, "postProcess");
  $notificationManager = $notificationFactory->getPushNotificationManager();

  if (isset($notificationManager)) {
    $notificationManager->sendNotification();
  }
}

function civimobileapi_civicrm_alterMailParams(&$params, $context) {
  CRM_CiviMobileAPI_Hook_AlterMailParams_EventOnlineReceipt::run($params, $context);
  CRM_CiviMobileAPI_Hook_AlterMailParams_EventOfflineReceipt::run($params, $context);
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
        CRM_CiviMobileAPI_Utils_Contact::isContactHasApiKey($context['contact_id']) &&
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

/**
 * Implements hook_civicrm_permission().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_permission/
 *
 * @param $permissionList
 */
function civimobileapi_civicrm_permission(&$permissionList) {
  $permissionsPrefix = 'CiviCRM : ';

  $permissionList[CRM_CiviMobileAPI_Utils_Permission::CAN_CHECK_IN_ON_EVENT] = [
    $permissionsPrefix . CRM_CiviMobileAPI_Utils_Permission::CAN_CHECK_IN_ON_EVENT,
    ts("It means User can only update Participant status to 'Registered' or 'Attended'. Uses by QR Code."),
  ];
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
  $civiMobile = [
    'name' => ts('CiviMobile'),
    'permission' => 'administer CiviCRM',
    'operator' => NULL,
    'separator' => NULL,
  ];
  _civimobileapi_civix_insert_navigation_menu($menu, 'Administer/', $civiMobile);

  $civiMobileSettings = [
    'name' => ts('CiviMobile Settings'),
    'url' => 'civicrm/civimobile/settings',
    'permission' => 'administer CiviCRM',
    'operator' => NULL,
    'separator' => NULL,
  ];
  _civimobileapi_civix_insert_navigation_menu($menu, 'Administer/CiviMobile/', $civiMobileSettings);

  $civiMobileCalendarSettings = [
    'name' => ts('CiviMobile Calendar Settings'),
    'url' => 'civicrm/civimobile/calendar/settings',
    'permission' => 'administer CiviCRM',
    'operator' => NULL,
    'separator' => NULL,
  ];
  _civimobileapi_civix_insert_navigation_menu($menu, 'Administer/CiviMobile/', $civiMobileCalendarSettings);
}

/**
 * Implements hook_civicrm_buildForm().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_buildForm/
 */
function civimobileapi_civicrm_buildForm($formName, &$form) {
  $action = $form->getAction();
  if ($formName == 'CRM_Event_Form_ManageEvent_EventInfo' && $action == CRM_Core_Action::ADD) {
    $templatePath = realpath(dirname(__FILE__)."/templates");

    $form->add('checkbox', 'default_qrcode_checkin_event', ts('When generating QR Code tokens, use this Event'));
    CRM_Core_Region::instance('page-body')->add([
      'template' => "{$templatePath}/qrcode-checkin-event-options.tpl"
    ]);

    CRM_Core_Region::instance('page-body')->add([
      'style' => '.custom-group-' . CRM_CiviMobileAPI_Install_Entity_CustomGroup::QR_USES . ' { display:none;}'
    ]);
  }

  if ($formName == 'CRM_Event_Form_Participant' && $action == CRM_Core_Action::ADD) {
    $elementName = 'send_receipt';
    if ($form->elementExists($elementName)) {
      $element = $form->getElement($elementName);
      $element->setValue(1);
    }
  }

  (new CRM_CiviMobileAPI_Hook_BuildForm_Register)->run($formName, $form);
}

/**
 * Implements hook_civicrm_alterBadge().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_alterBadge/
 */
function civimobileapi_civicrm_alterBadge( &$labelName, &$label, &$format, &$participant ) {
  $qrCodeCustomFieldName = "custom_" . CRM_CiviMobileAPI_Utils_CustomField::getId(CRM_CiviMobileAPI_Install_Entity_CustomGroup::QR_CODES, CRM_CiviMobileAPI_Install_Entity_CustomField::QR_IMAGE);
  if (isset($format['values'][$qrCodeCustomFieldName])) {
    $link = $format['values'][$qrCodeCustomFieldName];
    $label->printImage($link, '100', '0' , 30, 30);

    //hide label
    if (!empty($format['token'])) {
      foreach ($format['token'] as $key => $token) {
        if ($token['token'] == '{participant.' . $qrCodeCustomFieldName . '}') {
          $format['token'][$key]['value'] =  '';
        }
      }
    }
  }
}
