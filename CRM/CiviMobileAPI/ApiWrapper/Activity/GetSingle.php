<?php

/**
 * @deprecated will be deleted in version 7.0.0
 */
class CRM_CiviMobileAPI_ApiWrapper_Activity_GetSingle implements API_Wrapper {

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
   * - short_description
   * - source_record_id
   * - source_record_type
   *
   * @param $apiRequest
   * @param $result
   *
   * @return array
   */
  public function toApiOutput($apiRequest, $result) {
    if (!empty($result['id'])) {
      if (isset($result['details'])) {
        $result['details'] = preg_replace('/&nbsp;/', ' ', $result['details']);

        if (!isset($result['short_description']) && isset($result['details'])) {
          $result['short_description'] = $result['details'] ? mb_substr (strip_tags(preg_replace('/\s\s+/', ' ', $result['details'])), 0, 200) : '';
        }
      } else {
        $result['short_description'] = '';
      }

      $checkCaseActivity = new CRM_Case_DAO_CaseActivity();
      $checkCaseActivity->activity_id = $result['id'];

      if ($checkCaseActivity->find(TRUE)) {
        $result['source_record_id'] = $checkCaseActivity->case_id;
        $result['source_record_type'] = 'case';

        $case = new CRM_Case_DAO_Case();
        $case->id = $result['source_record_id'];

        if ($case->find(TRUE)) {
          $userId = CRM_Core_Session::singleton()->get('userID');
          $caseRelationships = CRM_Case_BAO_Case::getCaseRoles($userId, $case->id);
          $yourRoles = CRM_CiviMobileAPI_Utils_CaseRole::getYourRoles($caseRelationships, $userId);

          $editAllContacts = CRM_Core_Permission::check('edit all contacts');
          $editAllCase = CRM_Core_Permission::check('access all cases and activities');

          $result['source_record_title'] = $case->subject;

          $caseType = new CRM_Case_DAO_CaseType();
          $caseType->id = $case->case_type_id;

          if ($caseType->find(TRUE)) {
            $result['case_type_is_active'] = $caseType->is_active;
          }

          $result['can_edit'] = !empty($yourRoles) || ($editAllContacts && $editAllCase) ? 1 : 0;
          $result['can_delete'] = !empty($yourRoles) || ($editAllContacts && $editAllCase) ? 1 : 0;
        }
      } elseif (isset($result['source_record_id'])) {
        $result['source_record_type'] = 'event';
      }
    }

    return $result;
  }

}
