<?php

/**
 * @deprecated will be deleted in version 7.0.0
 */
class CRM_CiviMobileAPI_ApiWrapper_Contact implements API_Wrapper {

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
   * Adds current_employer_id field
   *
   * @param $apiRequest
   * @param $result
   *
   * @return array
   */
  public function toApiOutput($apiRequest, $result) {
    if ($apiRequest['action'] == 'getsingle') {
      if (empty($result['current_employer_id']) && !empty($result['contact_id'])) {
        $result['current_employer_id'] = CRM_Core_DAO::getFieldValue('CRM_Contact_DAO_Contact', $result['contact_id'], 'employer_id');
      }
    }

    return $result;
  }

}
