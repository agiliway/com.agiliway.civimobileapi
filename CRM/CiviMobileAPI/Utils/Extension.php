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
    $downloadUrl = 'https://codeload.github.com/agiliway/' . CRM_CiviMobileAPI_ExtensionUtil::LONG_NAME . '/legacy.zip/';
    $downloadUrl .=  'v' . $version->getLatestMajorVersion() . '.' . $version->getLatestMinorVersion();

    if ($version->getLatestPatchVersion() != 0) {
      $downloadUrl .= '.' . $version->getLatestPatchVersion();
    }

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

}
