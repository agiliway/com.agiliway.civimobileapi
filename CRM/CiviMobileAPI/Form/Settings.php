<?php
/*--------------------------------------------------------------------+
 | CiviCRM version 4.7                                                |
+--------------------------------------------------------------------+
 | Copyright CiviCRM LLC (c) 2004-2017                                |
+--------------------------------------------------------------------+
 | This file is a part of CiviCRM.                                    |
 |                                                                    |
 | CiviCRM is free software; you can copy, modify, and distribute it  |
 | under the terms of the GNU Affero General Public License           |
 | Version 3, 19 November 2007 and the CiviCRM Licensing Exception.   |
 |                                                                    |
 | CiviCRM is distributed in the hope that it will be useful, but     |
 | WITHOUT ANY WARRANTY; without even the implied warranty of         |
 | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.               |
 | See the GNU Affero General Public License for more details.        |
 |                                                                    |
 | You should have received a copy of the GNU Affero General Public   |
 | License and the CiviCRM Licensing Exception along                  |
 | with this program; if not, contact CiviCRM LLC                     |
 | at info[AT]civicrm[DOT]org. If you have questions about the        |
 | GNU Affero General Public License or the licensing of CiviCRM,     |
 | see the CiviCRM license FAQ at http://civicrm.org/licensing        |
 +-------------------------------------------------------------------*/

class CRM_CiviMobileAPI_Form_Settings extends CRM_Core_Form {

  public function preProcess() {
    parent::preProcess();

    $pushNotificationMessage = ts('To use Push Notifications you must register at <a href="https://civimobile.org/partner"  target="_blank">civimobile.org</a> and generate your own Server Key');
    $version = CRM_CiviMobileAPI_Utils_VersionController::getInstance();
    $latestCivicrmMessage = FALSE;
    $oldCivicrmMessage = FALSE;
    $serverKeyValidMessage = FALSE;
    $folderPermissionMessage = FALSE;
    $serverKeyInValidMessage = FALSE;
    $currentExtensionName = CRM_CiviMobileAPI_Utils_Extension::getCurrentExtensionName();
    $currentExtensionPath = CRM_CiviMobileAPI_Utils_Extension::getCurrentExtensionPath();
    $isCorrectExtensionName = CRM_CiviMobileAPI_Utils_Extension::isCorrectExtensionName();

    if ($version->isCurrentVersionLowerThanRepositoryVersion()) {
      $oldCivicrmMessage = ts('You are using CiviMobileAPI <strong>%1</strong>. The latest version is CiviMobileAPI <strong>%2</strong>', [
        1 => 'v' . $version->getCurrentFullVersion(),
        2 => 'v' . $version->getLatestFullVersion(),
      ]);
    } else {
      $latestCivicrmMessage = ts('Your extension version is up to date - CiviMobile <strong>%1</strong>', [1 => 'v' . $version->getCurrentFullVersion()]);
    }

    if (!CRM_CiviMobileAPI_Utils_Extension::directoryIsWritable()) {
      $folderPermissionMessage = '<strong>' . ts('Access to extension directory (%1) is denied. Please provide permission to access the extension directory', [1 => CRM_Core_Config::singleton()->extensionsDir . CRM_CiviMobileAPI_ExtensionUtil::LONG_NAME ]) . '</strong> ';
    }

    if (Civi::settings()->get('civimobile_is_server_key_valid') == 1) {
      $serverKeyValidMessage = ts('Your Server Key is valid and you can use Push Notifications.');
    } else {
      $serverKeyInValidMessage =  ts('Your Server Key is invalid. Please enter valid Server Key.');
    }

    $this->assign('isWritable', CRM_CiviMobileAPI_Utils_Extension::directoryIsWritable());
    $this->assign('serverKeyValidMessage', $serverKeyValidMessage);
    $this->assign('serverKeyInValidMessage', $serverKeyInValidMessage);
    $this->assign('pushNotificationMessage', $pushNotificationMessage);
    $this->assign('latestCivicrmMessage', $latestCivicrmMessage);
    $this->assign('oldCivicrmMessage', $oldCivicrmMessage);
    $this->assign('folderPermissionMessage', $folderPermissionMessage);
    $this->assign('currentExtensionName', $currentExtensionName);
    $this->assign('currentExtensionPath', $currentExtensionPath);
    $this->assign('isCorrectExtensionName', $isCorrectExtensionName);
    $this->assign('correctExtensionName', CRM_CiviMobileAPI_ExtensionUtil::LONG_NAME);
  }

  /**
   * AddRules hook
   */
  public function addRules() {
    $params = $this->exportValues();

    if (!empty($params['_qf_Settings_submit'])) {
      $this->addFormRule([CRM_CiviMobileAPI_Form_Settings::class, 'validateToken']);
    }
  }

  /**
   * Validates token
   *
   * @param $values
   *
   * @return array
   */
  public static function validateToken($values) {
    $errors = [];
    $tokenFieldName = 'civimobile_server_key';

    if (empty($values[$tokenFieldName]) || empty(trim($values[$tokenFieldName]))) {
      $errors[$tokenFieldName] = ts('Fields can not be empty.');
      return empty($errors) ? TRUE : $errors;
    }

    $token = trim($values[$tokenFieldName]);

    try {
      $result = civicrm_api3('CiviMobileConfirmToken', 'run', ['civicrm_server_token' => $token]);
    } catch (CiviCRM_API3_Exception $e) {
      $errors[$tokenFieldName] = ts('Error. Something went wrong. Please contact us.');
    }

    if (!empty($result['values']['response']) ) {
      if ($result['values']['response']['error'] == 1) {
        $errors[$tokenFieldName] = ts($result['values']['response']['message']);
      } else {
        Civi::settings()->set('civimobile_is_server_key_valid', 1);
      }
    }

    if (!empty($errors)) {
      Civi::settings()->set('civimobile_is_server_key_valid', 0);
    }

    return empty($errors) ? TRUE : $errors;
  }

  /**
   * Build the form object
   *
   * @throws \HTML_QuickForm_Error
   */
  public function buildQuickForm() {
    parent::buildQuickForm();

    $this->addElement('text', 'civimobile_server_key', ts('Server key'));
    $this->addElement('checkbox', 'civimobile_auto_update', ts('Automatically keep the extension up to date'));
    $this->addElement('checkbox', 'civimobile_is_allow_public_info_api', ts('Enable CiviMobile for Anonymous users'));
    $this->addElement('checkbox', 'civimobile_is_allow_public_website_url_qrcode', ts('Show a Website URL QR-code for Anonymous users'));
    $this->addElement('radio', 'civimobile_site_name_to_use', NULL, ts('Use CMS site name'), 'cms_site_name');
    $this->addElement('radio', 'civimobile_site_name_to_use', NULL, ts('Use custom site name'), 'custom_site_name');
    $this->addElement('text', 'civimobile_custom_site_name', ts('Site name'));

    $buttons = [
      [
        'type' => 'upload',
        'name' => ts('Save settings'),
        'isDefault' => TRUE,
      ],
      [
        'type' => 'submit',
        'name' => ts('Confirm server key'),
      ],
      [
        'type' => 'cancel',
        'name' => ts('Cancel'),
      ]
    ];

    if (CRM_CiviMobileAPI_Utils_VersionController::getInstance()->isCurrentVersionLowerThanRepositoryVersion()
      && !empty(CRM_CiviMobileAPI_Utils_Extension::directoryIsWritable())) {
      $buttons[] = [
        'type' => 'next',
        'name' => ts('Update CiviMobile Extension'),
      ];
    }

    $this->addButtons($buttons);
  }

  public function postProcess() {
    $params = $this->exportValues();

    if (!empty($params['_qf_Settings_submit'])) {
      if (!empty($params['civimobile_auto_update'])) {
        Civi::settings()->set('civimobile_auto_update', 1);
      }
      else {
        Civi::settings()->set('civimobile_auto_update', 0);
      }

      if (empty($params['_qf_Settings_next'])) {
        Civi::settings()->set('civimobile_server_key', $params['civimobile_server_key']);
        CRM_Core_Session::setStatus(ts('Server key updated'));
      }
    }
    elseif (!empty($params['_qf_Settings_next'])) {
      try {
        if (CRM_CiviMobileAPI_Utils_VersionController::getInstance()->isCurrentVersionLowerThanRepositoryVersion()) {
          $this->controller->setDestination(CRM_Utils_System::url('civicrm/civimobile/settings', http_build_query([])));
          CRM_CiviMobileAPI_Utils_Extension::update();

          CRM_Core_Session::setStatus(ts('CiviMobile updated'));
        }
      }
      catch (Exception $e) {
        CRM_Core_Session::setStatus($e->getMessage());
      }
    }
    elseif (!empty($params['_qf_Settings_upload'])) {
      $this->controller->setDestination(CRM_Utils_System::url('civicrm/civimobile/settings', http_build_query([])));
      if (!isset($params['civimobile_is_allow_public_info_api'])) {
        $params['civimobile_is_allow_public_info_api'] = 0;
      }
      if (!isset($params['civimobile_is_allow_public_website_url_qrcode'])) {
        $params['civimobile_is_allow_public_website_url_qrcode'] = 0;
      }
      if (!isset($params['civimobile_custom_site_name'])) {
        $params['civimobile_custom_site_name'] = '';
      }
      Civi::settings()->set('civimobile_is_allow_public_info_api', $params['civimobile_is_allow_public_info_api']);
      Civi::settings()->set('civimobile_is_allow_public_website_url_qrcode', $params['civimobile_is_allow_public_website_url_qrcode']);
      Civi::settings()->set('civimobile_site_name_to_use', $params['civimobile_site_name_to_use']);
      Civi::settings()->set('civimobile_custom_site_name', $params['civimobile_custom_site_name']);
      CRM_Core_Session::setStatus(ts('CiviMobile settings updated'));
    }
  }

  /**
   * Set defaults for form.
   */
  public function setDefaultValues() {
    $defaults = [];

    $defaults['civimobile_auto_update'] = Civi::settings()->get('civimobile_auto_update');
    $defaults['civimobile_server_key'] = Civi::settings()->get('civimobile_server_key');
    $defaults['civimobile_is_allow_public_info_api'] = CRM_CiviMobileAPI_Utils_Extension::isAllowPublicInfoApi();
    $defaults['civimobile_is_allow_public_website_url_qrcode'] = CRM_CiviMobileAPI_Utils_Extension::isAllowPublicWebisteURLQRCode();
    $defaults['civimobile_site_name_to_use'] = (!empty(Civi::settings()->get('civimobile_site_name_to_use'))) ? Civi::settings()->get('civimobile_site_name_to_use') : 'cms_site_name' ;
    $defaults['civimobile_custom_site_name'] = Civi::settings()->get('civimobile_custom_site_name');

    return $defaults;
  }

}
