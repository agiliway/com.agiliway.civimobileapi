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

  /**
   * @var bool
   */
  private $isWritable;

  /**
   * @var bool
   */
  private $needUpdate;

  /**
   * @var float
   */
  private $currentVersion;

  /**
   * @var float
   */
  private $latestVersion;

  public function preProcess() {
    parent::preProcess();

    $this->currentVersion = CRM_CiviMobileAPI_Utils_Version::getCurrentVersion();
    $this->latestVersion = CRM_CiviMobileAPI_Utils_Version::getLatestVersion();
    $this->isWritable = CRM_CiviMobileAPI_Utils_Version::directoryIsWritable();
    $this->assign('isWritable', $this->isWritable);

    $latestCivicrmMessage = FALSE;
    $oldCivicrmMessage = FALSE;

    if ($this->latestVersion > $this->currentVersion) {
      $oldCivicrmMessage = ts('You are using CiviMobile <strong>%1</strong>. The latest version is CiviMobile <strong>%2</strong>', [
        1 => 'v' . number_format($this->currentVersion, 1, '.', ''),
        2 => 'v' . number_format($this->latestVersion, 1, '.', ''),
      ]);

      $this->needUpdate = TRUE;
    } else {
      $latestCivicrmMessage = ts('Your extension version is up to date - CiviMobile <strong>%1</strong>', [1 => 'v' . number_format($this->currentVersion, 1, '.', '')]);
      $this->needUpdate = FALSE;
    }

    $folderPermissionMessage = FALSE;
    if (!$this->isWritable) {
      $folderPermissionMessage = '<strong>' . ts('Access to extension directory (%1) is denied. Please provide permission to access the extension directory', [1 => CRM_Core_Config::singleton()->extensionsDir . CRM_CiviMobileAPI_ExtensionUtil::LONG_NAME ]) . '</strong> ';
    }
    $serverKeyValidMessage = FALSE;
    $serverKeyInValidMessage = FALSE;
    if (Civi::settings()->get('civimobile_is_server_key_valid') == 1) {
      $serverKeyValidMessage = ts('Your Server Key is valid and you can use Push Notifications.');
    } else {
      $serverKeyInValidMessage =  ts('Your Server Key is invalid. Please enter valid Server Key.');
    }

    $pushNotificationMessage = ts('To use Push Notifications you must register at <a href="https://civimobile.org/partner"  target="_blank">civimobile.org</a> and generate your own Server Key');

    $this->assign('serverKeyValidMessage', $serverKeyValidMessage);
    $this->assign('serverKeyInValidMessage', $serverKeyInValidMessage);
    $this->assign('pushNotificationMessage', $pushNotificationMessage);
    $this->assign('latestCivicrmMessage', $latestCivicrmMessage);
    $this->assign('oldCivicrmMessage', $oldCivicrmMessage);
    $this->assign('folderPermissionMessage', $folderPermissionMessage);
  }

  /**
   * AddRules hook
   */
  public function addRules() {
    $params = $this->exportValues();

    if (empty($params['_qf_Settings_next'])) {
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
   * Build the form object.
   *
   * @return void
   * @throws \HTML_QuickForm_Error
   */
  public function buildQuickForm() {
    parent::buildQuickForm();

    $this->addElement('text', 'civimobile_server_key', ts('Server key'));
    $this->addElement('checkbox', 'civimobile_auto_update', ts('Automatically keep the extension up to date'));

    $buttons = [
      [
        'type' => 'submit',
        'name' => ts('Confirm server key'),
        'isDefault' => TRUE,
      ],
      [
        'type' => 'cancel',
        'name' => ts('Cancel'),
      ]
    ];

    if ($this->needUpdate && !empty(CRM_CiviMobileAPI_Utils_Version::directoryIsWritable())) {
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
        if ($this->latestVersion > $this->currentVersion) {
          $this->controller->setDestination(CRM_Utils_System::url('civicrm/civimobile/settings', http_build_query([])));
          CRM_CiviMobileAPI_Utils_Version::update($this->latestVersion);

          CRM_Core_Session::setStatus(ts('CiviMobile updated'));
        }
      }
      catch (Exception $e) {
        CRM_Core_Session::setStatus($e->getMessage());
      }
    }
  }

  /**
   * Set defaults for form.
   */
  public function setDefaultValues() {
    $defaults = [];

    $defaults['civimobile_auto_update'] = Civi::settings()->get('civimobile_auto_update');
    $defaults['civimobile_server_key'] = Civi::settings()->get('civimobile_server_key');

    return $defaults;
  }

}
