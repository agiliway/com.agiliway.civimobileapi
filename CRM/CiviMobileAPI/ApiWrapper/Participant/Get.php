<?php

/**
 * @deprecated will be deleted in version 7.0.0
 */
class CRM_CiviMobileAPI_ApiWrapper_Participant_Get implements API_Wrapper {

  /**
   * Interface for interpreting api input
   *
   * @param array $apiRequest
   *
   * @return array
   */
  public function fromApiInput($apiRequest) {
    return $apiRequest;
  }

  /**
   * Interface for interpreting api output
   *
   * @param $apiRequest
   * @param $result
   *
   * @return array
   * @throws API_Exception
   */
  public function toApiOutput($apiRequest, $result) {
    if (empty($result['values'])) {
      return $result;
    }

    $activeParam = !empty($apiRequest['params']['status_active']) ? $apiRequest['params']['status_active'] : null;

    foreach ($result['values'] as $key => &$value) {
      try {
        $imageUrl = civicrm_api3('Contact', 'getvalue', [
          'return' => "image_URL",
          'id' => $value['contact_id'],
        ]);
      } catch (CiviCRM_API3_Exception $e) {
        throw new \API_Exception(ts("Something wrong with getting img url of contact: " . $e->getMessage()));
      }

      if ($activeParam == 1) {
        try {
          $statusInfo = civicrm_api3('ParticipantStatusType', 'getsingle', [
            'sequential' => 1,
            'return' => ["is_active"],
            'id' => $value['participant_status_id']
          ]);
        } catch (CiviCRM_API3_Exception $e) {
          $statusInfo = [];
        }

        if (!empty($statusInfo) && $statusInfo['is_active'] != 1) {
          unset($result['values'][$key]);
          $result['count'] -= 1;
        }
      }

      $customQrCode = "custom_" . CRM_CiviMobileAPI_Utils_CustomField::getId(CRM_CiviMobileAPI_Install_Entity_CustomGroup::QR_CODES, CRM_CiviMobileAPI_Install_Entity_CustomField::QR_CODE);
      $value['qr_token'] = !empty($value[$customQrCode]) ? $value[$customQrCode] : '';
      $value['image_URL'] = !empty($imageUrl) ? $imageUrl : '';

    }

    $result['values'] = array_values($result['values']);

    return $result;
  }

}
