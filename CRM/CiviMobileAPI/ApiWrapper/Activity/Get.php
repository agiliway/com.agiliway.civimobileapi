<?php

/**
 * @deprecated will be deleted in version 7.0.0
 */
class CRM_CiviMobileAPI_ApiWrapper_Activity_Get implements API_Wrapper {

  /**
   * Interface for interpreting api input.
   *
   * @param array $apiRequest
   *
   * @return array
   */
  public function fromApiInput($apiRequest) {
    return $apiRequest;
  }

  /**
   * Adds next fields:
   * - can_edit
   * - can_delete
   *
   * @param $apiRequest
   * @param $result
   *
   * @return array
   */
  public function toApiOutput($apiRequest, $result) {
    if (empty($result['values']) || !is_array($result['values'])) {
      return $result;
    }

    $userId = CRM_Core_Session::singleton()->get('userID');
    $editAllContacts = CRM_Core_Permission::check('edit all contacts');
    $editAllCase = CRM_Core_Permission::check('access all cases and activities');

    if (empty($result['values'])) {
      return $result;
    }

    foreach ($result['values'] as &$value) {
      $checkCaseActivity = new CRM_Case_DAO_CaseActivity();
      $checkCaseActivity->activity_id = $value['id'];
      $value['can_edit'] = 0;
      $value['can_delete'] = 0;

      if ($checkCaseActivity->find(TRUE)) {
        $case = new CRM_Case_DAO_Case();
        $case->id = $checkCaseActivity->case_id;

        if ($case->find(TRUE)) {
          $caseRelationships = CRM_Case_BAO_Case::getCaseRoles($userId, $case->id);
          $yourRoles = CRM_CiviMobileAPI_Utils_CaseRole::getYourRoles($caseRelationships, $userId);

          $value['can_edit'] = !empty($yourRoles) || ($editAllContacts && $editAllCase) ? 1 : 0;
          $value['can_delete'] = !empty($yourRoles) || ($editAllContacts && $editAllCase) ? 1 : 0;
        }
      }
    }

    return $result;
  }

}
