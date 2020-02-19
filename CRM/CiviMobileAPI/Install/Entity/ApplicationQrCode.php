<?php

class CRM_CiviMobileAPI_Install_Entity_ApplicationQrCode implements CRM_CiviMobileAPI_Install_Entity_InstallInterface {

  /**
   * File name for Application QrCode
   *
   * @var string
   */
  const FILE_NAME = 'qrCodeForApplication.png';

  public static function getPath() {
    return CRM_CiviMobileAPI_Utils_File::getFileUrl(1, 'civimobile', self::FILE_NAME);
  }

  public function install() {
    $this->generateQrCode();
  }

  public function generateQrCode() {
    $config = CRM_Core_Config::singleton();
    $currentCMS = CRM_CiviMobileAPI_Utils_CmsUser::getInstance()->getSystem();
    $directoryName = $config->uploadDir . DIRECTORY_SEPARATOR . 'qr';
    CRM_Utils_File::createDir($directoryName);
    $imageName = self::FILE_NAME;
    $path = $directoryName . DIRECTORY_SEPARATOR . $imageName;

    if ($currentCMS == CRM_CiviMobileAPI_Utils_CmsUser::CMS_JOOMLA ) {
      $siteUrl = str_replace("/administrator/", "", CIVICRM_UF_BASEURL);
    } else {
      $siteUrl = CIVICRM_UF_BASEURL;
    }
    $params = [
      'attachFile_1' => [
        'uri' => $path,
        'location' => $path,
        'description' => '',
        'type' => 'image/png'
      ],
    ];

    $qrCodeContent = 'https://civimobile.org/download?domain=' . $siteUrl;
    \PHPQRCode\QRcode::png($qrCodeContent, $path, 'L', 9, 3);
    CRM_Core_BAO_File::processAttachment($params, 'civimobile', 1);
  }

}
