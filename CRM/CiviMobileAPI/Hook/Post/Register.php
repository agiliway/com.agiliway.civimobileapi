<?php

class CRM_CiviMobileAPI_Hook_Post_Register {

  /**
   * @param $op
   * @param $objectName
   * @param $objectId
   * @param $objectRef
   * @return bool
   * @throws CRM_Core_Exception
   */
  public static function run($op, $objectName, $objectId, &$objectRef) {
    if ($objectName == 'Participant' && $op == 'create') {
      $session = CRM_Core_Session::singleton();
      $cmbHash = $session->get('cmbHash');
      if ($cmbHash) {
        if ($tmpData = CRM_CiviMobileAPI_BAO_CivimobileEventPaymentInfo::getByHash($cmbHash)) {
          try {
            $publicKeyFieldId = "custom_" . CRM_CiviMobileAPI_Utils_CustomField::getId(
                CRM_CiviMobileAPI_Install_Entity_CustomGroup::PUBLIC_INFO,
                CRM_CiviMobileAPI_Install_Entity_CustomField::PUBLIC_KEY
              );
            $result = civicrm_api3('Participant', 'create', [
              'id' => $objectId,
              $publicKeyFieldId => $tmpData['public_key']
            ]);
            return TRUE;
          }
          catch (CiviCRM_API3_Exception $e) {
            throw new CRM_Core_Exception(ts('Failed to update participant public_key in database'));
          }
        }
      }
    }
  }

}
