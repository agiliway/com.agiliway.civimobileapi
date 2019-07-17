<?php

/**
 * Class provide File helper methods
 */
class CRM_CiviMobileAPI_Utils_File {

  public static function getUploadDirPath() {
    return Civi::paths()->getPath(Civi::settings()->get('customFileUploadDir'));
  }

  /**
   * Removes uploaded file from server
   *
   * @param $filename
   *
   * @return bool
   */
  public static function removeUploadFile($filename) {
    $uploadDirPath = self::getUploadDirPath();
    $filePath = $uploadDirPath . $filename;

    if (!file_exists($filePath)) {
      return false;
    }

    if (unlink($filePath)) {
      return true;
    }

    return false;
  }

  /**
   * Gets Contact's avatar file name
   *
   * @param $contactId
   *
   * @return bool
   */
  public static function getContactAvatarFileName($contactId) {
    try {
      $linkToAvatar = civicrm_api3('Contact', 'getvalue', array(
        'return' => "image_URL",
        'id' => $contactId,
      ));
    } catch (CiviCRM_API3_Exception $e) {
      return false;
    }

    $linkToAvatar = htmlspecialchars_decode($linkToAvatar, ENT_NOQUOTES);
    $urlQuery = parse_url($linkToAvatar, PHP_URL_QUERY);
    parse_str($urlQuery, $parsedUrlQuery);

    if (!empty($parsedUrlQuery["photo"])) {
      return $parsedUrlQuery["photo"];
    }

    return false;
  }

}
