<?php

/**
 * Class provide extension version helper methods
 */
class CRM_CiviMobileAPI_Utils_Version {

  /**
   * Gets current version of extension
   *
   * @return float
   */
  public static function getCurrentVersion() {
    $extensionPath = CRM_Core_Config::singleton()->extensionsDir . CRM_CiviMobileAPI_ExtensionUtil::LONG_NAME;
    $infoFilePath = $extensionPath . '/info.xml';

    try {
      $extensionInfo = CRM_Extension_Info::loadFromFile($infoFilePath);

      return (float) $extensionInfo->version;
    }
    catch (Exception $e) {
      return 0;
    }
  }

  /**
   * Gets latest version of extension
   *
   * @return float
   */
  public static function getLatestVersion() {
    if (function_exists('curl_init')) {
      $ch = curl_init();

      curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
      curl_setopt($ch, CURLOPT_URL, 'https://api.github.com/repos/agiliway/' . CRM_CiviMobileAPI_ExtensionUtil::LONG_NAME . '/releases/latest');
      curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'User-Agent: Awesome-Octocat-App'
      ]);

      $response = json_decode(curl_exec($ch), TRUE);

      return (float) trim($response['tag_name'], 'v');
    }
    else {
      return 0;
    }
  }

  /**
   * @param float $version
   *
   * @throws \CRM_Extension_Exception
   * @throws \Exception
   */
  public static function update($version) {
    if ((int) $version == $version) {
      $version = number_format($version, 1);
    }
    
    $downloadUrl = 'https://codeload.github.com/agiliway/' . CRM_CiviMobileAPI_ExtensionUtil::LONG_NAME . '/legacy.zip/' . 'v' . $version;

    CRM_Extension_System::singleton()->getDownloader()->download(CRM_CiviMobileAPI_ExtensionUtil::LONG_NAME, $downloadUrl);

    self::updateSchemaVersion();
  }

  /**
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
   * @return float
   */
  public static function directoryIsWritable() {
    $extensionPath = CRM_Core_Config::singleton()->extensionsDir . CRM_CiviMobileAPI_ExtensionUtil::LONG_NAME;

    return is_writable_r($extensionPath);
  }

}