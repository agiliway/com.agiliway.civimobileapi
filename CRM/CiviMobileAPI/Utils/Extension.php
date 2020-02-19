<?php

/**
 * Class provide extension helper methods
 */
class CRM_CiviMobileAPI_Utils_Extension {

  /**
   * Update extension to latest
   *
   * @throws \Exception
   */
  public static function update() {
    CRM_Extension_System::singleton()->getDownloader()->download(
      CRM_CiviMobileAPI_ExtensionUtil::LONG_NAME,
      self::getLatestVersionDownloadLink()
    );

    self::updateSchemaVersion();
  }

  /**
   * Get latest version of extension download link
   */
  public static function getLatestVersionDownloadLink() {
    $version = CRM_CiviMobileAPI_Utils_VersionController::getInstance();
    $downloadUrl = 'https://lab.civicrm.org/extensions/civimobileapi/-/archive/';
    $downloadUrl .= $version->getLatestFullVersion() . '/civimobileapi-' . $version->getLatestFullVersion() . '.zip';

    return $downloadUrl;
  }

  /**
   * Updates schema version
   *
   * @throws \Exception
   */
  public static function updateSchemaVersion() {
    $queue = CRM_Extension_Upgrades::createQueue();

    $taskCtx = new CRM_Queue_TaskContext();
    $taskCtx->queue = $queue;
    $taskCtx->log = CRM_Core_Error::createDebugLogger();

    $extensionPath = CRM_Core_Config::singleton()->extensionsDir . CRM_CiviMobileAPI_ExtensionUtil::LONG_NAME;
    $upgrader = new CRM_CiviMobileAPI_Upgrader(CRM_CiviMobileAPI_ExtensionUtil::LONG_NAME, $extensionPath);

    $currentRevision = $upgrader->getCurrentRevision();
    $newestRevision = 0;
    $revisions = $upgrader->getRevisions();

    foreach ($revisions as $revision) {
      if ($revision > $currentRevision) {
        $title = ts('Upgrade %1 to revision %2', [
          1 => CRM_CiviMobileAPI_ExtensionUtil::LONG_NAME,
          2 => $revision,
        ]);

        $task = new CRM_Queue_Task(
          [get_class($upgrader), '_queueAdapter'],
          ['upgrade_' . $revision],
          $title
        );
        $task->run($taskCtx);

        $newestRevision = $revision;
      }
    }

    if ($newestRevision) {
      CRM_Core_BAO_Extension::setSchemaVersion(CRM_CiviMobileAPI_ExtensionUtil::LONG_NAME, $newestRevision);
    }
  }

  /**
   * Is extension folder is writable
   *
   * @return float
   */
  public static function directoryIsWritable() {
    $extensionPath = CRM_Core_Config::singleton()->extensionsDir . CRM_CiviMobileAPI_ExtensionUtil::LONG_NAME;

    return is_writable_r($extensionPath);
  }

  /**
   * Returns current extension path
   * Be careful when move this method
   *
   * @return bool|string
   */
  public static function getCurrentExtensionPath() {
    return realpath(__DIR__ . '/../../../');
  }

  /**
   * Returns current extension name
   *
   * @return string
   */
  public static function getCurrentExtensionName() {
    $path = static::getCurrentExtensionPath();
    $separatedPath = explode('/', $path);

    return end($separatedPath);
  }

  /**
   * Checks if is correct extension name
   *
   * @return bool
   */
  public static function isCorrectExtensionName() {
    return static::getCurrentExtensionName() == CRM_CiviMobileAPI_ExtensionUtil::LONG_NAME;
  }

  /**
   * Is allow public info api
   *
   * @return int
   */
  public static function isAllowPublicInfoApi() {
    return (Civi::settings()->get('civimobile_is_allow_public_info_api') == 1) ? 1 : 0;
  }

  /**
   * Show a Website URL QR-code for Anonymous users
   *
   * @return int
   */
  public static function isAllowPublicWebisteURLQRCode() {
    return (Civi::settings()->get('civimobile_is_allow_public_website_url_qrcode') == 1) ? 1 : 0;
  }

  /**
   * Is allow public info
   *
   * @return int
   */
  public static function isAllowCmsRegistration() {
    $config = CRM_Core_Config::singleton();

    return (bool) $config->userSystem->isUserRegistrationPermitted() ? 1 : 0;
  }

  /**
   * Get site name from settings
   *
   * @return string|null
   */
  public static function getSiteName() {
    if(Civi::settings()->get('civimobile_site_name_to_use') == 'custom_site_name') {
      return Civi::settings()->get('civimobile_custom_site_name');
    }

    return CRM_CiviMobileAPI_Utils_Cms::getSiteName();
  }

  /**
   * Has extension right folder name?
   *
   * @return bool
   */
  public static function hasExtensionRightFolderName() {
    $infoFilePath = CRM_Core_Config::singleton()->extensionsDir . CRM_CiviMobileAPI_ExtensionUtil::LONG_NAME . '/civimobileapi.php';
    if (file_exists($infoFilePath)) {
      return TRUE;
    }
    return FALSE;
  }

}
