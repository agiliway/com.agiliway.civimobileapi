<?php

class CRM_CiviMobileAPI_Utils_Checklist {

  /**
   * @var array
   */
  private $checkedItems = [];

  /**
   * @var array
   */
  private $systemInfo = [];

  /**
   * Returns all checked items in object
   *
   * @return array
   */
  public function getCheckedItemsResult() {
    return $this->checkedItems;
  }

  /**
   * Calls all available methods to check
   * Runs all methods which name starts from '_check'
   */
  public function checkAllAvailableItems() {
    $classMethods = get_class_methods($this);

    foreach ($classMethods as $method) {
      if (preg_match('/^_check/', $method)) {
        $this->$method();
      }
    }
  }

  /**
   * Calls all available system info methods and returns report
   * Runs all methods which name starts from '_si'(System Info)
   *
   * @return array
   */
  public function getSystemInfoReport() {
    $classMethods = get_class_methods($this);

    foreach ($classMethods as $method) {
      if (preg_match('/^_si/', $method)) {
        $this->$method();
      }
    }

    return $this->systemInfo;
  }

  /**
   * Checks Extension Version
   *
   * @return bool
   */
  public function _checkExtensionVersion() {
    $version = CRM_CiviMobileAPI_Utils_VersionController::getInstance();
    $isOlderVersion = $version->isCurrentVersionLowerThanRepositoryVersion();
    $this->checkedItems['latest_version']['title'] = 'Do you have last extension version?';

    if ($isOlderVersion) {
      $this->checkedItems['latest_version']['message'] = ts('You are using CiviMobileAPI <strong>%1</strong>. The latest version is CiviMobileAPI <strong>%2</strong>', [
        1 => 'v' . $version->getCurrentFullVersion(),
        2 => 'v' . $version->getLatestFullVersion(),
      ]);
      $this->checkedItems['latest_version']['status'] = 'warning';

      return false;
    } else {
      $this->checkedItems['latest_version']['message'] = ts('Your extension version is up to date - CiviMobile <strong>%1</strong>', [1 => 'v' . $version->getCurrentFullVersion()]);
      $this->checkedItems['latest_version']['status'] = 'success';
    }

    return !$isOlderVersion;
  }

  /**
   * Checks CiviCRM Supported version
   *
   * @return bool
   */
  public function _checkCiviCRMSupportedVersion() {
    $isCiviCRMSupported = CRM_CiviMobileAPI_Utils_Extension::LATEST_SUPPORTED_CIVICRM_VERSION <= CRM_Utils_System::version();
    $this->checkedItems['is_civicrm_version_supported'] = [
      'title' => 'Is CiviCRM version supported by CiviMobileAPI?',
      'message' => $isCiviCRMSupported ? 'Your CiviCRM version is supported' : 'You should to install CiviCRM with minimum version ' . CRM_CiviMobileAPI_Utils_Extension::LATEST_SUPPORTED_CIVICRM_VERSION,
      'status' => $isCiviCRMSupported ? 'success' : 'warning',
    ];

    return $isCiviCRMSupported;
  }

  /**
   * Checks Is valid Extension folder name
   *
   * @param bool $boolOnly
   * @return bool
   */
  public function _checkExtensionFolderName($boolOnly = false) {
    $isRightExtensionFolderName = CRM_CiviMobileAPI_Utils_Extension::hasExtensionRightFolderName();

    if ($boolOnly) {
      return $isRightExtensionFolderName;
    }

    $this->checkedItems['is_civimobile_ext_has_right_folder_name'] = [
      'title' => 'Is CivimobileAPI`s folder name correct?',
      'message' => $isRightExtensionFolderName ? 'Folder name is correct.' : 'You should rename CivimobileAPI extension`s folder to <b>"' . CRM_CiviMobileAPI_ExtensionUtil::LONG_NAME . '"</b> and then reinstall extension.',
      'status' => $isRightExtensionFolderName ? 'success' : 'error',
    ];

    return $isRightExtensionFolderName;
  }

  /**
   * Checks is extension folder writable
   *
   * @return bool|float
   */
  public function _checkIsExtensionFolderWritable() {
    if ($this->_checkExtensionFolderName(true)) {
      $isDirectoryWritable = CRM_CiviMobileAPI_Utils_Extension::directoryIsWritable();
      $this->checkedItems['is_directory_writable'] = [
        'title' => 'Is CiviMobileAPI extension`s directory writable?',
        'message' => $isDirectoryWritable ? 'Extension directory is writable.' : 'Please give permissions to write for CiviMobileAPI extension`s directory.',
        'status' => $isDirectoryWritable ? 'success' : 'warning',
      ];

      return $isDirectoryWritable;
    }

    return false;
  }

  /**
   * Checks is additional Wordpress plugin installed
   *
   * @return bool
   */
  public function _checkIsAdditionWpRestPluginInstalled() {
    if (CRM_CiviMobileAPI_Utils_CmsUser::getInstance()->getSystem() == CRM_CiviMobileAPI_Utils_CmsUser::CMS_WORDPRESS) {
      $this->checkedItems['is_wp_rest_plugin_active']['title'] = 'Do you have the additional plugin for Wordpress?';
      $isWpRestPluginActive = (new CRM_CiviMobileAPI_Utils_RestPath())->isWpRestPluginActive();

      if ($isWpRestPluginActive) {
        $this->checkedItems['is_wp_rest_plugin_active']['message'] = 'You have the additional plugin for Wordpress.';
        $this->checkedItems['is_wp_rest_plugin_active']['status'] = 'success';
      } else {
        $this->checkedItems['is_wp_rest_plugin_active']['message'] = 'You don`t have the additional plugin for Wordpress. CivimobileAPI cannot work without this plugin. You can read about this plugin on <a href="https://github.com/mecachisenros/civicrm-wp-rest">https://github.com/mecachisenros/civicrm-wp-rest</a>';
        $this->checkedItems['is_wp_rest_plugin_active']['status'] = 'error';
      }

      return $isWpRestPluginActive;
    }

    return false;
  }

  /**
   * Checks is Server key valid
   *
   * @return bool
   */
  public function _checkIsServerKeyValid() {
    $isServerKeyValid = Civi::settings()->get('civimobile_is_server_key_valid') == 1;

    $this->checkedItems['is_server_key_valid']['title'] = 'Is server key valid?';

    if ($isServerKeyValid) {
      $this->checkedItems['is_server_key_valid']['message'] = 'Your server key is valid.';
      $this->checkedItems['is_server_key_valid']['status'] = 'success';
    } else {
      $this->checkedItems['is_server_key_valid']['message'] = 'Your server key is invalid. Please add correct server key on <a href="' . CRM_Utils_System::url('civicrm/civimobile/settings') . '" target="_blank">CiviMobile Settings</a> to activate Push Notifications on application.';
      $this->checkedItems['is_server_key_valid']['status'] = 'warning';
    }

    return $isServerKeyValid;
  }

  /**
   * Checks is php-extension enabled
   *
   * @return bool
   */
  public function _checkIsCurlEnabled() {
    $this->checkedItems['is_curl_enabled']['title'] = 'Is curl php-extension enabled?';
    $isCurlEnabled = !in_array('curl', get_loaded_extensions());

    if ($isCurlEnabled) {
      $this->checkedItems['is_curl_enabled']['message'] = 'Curl php-extension is not available on your web server. It`s necessary for PushNotifications.';
      $this->checkedItems['is_curl_enabled']['status'] = 'error';
    } else {
      $this->checkedItems['is_curl_enabled']['message'] = 'Curl php-extension is available on your web server.';
      $this->checkedItems['is_curl_enabled']['status'] = 'success';
    }

    return $isCurlEnabled;
  }

  /**
   * Checks is Cron running
   *
   * @return bool
   */
  public function _checkCron() {
    $checkCron = CRM_Utils_Check_Component_Env::checkLastCron();

    $this->checkedItems['last_cron']['title'] = 'Is CRON running correct?';
    $this->checkedItems['last_cron']['message'] = $checkCron[0]->getTitle() . "<br>" . $checkCron[0]->getMessage();

    switch ($checkCron[0]->getLevel()) {
      case CRM_Utils_Check::severityMap(\Psr\Log\LogLevel::INFO):
        $this->checkedItems['last_cron']['status'] = 'success';
        break;
      case CRM_Utils_Check::severityMap(\Psr\Log\LogLevel::WARNING):
        $this->checkedItems['last_cron']['status'] = 'warning';
        break;
      case CRM_Utils_Check::severityMap(\Psr\Log\LogLevel::ERROR):
        $this->checkedItems['last_cron']['status'] = 'error';
        break;
    }

    return $checkCron[0]->getLevel() == \Psr\Log\LogLevel::INFO;
  }

  /**
   *  Adds CiviCRM version to $systemInfo
   */
  public function _siCiviCRMVersion() {
    $this->systemInfo[] = [
      'title' => 'CiviCRM version',
      'message' => CRM_Utils_System::version(),
    ];
  }

  /**
   *  Adds CMS to $systemInfo
   */
  public function _siCMS() {
    $this->systemInfo[] = [
      'title' => 'CMS',
      'message' => CRM_CiviMobileAPI_Utils_CmsUser::getInstance()->getSystem(),
    ];
  }

  /**
   *  Adds current and latest available versions to $systemInfo
   */
  public function _siCiviMobileVersions() {
    $version = CRM_CiviMobileAPI_Utils_VersionController::getInstance();

    $this->systemInfo[] = [
      'title' => 'Your CiviMobileAPI extension version',
      'message' => $version->getCurrentFullVersion(),
    ];
    $this->systemInfo[] = [
      'title' => 'Latest available CiviMobileAPI extension version',
      'message' => $version->getLatestFullVersion(),
    ];
  }

  /**
   *  Adds rest path to $systemInfo
   */
  public function _siRestPath() {
    $this->systemInfo[] = [
      'title' => 'Rest path',
      'message' => (new CRM_CiviMobileAPI_Utils_RestPath())->get(),
    ];
  }

  /**
   *  Adds absolute rest url to $systemInfo
   */
  public function _siAbsoluteRestUrl() {
    $this->systemInfo[] = [
      'title' => 'Absolute rest url',
      'message' => (new CRM_CiviMobileAPI_Utils_RestPath())->getAbsoluteUrl(),
    ];
  }

}
