<?php

class CRM_CiviMobileAPI_Utils_ParticipantQrCode {

  /**
   * Sets QR code for Event
   *
   * @param $participantId
   * @param $eventId
   * @param $contactId
   * @param $hash
   * @param $image
   * @return bool
   */
  public static function setQrCodeToParticipant($participantId, $eventId, $contactId, $hash, $image) {
    $qrEventIdFieldName = "custom_" . CRM_CiviMobileAPI_Utils_CustomField::getId(CRM_CiviMobileAPI_Install_Entity_CustomGroup::QR_CODES, CRM_CiviMobileAPI_Install_Entity_CustomField::QR_EVENT_ID);
    $qrHashFieldName = "custom_" . CRM_CiviMobileAPI_Utils_CustomField::getId(CRM_CiviMobileAPI_Install_Entity_CustomGroup::QR_CODES, CRM_CiviMobileAPI_Install_Entity_CustomField::QR_CODE);
    $qrImageFieldName = "custom_" . CRM_CiviMobileAPI_Utils_CustomField::getId(CRM_CiviMobileAPI_Install_Entity_CustomGroup::QR_CODES, CRM_CiviMobileAPI_Install_Entity_CustomField::QR_IMAGE);

    try {
      civicrm_api3('Participant', 'create', [
        'id' => $participantId,
        'contact_id' => $contactId,
        $qrEventIdFieldName => $eventId,
        $qrHashFieldName  => $hash,
        $qrImageFieldName  => $image,
      ]);
    } catch (CiviCRM_API3_Exception $e) {
      return false;
    }

    return true;
  }

  /**
   * Gets QR code info
   *
   * @param $participantId
   * @return array|null
   */
  public static function getQrCodeInfo($participantId) {
    $customQrCode = "custom_" . CRM_CiviMobileAPI_Utils_CustomField::getId(CRM_CiviMobileAPI_Install_Entity_CustomGroup::QR_CODES, CRM_CiviMobileAPI_Install_Entity_CustomField::QR_CODE);
    $customQrImage = "custom_" . CRM_CiviMobileAPI_Utils_CustomField::getId(CRM_CiviMobileAPI_Install_Entity_CustomGroup::QR_CODES, CRM_CiviMobileAPI_Install_Entity_CustomField::QR_IMAGE);
    $customEventId = "custom_" . CRM_CiviMobileAPI_Utils_CustomField::getId(CRM_CiviMobileAPI_Install_Entity_CustomGroup::QR_CODES, CRM_CiviMobileAPI_Install_Entity_CustomField::QR_EVENT_ID);

    try {
      $participant = civicrm_api3('Participant', 'getsingle', [
        'sequential' => 1,
        'id' => $participantId,
        'return' => [$customQrCode, $customQrImage, $customEventId]
      ]);
    } catch (CiviCRM_API3_Exception $e) {
      $participant = [];
    }

    if (!empty($participant)) {
      return [
        'qr_code_image' => $participant[$customQrImage],
        'qr_code_hash'  => $participant[$customQrCode],
        'qr_event_id'   => $participant[$customEventId]
      ];
    }

    return NULL;
  }

}
