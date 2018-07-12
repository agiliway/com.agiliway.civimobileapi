<?php

class CRM_CiviMobileAPI_ApiWrapper_Contact implements API_Wrapper {

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
    if($apiRequest['action'] == 'getsingle') {
      if(empty($result['current_employer_id']) && isset($result['contact_id'])) {
        try {
          $organization_id = civicrm_api3('Relationship', 'getvalue', [
            'return' => 'contact_id_b',
            'contact_id_a' => $result['contact_id'],
            'relationship_type_id' => 5,
            'is_active' => 1,
            'options' => [
              'limit' => 1,
            ],
          ]);
        } catch (Exception $e) {
          $organization_id = '';
        }
        $result['current_employer_id'] = $organization_id;
      }
    }
    
    return $result;
  }
}
