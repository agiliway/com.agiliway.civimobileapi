<?php

/**
 * Class retrieve version of extension and compare
 */
class CRM_CiviMobileAPI_Utils_VersionController {

  /**
   * @var CRM_CiviMobileAPI_Utils_VersionController
   */
  private static $instance;

  /**
   * Major version of current extension
   */
  private $currentMajorVersion = 0;

  /**
   * Minor version of current extension
   */
  private $currentMinorVersion = 0;

  /**
   * Patch version of current extension
   */
  private $currentPatchVersion = 0;

  /**
   * Latest major version of extension in remote repository
   */
  private $latestMajorVersion = 0;

  /**
   * Latest minor version of extension in remote repository
   */
  private $latestMinorVersion = 0;

  /**
   * Latest patch version of extension in remote repository
   */
  private $latestPatchVersion = 0;

  /**
   * Is sets version from repository
   */
  private $isSetVersionFromRepository = false;

  /**
   * CRM_CiviMobileAPI_Utils_VersionController constructor.
   */
  private function __construct() {
    $this->setVersionFromExtension();
  }

  /**
   * Sets version from current extension
   */
  public function setVersionFromExtension() {
    $currentVersion = $this->parseVersion($this->getCurrentVersion());
    $this->currentMajorVersion = $currentVersion['major'];
    $this->currentMinorVersion = $currentVersion['minor'];
    $this->currentPatchVersion = $currentVersion['patch'];
  }

  /**
   * Sets version from repository
   */
  public function setVersionFromRepository() {
    if ($this->isSetVersionFromRepository) {
      return;
    }

    $latestVersion = $this->parseVersion($this->getLatestVersion());
    $this->latestMajorVersion = $latestVersion['major'];
    $this->latestMinorVersion = $latestVersion['minor'];
    $this->latestPatchVersion = $latestVersion['patch'];
    $this->isSetVersionFromRepository = true;
  }

  /**
   * Parse version
   *
   * @param $versionString
   *
   * @return array
   */
  private function parseVersion($versionString) {
    $version = [
      'major' => 0,
      'minor' => 0,
      'patch' => 0,
    ];

    $versionString = trim(trim((string) $versionString, 'v'));

    if (empty($versionString)) {
      return $version;
    }

    $splitVersion = explode('.', $versionString);

    if (isset($splitVersion[0])) {
      $version['major'] = (int) $splitVersion[0];
    }

    if (isset($splitVersion[1])) {
      $version['minor'] = (int) $splitVersion[1];
    }

    if (isset($splitVersion[2])) {
      $version['patch'] = (int) $splitVersion[2];
    }

    return $version;
  }

  /**
   * Gets current version of extension
   *
   * @return float
   */
  private function getCurrentVersion() {
    $extensionPath = CRM_Core_Config::singleton()->extensionsDir . CRM_CiviMobileAPI_ExtensionUtil::LONG_NAME;
    $infoFilePath = $extensionPath . '/info.xml';

    try {
      $extensionInfo = CRM_Extension_Info::loadFromFile($infoFilePath);

      return (string) $extensionInfo->version;
    }
    catch (Exception $e) {
      return '';
    }
  }

  /**
   * Gets latest version of extension
   *
   * @return string
   */
  private function getLatestVersion() {
    if (!function_exists('curl_init')) {
      return '';
    }
    $ch = curl_init();

    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_URL, 'https://lab.civicrm.org/api/v4/projects/460/releases/');
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
      'User-Agent: Awesome-Octocat-App',
    ]);
    $response = json_decode(curl_exec($ch), TRUE);

    return (string) $response[0]['tag_name'];
  }

  /**
   * Is current version lower than repository version
   * Is need to update extension
   *
   * @return bool
   */
  public function isCurrentVersionLowerThanRepositoryVersion() {
    $this->setVersionFromRepository();

    if ($this->getCurrentMajorVersion() < $this->getLatestMajorVersion()) {
      return true;
    }

    if (($this->getCurrentMajorVersion() == $this->getLatestMajorVersion())
      && ($this->getCurrentMinorVersion() < $this->getLatestMinorVersion())) {
      return true;
    }

    if (($this->getCurrentMajorVersion() == $this->getLatestMajorVersion())
      && ($this->getCurrentMinorVersion() == $this->getLatestMinorVersion())
    && ($this->getCurrentPatchVersion() < $this->getLatestPatchVersion())) {
      return true;
    }

    return false;
  }

  /**
   * Gets the instance
   */
  public static function getInstance()
  {
    if (null === static::$instance) {
      static::$instance = new static();
    }

    return static::$instance;
  }

  /**
   * Gets Latest full version of extension in remote repository
   *
   * @return string
   */
  public function getLatestFullVersion() {
    $this->setVersionFromRepository();
    return $this->getLatestMajorVersion() . '.' . $this->getLatestMinorVersion() . '.' . $this->getLatestPatchVersion();
  }

  /**
   * Gets current full version of extension in remote repository
   *
   * @return string
   */
  public function getCurrentFullVersion() {
    return $this->getCurrentMajorVersion() . '.' . $this->getCurrentMinorVersion() . '.' . $this->getCurrentPatchVersion();
  }

  /**
   * @return int
   */
  public function getCurrentMajorVersion() {
    return $this->currentMajorVersion;
  }

  /**
   * @return int
   */
  public function getCurrentMinorVersion() {
    return $this->currentMinorVersion;
  }

  /**
   * @return int
   */
  public function getCurrentPatchVersion() {
    return $this->currentPatchVersion;
  }

  /**
   * @return int
   */
  public function getLatestMajorVersion() {
    $this->setVersionFromRepository();
    return $this->latestMajorVersion;
  }

  /**
   * @return int
   */
  public function getLatestMinorVersion() {
    $this->setVersionFromRepository();
    return $this->latestMinorVersion;
  }

  /**
   * @return mixed
   */
  public function getLatestPatchVersion() {
    $this->setVersionFromRepository();
    return $this->latestPatchVersion;
  }

  private function __clone() {}

  private function __wakeup() {}

}
