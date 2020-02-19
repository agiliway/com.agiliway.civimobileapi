<?php

/**
 * @deprecated will be deleted in version 7.0.0
 */
class CRM_CiviMobileAPI_ApiWrapper_Event implements API_Wrapper {

  /**
   * Interface for interpreting api input
   *
   * @param array $apiRequest
   *
   * @return array
   */
  public function fromApiInput($apiRequest) {
    if (is_mobile_request()) {
      $apiRequest['params']['check_permissions'] = 0;
    }

    return $apiRequest;
  }

  /**
   * Adds extra field
   *
   * @param $apiRequest
   * @param $result
   *
   * @return array
   */
  public function toApiOutput($apiRequest, $result) {
    $isQrUsedFieldName = "custom_" . CRM_CiviMobileAPI_Utils_CustomField::getId(CRM_CiviMobileAPI_Install_Entity_CustomGroup::QR_USES, CRM_CiviMobileAPI_Install_Entity_CustomField::IS_QR_USED);
    $isQrUsedAlias = 'is_event_use_qr_code';

    if ($apiRequest['action'] == 'getsingle') {
      $result['url'] = CRM_Utils_System::url('civicrm/event/info', 'id=' . $result['id'], true);
      if (!empty($result['creator_id'])) {
        $result[$isQrUsedAlias] = (isset($result[$isQrUsedFieldName])) ? $result[$isQrUsedFieldName] : NULL;
        $result['is_user_' . CRM_CiviMobileAPI_Utils_Permission::CAN_CHECK_IN_ON_EVENT] = (int) CRM_Core_Permission::check(CRM_CiviMobileAPI_Utils_Permission::CAN_CHECK_IN_ON_EVENT);
        $result['is_user_can_manage_participant'] = (int) CRM_CiviMobileAPI_Utils_Permission::isUserCanManageParticipant($result['creator_id']);
      }

      if (isset($result['currency'])) {
        if (!empty($result['currency'])) {
          $result['currency_symbol'] = CRM_CiviMobileAPI_Utils_Currency::getSymbolByName($result['currency']);
        } else {
          $result['currency_symbol'] = '';
        }
      } else {
        $result['currency'] = '';
        $result['currency_symbol'] = '';
      }
    }

    if ($apiRequest['action'] == 'get' && !empty($result['values'])) {
      foreach ($result['values'] as $key => $event) {
        if (isset($event['currency'])) {
          if (!empty($event['currency'])) {
            $result['values'][$key]['currency_symbol'] = CRM_CiviMobileAPI_Utils_Currency::getSymbolByName($event['currency']);
          } else {
            $result['values'][$key]['currency_symbol'] = '';
          }
        } else {
          $result['values'][$key]['currency'] = '';
          $result['values'][$key]['currency_symbol'] = '';
        }
        if (!empty($event['creator_id'])) {
          $result['values'][$key]['is_user_' . CRM_CiviMobileAPI_Utils_Permission::CAN_CHECK_IN_ON_EVENT] = (int) CRM_Core_Permission::check(CRM_CiviMobileAPI_Utils_Permission::CAN_CHECK_IN_ON_EVENT);
          $result['values'][$key][$isQrUsedAlias] = (isset($event[$isQrUsedFieldName])) ? $event[$isQrUsedFieldName] : NULL;
          $result['values'][$key]['is_user_can_manage_participant'] = (int) CRM_CiviMobileAPI_Utils_Permission::isUserCanManageParticipant($event['creator_id']);
        }
      }
    }

    return $result;
  }

}
