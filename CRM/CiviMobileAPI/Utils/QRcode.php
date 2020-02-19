<?php

/**
 * Class provide QRcode helper methods
 */
class CRM_CiviMobileAPI_Utils_QRcode {

  /**
   * Generates QRcode for participant
   * Saves QRcode in Participant's custom fields
   *
   * @param $participantId
   *
   * @throws \api_Exception
   */
  public static function generateQRcode($participantId) {
    $participant = new CRM_Event_BAO_Participant();
    $participant->id = $participantId;
    $participantExist = $participant->find(TRUE);

    if (empty($participantExist)) {
      throw new api_Exception('Participant does not exist.', 'participant_does_not_exist');
    }

    $contactId = $participant->contact_id;
    $eventId = $participant->event_id;

    if (CRM_CiviMobileAPI_Utils_EventQrCode::isEventUsedQrCode($eventId) == 1) {
      $hashCode = hash('ripemd160', "eventId:" . $eventId . "pId:" . $participantId);
      $config = CRM_Core_Config::singleton();
      $directoryName = $config->uploadDir . DIRECTORY_SEPARATOR . 'qr';
      CRM_Utils_File::createDir($directoryName);
      $imageName = self::generateImageName($participantId);
      $path = $directoryName . DIRECTORY_SEPARATOR . $imageName;
      $params = [
        'attachFile_1' => [
          'uri' => $path,
          'location' => $path,
          'description' => '',
          'type' => 'image/png'
        ],
      ];

      \PHPQRCode\QRcode::png("http://civimobile.org/events?qr=" . $participantId . '_' . $hashCode, $path, 'L', 9, 3);
      CRM_Core_BAO_File::processAttachment($params, 'civicrm_participant', $participantId);
      $fileUrl = CRM_CiviMobileAPI_Utils_File::getFileUrl($participantId,'civicrm_participant', self::generateImageName($participantId));
      CRM_CiviMobileAPI_Utils_ParticipantQrCode::setQrCodeToParticipant($participantId, $eventId, $contactId, $hashCode, $fileUrl);
    }
  }


  /**
   * Generates image name
   *
   * @param $participantId
   *
   * @return string
   */
  private static function generateImageName($participantId) {
    return 'participantId_' . $participantId . '.png';
  }

}
