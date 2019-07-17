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
      $path = $directoryName . DIRECTORY_SEPARATOR . 'participantId_' . $participantId . '.png';
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

      $entityFileDAO = new CRM_Core_DAO_EntityFile();
      $entityFileDAO->entity_table = 'civicrm_participant';
      $entityFileDAO->entity_id = $participantId;
      $entityFileDAO->find(true);
      $fileId = $entityFileDAO->file_id;
      $protocol = stripos($_SERVER['SERVER_PROTOCOL'],'https') === true ? 'https://' : 'http://';
      $fileUrlOther = $protocol . $_SERVER['SERVER_NAME'] . CRM_Utils_System::url('civicrm/file', 'reset=1&filename=participantId_' . $participantId . '.png&mime-type=image/png');
      $fileUrl = $protocol . $_SERVER['SERVER_NAME'] . CRM_Utils_System::url('civicrm/file', 'reset=1&id=' . $fileId . '&eid=' . $participantId);
      CRM_CiviMobileAPI_Utils_ParticipantQrCode::setQrCodeToParticipant($participantId, $eventId, $contactId, $hashCode, $fileUrl);
    }
  }

}
