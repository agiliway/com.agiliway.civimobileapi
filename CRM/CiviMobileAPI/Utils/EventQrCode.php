<?php

class CRM_CiviMobileAPI_Utils_EventQrCode {
  /**
   * Checks is Event used QR code
   *
   * @param $eventId
   *
   * @return int|NULL
   */
  public static function isEventUsedQrCode($eventId) {
    $customFieldName = "custom_" . CRM_CiviMobileAPI_Utils_CustomField::getId(CRM_CiviMobileAPI_Install_Entity_CustomGroup::QR_USES, CRM_CiviMobileAPI_Install_Entity_CustomField::IS_QR_USED);

    try {
      $event = civicrm_api3('Event', 'getsingle', [
        'id' => $eventId,
        'return' => [$customFieldName]
      ]);
    } catch (CiviCRM_API3_Exception $e) {
      $event = [];
    }

    return (!empty($event[$customFieldName]) ? $event[$customFieldName] : NULL);
  }

  /**
   * Sets QR code for Event
   *
   * @param $eventId
   * @return bool
   */
  public static function setQrCodeToEvent($eventId) {
    $customFieldName = "custom_" . CRM_CiviMobileAPI_Utils_CustomField::getId(CRM_CiviMobileAPI_Install_Entity_CustomGroup::QR_USES, CRM_CiviMobileAPI_Install_Entity_CustomField::IS_QR_USED);

    try {
      civicrm_api3('Event', 'create', array(
        'id' => $eventId,
        $customFieldName  => 1
      ));
    } catch (CiviCRM_API3_Exception $e) {
      return false;
    }

    return true;
  }

}
